<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Items\City;
use App\Models\Items\Country;
use App\Models\Items\ItemDetail;
use App\Models\Users\UserPreference;
use App\Models\Users\Watchlist;
use Illuminate\Http\Request;

use App\Models\Items\Item;
use App\Models\Users\Users;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Validator;
use Facades\App\Repositories\CurrencyRatesRepository;

class ApiUserWatchedItemsController extends ApiBaseController
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_currency' => 'string',
        ]);

        if ($validator->fails()) {
            $message = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $message[$key] = $value[0];
            }
            $message = implode(", ", $message);
            return $this->apiErrorResponse(false, $message, self::HTTP_STATUS_INVALID_INPUT, 'invalidFields');
        }

        $user_currency_code = $request->user_currency ? $request->user_currency : 'AUD';
        $user_id = $request->user_id;
        $user = Users::find($user_id);
        $countries_collection = collect();

        if (!$user) {
            return $this->apiErrorResponse(false, 'User not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
        }

        $user_preference = UserPreference::where('user_id', $user_id)->first();

        $items = collect($user->watchlist()->with('tags')->get());
        $items_collection
                = $items->map(function ($item) use ($user_preference, $user_currency_code, $countries_collection) {
                    $city_currency_code = $this->getCityCurrencyCode($item['city_id']);

                    $rate = 1;


                    $item['ave_local_price'] = $this->itemAveragePrice($item->item_id);

                    if ($item['ave_local_price'] > 0) {
                        try {
                            $currencyService = new CurrencyService();
                            $rate = $currencyService->convertRates($user_currency_code, $city_currency_code);
                            $item['converted_price'] = (double) strval(round($item['ave_local_price'] / $rate, 2));
                        } catch (\Exception $e) {
                            $item['converted_price'] = 0.00;
                        }
                    }
                    $item['local_currency'] = $item->getCityCurrency($item->city_id)->symbol_native;
                    $item['user_currency_code'] = $user_currency_code;
                    $item['user_currency_symbol'] = $this->getUserCurrencySymbol($user_currency_code);
                    $item['country_id'] = $this->getCountryByCity($item['city_id']);
                    $countries_collection->push($this->getItemCountry($item->item_id));
                    unset($item['pivot']);

                    $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
                    $item->unsetRelation('image');
                    
                    return $item;
                });

        $countries_collection = $countries_collection->unique('country_id');
        $countries_collection = $countries_collection->flatten();

        $countries_collection->map(function ($item) use ($items_collection) {
            $item['items'] = $this->getItemsByCountry($items_collection, $item['country_id']);
            return $item;
        });

        $countries_collection->each(fn ($country) => $country->setAppends(['flag_url', 'background_url']));

        $data = [
            'user' => $user,
            'countries' => $countries_collection
        ];

        return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }


    /**
     * @OA\Post(
     *      path="/user/{user_id}/watchlist",
     *      tags={"Watchlist"},
     *      summary="Save an item to user watchlist",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *         ),
     *     ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="item_id",
     *                      description="Item ID",
     *                      type="integer",
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Saved Watchlist")
     *)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|integer'
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
            if (!$user) {
                return $this->apiErrorResponse(false, 'User not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $item = Item::find($request->item_id);
            if (!$item) {
                return $this->apiErrorResponse(false, 'Item not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $watch_item = WatchList::updateOrCreate(
                ['user_id' => $request->user_id, 'item_id' => $request->item_id]
            );

            return $this->apiSuccessResponse($watch_item, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }


    /**
     * @OA\Delete(
     *      path="/user/{user_id}/watchlist",
     *      tags={"Watchlist"},
     *      summary="Delete saved country",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *         ),
     *     ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="item_id",
     *                      description="Item ID",
     *                      type="integer",
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Saved Watchlist")
     *)
     */
    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                $message = [];
                foreach ($validator->errors()->toArray() as $key => $value) {
                    $message[$key] = $value[0];
                }
                $message = implode(", ", $message);
                return $this->apiErrorResponse(false, $message, self::
                                                HTTP_STATUS_INVALID_INPUT, 'invalidFields');
            }

            $user = Users::find($request->user_id);
            if (!$user) {
                return $this->apiErrorResponse(false, 'User not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $item = Item::find($request->item_id);
            if (!$item) {
                return $this->apiErrorResponse(false, 'Item not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $watch_item = WatchList::where('user_id', $request->user_id)
                                ->where('item_id', $request->item_id)->delete();

            return $this->apiSuccessResponse([], true, 'Success', self::
                                                HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    private function itemAveragePrice($item_id)
    {
        /** @var \App\Models\Items\Item */
        $item = Item::where('item_id', $item_id)->first();

        return $item->getAveragePrice();
    }

    private function getCityCurrencyCode($city_id)
    {
        return City::join('countries', 'cities.country_id', '=', 'countries.country_id')
            ->where('cities.city_id', $city_id)
            ->pluck('countries.currency_code')
            ->first();
    }

    private function getCountryByCity($city_id)
    {
        return City::where('city_id', $city_id)->pluck('country_id')->first();
    }

    private function getItemCountry($item_id)
    {
        $city_id = Item::where('item_id', $item_id)->pluck('city_id');
        $country_id = City::where('city_id', $city_id)->pluck('country_id');
        $country = Country::where('country_id', $country_id)->first();

        return $country;
    }

    private function getItemsByCountry($items, $country_id)
    {
        return $items->filter(function ($value, $key) use ($country_id) {
            return $value['country_id'] == $country_id;
        })->flatten();
    }


    private function getUserCurrencySymbol($currency_code)
    {
        $currency_symbol = Country::where('currency_code', $currency_code)->pluck('symbol_native');
        if (is_null($currency_symbol)) {
            $currency_symbol = '$';
        }
        return $currency_symbol[0];
    }
}
