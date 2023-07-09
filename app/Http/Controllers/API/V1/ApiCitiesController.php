<?php

namespace App\Http\Controllers\API\V1;

use App\Events\Notifications\NotifyRecommendItemPrice;
use App\Models\Device;
use App\Models\Items\Country;
use App\Models\Items\City;
use App\Models\Items\Item;
use App\Models\Items\ItemTag;
use App\Models\Items\ItemDetail;
use App\Models\Items\Category;
use App\Models\Items\Location;
use App\Models\Items\Recommendation;
use App\Models\Users\Users;
use App\Models\Users\Watchlist;
use Facades\App\Repositories\CurrencyRatesRepository;

use Illuminate\Http\Request;
use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleLocationService;

use JWTAuth;
use DB;

class ApiCitiesController extends ApiBaseController
{
    /**
     * @OA\Get(
     *      path="/{country_id}/cities",
     *      tags={"Cities"},
     *      operationId="getCities",
     *      summary="Get cities by country",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="country_id",
     *          description="Country ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Cities")
     * )
     */
    public function getCities(Request $request, $country_id)
    {
        try {
            $cities = City::select('city_id', 'city_name', 'city_code')
                        ->where('country_id', $country_id)
                        ->orderBy('city_name', 'ASC')
                        ->get();

            return $this->apiSuccessResponse(compact('cities'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="cities/{city_id}/items",
     *      tags={"Cities"},
     *      summary="Get items per city",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="city_id",
     *          description="City ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Items")
     * )
     */
    public function getItems(Request $request, $city_id)
    {
        try {
            $request->merge([ 'limit' => 0, 'return_total' => true ,
                              'order_by' => 'created_at' ,
                              'order_direction' => 'DESC',
                              'noLimit' => true]);
            $categories = Category::ordered()->get()->each(fn ($category) => $category->makeHidden('order_column'));
            $items = new Item();
            $user = Users::find($request->user_id);
            $is_allowed = false;
            if ($user) {
                $is_allowed = $user->is_allowed;
            }
            $current_page = $request->page ? $request->page : 1;
            $next_page = $current_page + 1;
            $currency = $request->currency ? $request->currency : null;
            $country_id = City::find($city_id)->country_id;

            if ($request->currency) {
                $user_currency_code = $request->currency;
                $user_currency_symbol = $items->getCurrencySymbol($request->currency)->symbol_native;
                $currency_string = '?currency=' . $currency;
                $page_string = '&page=' . $next_page;
            } else {
                $user_currency_code = '';
                $user_currency_symbol = '';
                $currency_string = '';
                $page_string = '?page=' . $next_page;
            }


            $data  = [
                'categories' => $categories,
                'items_local_currency' =>  $items->getCityCurrency($city_id)->symbol_native,
                'items_local_currency_code' => Country::find($country_id)->currency_code,
                'user_currency_code' => $user_currency_code,
                'user_currency_symbol'=> $user_currency_symbol,
                'items' => $items->getCollection($request, $city_id),
                'next_page_url' => $request->url() . $currency_string . $page_string,
                'is_allowed' => $is_allowed,
                'has_next_page' => $items->hasNextPage(),
            ];

            return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="cities/{city_id}/items/{item_id}",
     *      tags={"Cities"},
     *      summary="Get items' details",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="city_id",
     *          description="City ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="item_id",
     *          description="Item ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Item Details")
     * )
     */
    public function getItemDetails(Request $request, $city_id, $item_id)
    {
        try {
            /** @var \App\Models\Items\Item */
            $item = Item::whereHas('details', function ($query) {
                $query->where(function ($query) {
                    $query->whereDoesntHave('recommendation')
                                ->orWhereHas('recommendation.user', function ($query) {
                                    $query->whereNull('disabled_at');
                                });
                })->where(function ($query) {
                    $query->whereDoesntHave('verifiedBusinessItem')
                                ->orWhereHas('verifiedBusinessItem.location', function ($query) {
                                    $query->where('is_verified', true);
                                });
                });
            })->find($request->item_id);
            if (!$item) {
                return $this->apiErrorResponse(false, 'Item not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $user = Users::find($request->user_id);
            $is_allowed = false;
            if ($user) {
                $is_allowed = $user->is_allowed;
            }

            $item_details = ItemDetail::join('locations', 'locations.location_id', 'item_details.location_id')
                        ->where(function ($query) {
                            // Where doesn't have - Possible due to an admin created the detail.
                            $query->whereDoesntHave('recommendation')->orWhereHas('recommendation.user', function ($query) {
                                $query->whereNull('disabled_at');
                            });
                        })
                        ->where(function ($query) {
                            $query->whereDoesntHave('verifiedBusinessItem')
                                ->orWhereHas('verifiedBusinessItem.location', function ($query) {
                                    $query->where('is_verified', true);
                                });
                        })
                        ->where('item_id', $item_id)
                        ->orderBy('price', 'ASC')
                        ->get()
                        ->map(function ($item) {
                            $item->is_verified = (bool)$item->is_verified;

                            return $item;
                        });

            $city_name = City::find($city_id)->city_name;

            $currency = Country::whereHas('cities', function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })->first();

            $local_price_range_from = $item->getFirstQuartilePrice();
            $local_price_range_to = $item->getThirdQuartilePrice();

            if ($request->currency) {
                $user_currency_code = $request->currency;
                $user_currency = Country::where('currency_code', $request->currency)->where('is_primary_currency', true)->first();
                $user_currency_symbol = $user_currency->symbol_native;
                $user_currency_name = $user_currency->currency_name;
                $user_currency_flag = $user_currency->flag_url;

                try {
                    $currencyService = new CurrencyService();

                    $rate = $currencyService->convertRates($currency->currency_code, $user_currency_code);
                    $user_price_from = strval(round($rate * $local_price_range_from, 2));
                    $user_price_to = strval(round($rate * $local_price_range_to, 2));
                } catch (\Exception $e) {
                    $user_price_from = '';
                    $user_price_to = '';
                }
            } else {
                $user_currency_code = '';
                $user_currency_symbol = '';
                $user_currency_name = '';
                $user_currency_flag = '';
                $user_price_from = '';
                $user_price_to = '';
            }

            $watch_item = false;
            if ($request->user_id) {
                $user = Users::find($request->user_id);

                if (!$user) {
                    return $this->apiErrorResponse(false, 'User not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
                }

                $watch_item = Watchlist::where('item_id', $item_id)
                                         ->where('user_id', $request->user_id)
                                         ->exists();
            }

            $data = [
                'is_allowed' =>$is_allowed,
                'is_watched' =>$watch_item,
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'city_name' => $city_name,
                'local_currency_code' =>  $currency->currency_code,
                'local_currency_symbol' =>  $currency->symbol_native,
                'local_currency_name' =>  $currency->currency_name,
                'local_currency_flag' => $currency->flag_url,
                'local_price_range_from' => $local_price_range_from,
                'local_price_range_to' => $local_price_range_to,
                'user_currency_code' => $user_currency_code,
                'user_currency_symbol' => $user_currency_symbol,
                'user_currency_name' => $user_currency_name,
                'user_currency_flag' => $user_currency_flag,
                'user_price_from' => $user_price_from,
                'user_price_to' => $user_price_to,
                'locations' => $item_details,
            ];

            return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Put(
     *      path="cities/{city_id}/items/{item_id}",
     *      tags={"Cities"},
     *      summary="Update items' details",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="city_id",
     *          description="City ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="item_id",
     *          description="Item ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_id",
     *                      description="User ID",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="recommended_price",
     *                      description="Recommended Price",
     *                      type="double",
     *                      example="9.99"
     *                  ),
     *                  @OA\Property(
     *                      property="location_id",
     *                      description="Location ID",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Item Details")
     * )
     */
    public function updateItemDetails(Request $request, $city_id, int $item_id)
    {
        $this->authenticate($request);

        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'recommended_price' => 'required|numeric|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
                'place_id' => 'required'
            ]);

            if ($validator->fails()) {
                $message = [];
                foreach ($validator->errors()->toArray() as $key => $value) {
                    $message[$key] = $value[0];
                }
                $message = implode(", ", $message);
                return $this->apiErrorResponse(false, $message, self::HTTP_STATUS_INVALID_INPUT, 'invalidFields');
            }

            $user = Users::find($request->user_id);
            $logged_user  = JWTAuth::toUser();
            if (!$user) {
                return $this->apiErrorResponse(false, 'User not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            DB::beginTransaction();

            $location = Location::where('place_id', $request->place_id)->first();
            if (!$location) {
                $location = new Location();
                $location = $location->saveNewLocation($request->place_id);
            }

            $item = Item::where('item_id', $item_id)->first();
            if ($item) {
                $recommendation = new Recommendation();
                $recommendation->user_id = (int) $request->user_id;
                $recommendation->item_id = $item_id;
                $recommendation->location_id = $location->location_id;
                $recommendation->recommended_price = (float) $request->recommended_price;
                $recommendation->save();

                $details = new ItemDetail();
                $details->item_id = $item_id;
                $details->location_id = $location->location_id;
                $details->price = $request->recommended_price;
                $details->recommendation_id = $recommendation->recommendation_id;
                $details->save();

                DB::commit();

                return $this->apiSuccessResponse($recommendation, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
            } else {
                DB::rollBack();
                return $this->apiErrorResponse(false, 'Item not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="cities/{city_id}/locations/keyword",
     *      tags={"Cities"},
     *      summary="Get the locations of the given city and given keyword",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="city_id",
     *          description="City ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="City Locations")
     * )
     */
    public function getCityLocations(Request $request, $city_id, $keyword)
    {
        try {
            $city = City::find($city_id);
            $country_code = Country::find($city->country_id)->country_alpha_code_2;

            $locations = new GoogleLocationService();
            $locations = $locations->searchLocation($keyword, $country_code, $city->city_name);

            return $this->apiSuccessResponse($locations, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
}
