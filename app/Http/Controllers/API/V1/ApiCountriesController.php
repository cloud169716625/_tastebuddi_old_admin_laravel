<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Items\Country;
use App\Models\Items\City;
use App\Models\Items\Item;
use App\Models\Items\ItemTag;
use App\Models\Items\ItemDetail;
use App\Models\Items\Category;
use App\Models\Items\Location;
use App\Models\Items\Recommendation;
use App\Models\Users\Users;
use App\Models\Users\UserSavedCountry;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use JWTAuth;
use DB;
use Ranium\Fixerio\Client;
use Illuminate\Support\Facades\Validator;
use Facades\App\Repositories\CurrencyRatesRepository;
use App\Services\GoogleLocationService;
use Illuminate\Support\Facades\Cache;

class ApiCountriesController extends ApiBaseController
{
    /**
     * @OA\Get(
     *      path="/countries",
     *      tags={"Countries"},
     *      summary="Get countries",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=false,
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
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Countries")
     * )
     */
    public function getCountries(Request $request)
    {
        try {
             $countries = Country::
                            select(
                                'countries.country_id',
                                'countries.country_name',
                                'countries.currency_code',
                                DB::raw('COUNT(item_details.price) as local_prices')
                            )
                        ->join('cities', 'cities.country_id', '=', 'countries.country_id')
                        ->join('items', 'items.city_id', '=', 'cities.city_id')
                        ->join('item_details', 'items.item_id', '=', 'item_details.item_id')
                        ->groupBy('countries.country_name')
                        ->orderBy('local_prices', 'DESC')
                        ->get();

            if ($request->user_id) {
                $user = Users::find($request->user_id);

                if (!$user) {
                    return $this->apiErrorResponse(false, 'User not found', self
                                                    ::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
                }

                $u_saved_country = new UserSavedCountry;
                $u_saved_countries = $u_saved_country->getUserCountries($user);

                $countries->map(function ($i, $index) use ($u_saved_countries) {
                    $i['is_saved'] = $u_saved_countries->contains('country_id', $i->country_id);
                    return $i;
                });
            }

            $countries->each(function ($country) {
                $country->setAppends(['background_url']);
            });

            return $this->apiSuccessResponse($countries, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/currencies",
     *      tags={"Currencies"},
     *      summary="Get currencies",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Currencies")
     * )
     */
    public function getCurrencies(Request $request)
    {
        try {
            $currencies = Country::query()
                            ->whereNotNull('currency_code')
                            ->where('is_primary_currency', true)
                            ->with('flag')
                            ->orderBy('currency_name')
                            ->get()
                            ->each
                            ->setAppends(['flag_url'])
                            ->map(function (Country $country) {
                                return [
                                    'currency_code'     => $country->currency_code,
                                    'currency_name'     => $country->currency_name,
                                    'currency_symbol'   => $country->symbol_native,
                                    'flag_url'          => $country->flag_url
                                ];
                            })
                            ->toArray();

            return $this->apiSuccessResponse(compact('currencies'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/currencies/rates",
     *      tags={"Currencies"},
     *      summary="Get Currency Rates",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="base",
     *                      description="Base",
     *                      type="integer",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Rates")
     * )
     */
    public function getRates(Request $request)
    {
        try {
            $accessKey = config('app.fixer')['ACCESS_KEY'];
            $secure  = config('app.fixer')['ACCOUNT_SECURE'];
            $config = [];

            $next_base = null;

            //$fixerio = Client::create($accessKey, $secure, $config);

            $currencies = Country
                            ::select('currency_code')
                            ->whereNotNull('currency_code')
                            ->groupBy('currency_code')->get();

            $base = $request->base ? $request->base : null;

            $codes = [];
            foreach ($currencies as $code) {
                array_push($codes, $code->currency_code);
            }

            $i = 0;
            while ($i < count($codes)) {
                if ($base == null) {
                    $base = $codes[$i];
                    $next_base = $codes[$i+1];
                    break;
                } elseif ($codes[$i] == $base) {
                    $next_base = $codes[$i+1];
                    break;
                } elseif ($base == $codes[count($codes) - 1]) {
                    $next_base = null;
                    break;
                }
                $i++;
            }

            $currencyService = new CurrencyService();

            $rates = $currencyService->getCachedRates($base);

            $rates = collect($rates)->map(function ($rate, $code) {
                return [
                    'currency_code' => $code,
                    'conversion_value' => $rate
                ];
            })->values();

            $next_string = $next_base != null ? $request->url() . '?base=' . $next_base : '';

            $data = [
                'base' => $base,
                'rates' => $rates,
                'next_page_url' =>  $next_string
            ];

            return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/search/country/{country_id}/{keyword=null}",
     *      tags={"Search"},
     *      summary="Country search in an empty state and with a given keyword",
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
     *      @OA\Parameter(
     *          name="keyword",
     *          description="Search Keyword",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Search Results")
     * )
     */
    public function search(Request $request, $country_id, $keyword = null)
    {
        try {
            $items = new Item;
            $recommendations = new Recommendation;
            $user = Users::find($request->user_id);
            $is_allowed = false;
            if ($user) {
                $is_allowed = $user->is_allowed;
            }
            $current_page = $request->page ? $request->page : 1;
            $next_page = $current_page + 1;
            $currency = $request->currency ? $request->currency : null;

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

            if (is_null($keyword)) {
                $data  = [
                    'items_local_currency' =>  Country::find($country_id)->symbol_native,
                    'items_local_currency_code' =>  Country::find($country_id)->currency_code,
                    'user_currency_code' => $user_currency_code,
                    'user_currency_symbol'=> $user_currency_symbol,
                    'is_allowed' => $is_allowed,
                    'recommendations' => $recommendations->getRecommendationsByCountry($country_id),
                    'items' => $items->getItemsByCountry($request, $country_id),
                ];

                return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
            } else {
                $request->merge([ 'limit' => 10, 'return_total' => true,
                                  'order_by' => 'created_at',
                                  'order_direction' => 'DESC'  ]);

                $data  = [
                    'items_local_currency' =>  Country::find($country_id)->symbol_native,
                    'items_local_currency_code' =>  Country::find($country_id)->currency_code,
                    'user_currency_code' => $user_currency_code,
                    'user_currency_symbol'=> $user_currency_symbol,
                    'is_allowed' => $is_allowed,
                    'items' => $items->searchItemsByCountry($request, $country_id, $keyword),
                    'next_page_url' => $request->url() . $currency_string . $page_string
                ];

                return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
            }
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/country/{country_id}/locations/{keyword}",
     *      tags={"Countries"},
     *      summary="Get the locations of the given country and given keyword",
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
     *      @OA\Parameter(
     *          name="keyword",
     *          description="Search Keyword",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Search Results")
     * )
     */
    public function getCountryLocations(Request $request, $country_id, $keyword)
    {
        try {
            $country_code = Country::find($country_id)->country_alpha_code_2;

            $locations = new GoogleLocationService();
            $locations = $locations->searchLocation($keyword, $country_code);


            return $this->apiSuccessResponse($locations, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }


    /**
     * @OA\Post(
     *      path="/countries/saved",
     *      tags={"Countries"},
     *      summary="Get saved countries",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="country_id",
     *                      description="Country ID",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="country_name",
     *                      description="Country Name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="capital",
     *                      description="Capital",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="full_name",
     *                      description="Full Name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="country_numeric_code",
     *                      description="Country Numeric Code",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="country_alpha_code_2",
     *                      description="Country Alpha Code 2",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="country_alpha_code_3",
     *                      description="Country Alpha Code 3",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="tl_domain",
     *                      description="TL Domain",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="currency_code",
     *                      description="Currency Code",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="currency_name",
     *                      description="Currency Name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="symbol_native",
     *                      description="Currency Native Symbol",
     *                      type="string",
     *                  ),
     *
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Saved Countries")
     * )
     */
    public function saveCountry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_id' => 'required|integer'
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

            $country = Country::find($request->country_id);
            if (!$country) {
                return $this->apiErrorResponse(false, 'Country not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $u_saved_country = UserSavedCountry::updateOrCreate(
                ['user_id' => $request->user_id, 'country_id' => $request->country_id]
            );

            $u_saved_country = new UserSavedCountry;
            $countries = $u_saved_country->getUserCountries($user);

            return $this->apiSuccessResponse($countries, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }


    /**
     * @OA\Get(
     *      path="/countries/user/{user_id}",
     *      tags={"Countries"},
     *      summary="Get saved countries",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Saved Countries")
     * )
     */
    public function getSavedCountries(Request $request)
    {
        try {
            $user = Users::find($request->user_id);
            if (!$user) {
                return $this->apiErrorResponse(false, 'User not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $u_saved_country = new UserSavedCountry;
            $countries = $u_saved_country->getUserCountries($user);

            return $this->apiSuccessResponse($countries, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }


    /**
     * @OA\Delete(
     *      path="/countries/delete",
     *      tags={"Countries"},
     *      summary="Delete saved country",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="country_id",
     *                      description="Country ID",
     *                      type="integer",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Saved Countries")
     *      )
     * )
     */
    public function deleteSavedCountry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_id' => 'required|integer'
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

            $country = Country::find($request->country_id);
            if (!$country) {
                return $this->apiErrorResponse(false, 'Country not found', self::
                                                HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $u_saved_country = UserSavedCountry::
                                  where('user_id', $request->user_id)
                                ->where('country_id', $request->country_id)->delete();

            $u_saved_country = new UserSavedCountry;
            $countries = $u_saved_country->getUserCountries($user);

            return $this->apiSuccessResponse($countries, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
}
