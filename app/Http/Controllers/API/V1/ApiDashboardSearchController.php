<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Items\Country;
use App\Models\Items\City;
use App\Models\Items\Item;
use App\Models\Items\ItemDetail;
use App\Models\Users\Users;
use App\Models\Users\UserSavedCountry;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class ApiDashboardSearchController extends ApiBaseController
{
    /**
     * @OA\Get(
     *      path="/search/dashboard/{keyword=null}",
     *      tags={"Search"},
     *      summary="Dashboard search in an empty state and with a given keyword",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="keyword",
     *          description="Search Keyword",
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
     *      @OA\Response(response=200, description="Search Results")
     * )
     */
    public function search(Request $request, $keyword = null)
    {
        try {
            $item_places_limit = 5;
            $item_prices_limit = 5;
            $places = (Country::select('country_id', 'country_name', 'currency_code')->paginate($item_places_limit));
            $prices = (
                Item::
                    select(
                        'items.item_id',
                        'items.item_name',
                        'countries.symbol_native as currency',
                        'countries.country_id',
                        'countries.currency_code',
                        'items.city_id'
                    )
                    ->whereHas('details', function ($query) {
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
                    })
                    ->leftJoin('cities', 'items.city_id', '=', 'cities.city_id')
                    ->leftJoin('countries', 'cities.country_id', '=', 'countries.country_id')
                    ->with('tags')
                    ->paginate($item_prices_limit));

            $places->map(function ($place) {
                $place['label'] = 'Country';
                return $place;
            });

            $prices->map(function ($price) {
                /** @var \App\Models\Items\Item */
                $item = Item::where('item_id', $price->item_id)->first();

                $local_price_range_from = $item->getFirstQuartilePrice();
                $local_price_range_to = $item->getThirdQuartilePrice();

                $price['local_price_range_from'] = $local_price_range_from ? $local_price_range_from : 0;
                $price['local_price_range_to'] = $local_price_range_to ? $local_price_range_to : 0;

                return $price;
            });


            if (is_null($keyword)) {
//                $places = $places->random($item_places_limit);
//                $prices = $prices->random($item_prices_limit);

            } else {
                $places = Country::select('country_id', 'country_name', 'currency_code')
                    ->where('country_name', 'like', '%'.$keyword.'%')
                    ->orWhere('capital', 'like', '%'.$keyword.'%')
                    ->paginate($item_places_limit);

                $places->map(function ($place) {
                    $place['label'] = 'Country';
                    return $place;
                });

                $prices = Item::
                            select(
                                'items.item_id',
                                'items.item_name',
                                'countries.symbol_native as currency',
                                'countries.country_id',
                                'countries.currency_code',
                                'items.city_id'
                            )
                        ->whereHas('details', function ($query) {
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
                        })
                        ->leftJoin('cities', 'items.city_id', '=', 'cities.city_id')
                        ->leftJoin('countries', 'cities.country_id', '=', 'countries.country_id')
                        ->with('tags', 'city')
                        ->where(function ($query) use ($keyword) {
                            $query->where('item_name', 'like', '%'.$keyword.'%')
                                ->orWhere('city_name', 'LIKE', "%{$keyword}%");
                        })
                        ->paginate($item_prices_limit);
                $prices->map(function ($price) {
                    $local_price_range_from = $price->getFirstQuartilePrice();
                    $local_price_range_to = $price->getThirdQuartilePrice();

                    $price['local_price_range_from'] = $local_price_range_from ? $local_price_range_from : 0;
                    $price['local_price_range_to'] = $local_price_range_to ? $local_price_range_to : 0;

                    return $price;
                });
            }

            if ($request->user_id) {
                $user = Users::find($request->user_id);

                if (!$user) {
                    return $this->apiErrorResponse(false, 'User not found', self
                                                    ::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
                }

                $u_saved_country = new UserSavedCountry;
                $u_saved_countries = $u_saved_country->getUserCountries($user);

                $places->map(function ($i, $index) use ($u_saved_countries) {
                    $i['is_saved'] = $u_saved_countries->contains('country_id', $i->country_id);
                    return $i;
                });
            }

            $places->each(function ($place) {
                $place->setAppends(['background_url']);
            });

            $prices->transform(function ($item) use ($request) {
                $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;

                $item->background_url = ($item->city->country->background)
                                    ? $item->city->country->background->getFullUrl()
                                    : null;

                $item->flag_url = ($item->city->country->flag)
                                    ? $item->city->country->flag->getFullUrl()
                                    : null;

                $item->unsetRelation('image');

                if (request()->has('currency')) {
                    
                    $user_currency_code = $request->input('currency');
                    $item_currency_code = Country::whereHas('cities', fn ($query) => $query->where('city_id', $item->city_id))
                                            ->first()
                                            ->currency_code;
        
                    try {
                        $currencyService = new CurrencyService();
                        $rate = $currencyService->convertRates($user_currency_code, $item_currency_code);
                    } catch (\Exception $e) {
                        $rate = 0;
                    }

                    $item->converted_currency_symbol = Country::where('currency_code', $request->input('currency'))
                                                        ->first()
                                                        ->symbol_native;
                                                
                    $item->converted_price_range_from = $rate > 0
                                                        ? bcdiv($item->local_price_range_from, $rate, 2)
                                                        : "0";

                    $item->converted_price_range_to = $rate > 0
                                                    ? bcdiv($item->local_price_range_to, $rate, 2)
                                                    : "0";
                }

                return $item;
            });

            $data  = [
                'places' => $places,
                'prices' => $prices
            ];

            return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
}
