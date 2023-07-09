<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Device;
use App\Models\Items\Country;
use App\Models\Items\Item;
use App\Models\Items\Location;
use App\Models\Items\Category;
use App\Models\Items\Recommendation;
use App\Models\Users\Users;
use App\Models\Users\Watchlist;
use Facades\App\Repositories\CurrencyRatesRepository;

use Illuminate\Http\Request;
use JWTAuth;
use DB;
use Illuminate\Support\Facades\Validator;

use App\Events\Notifications\NotifyRecommendItemPrice;
use App\Http\Requests\ReportRequest;
use App\Repositories\Item\Contracts\ItemInterface;
use App\Services\CurrencyService;
use Illuminate\Http\Response;

class ApiItemsController extends ApiBaseController
{
    private $item;

    /**
     * ApiItemsController constructor.
     *
     * @param ItemInterface $item
     */
    public function __construct(ItemInterface $item)
    {
        $this->item = $item;
    }

    /**
     * @OA\Post(
     *      path="/items",
     *      tags={"Items"},
     *      summary="Submit a New Item",
     *      security={{"BearerAuth":{}}},
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
     *                      property="place_id",
     *                      description="Place ID",
     *                      type="string",
     *                      example="ChIJKwdvi4-Z4jAR-GnYG7XAGII"
     *                  ),
     *                  @OA\Property(
     *                      property="city_id",
     *                      description="City ID",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="category_id",
     *                      description="Category ID",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="item_name",
     *                      description="Item Name",
     *                      type="string",
     *                      example="Eiffel Tower"
     *                  ),
     *                  @OA\Property(
     *                      property="recommended_price",
     *                      description="Recommended Price",
     *                      type="double",
     *                      example="99.99"
     *                  ),
     *                  @OA\Property(
     *                      property="image_url",
     *                      description="Item's Image URL",
     *                      type="string",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Items")
     * )
     */
    public function submitNewItem(Request $request)
    {
        $this->authenticate($request);

        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'item_name' => 'required',
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

            $user_id = (int) $request->user_id;

            $logged_user  = JWTAuth::toUser();

            $user = Users::find($user_id);

            if (!$user) {
                return $this->apiErrorResponse(false, 'User not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            DB::beginTransaction();

            $recommendation = new Recommendation();
            $recommendation = $recommendation->saveNewItemAsRecommendation($request);

            DB::commit();

            return $this->apiSuccessResponse($recommendation, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }


    /**
     * @OA\Get(
     *      path="/items/user/{user_id}",
     *      tags={"Items"},
     *      summary="Get Followed Items",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          description="User ID",
     *          in="path",
     *          name="user_id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="User's followed items")
     * )
     */
    public function getContributedItems(Request $request)
    {
        try {
            $user = Users::find($request->user_id);

            if (!$user) {
                return $this->apiErrorResponse(false, 'User not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
            }

            $request->merge([ 'limit' => 10,'return_total' => true ,
                              'order_by' => 'created_at' , 'order_direction' => 'DESC'  ]);

            $recommendation = new Recommendation();
            $items = $recommendation->getUserRecommendations($request);

            $current_page = $request->page ? $request->page : 1;
            $next_page = $current_page + 1;
            $currency = $request->currency ? $request->currency : null;

            if ($request->country_id) {
                $country = Country::find($request->country_id);
                if (!$country) {
                    return $this->apiErrorResponse(false, 'Country not found', self::HTTP_STATUS_NOT_FOUND, 'ResourceNotFound');
                } else {
                    $country = $country->country_id;
                }
            } else {
                $country = null;
            }


            if ($request->currency && $request->country_id) {
                $country_string = '?country_id=' . $country;
                $currency_string = '&currency=' . $currency;
                $page_string = '&page=' . $next_page;
            } elseif (!$request->currency && $request->country_id) {
                $country_string = '?country_id=' . $country;
                $currency_string = '';
                $page_string = '&page=' . $next_page;
            } elseif ($request->currency && !$request->country_id) {
                $country_string = '';
                $currency_string = '?currency=' . $currency;
                $page_string = '&page=' . $next_page;
            } else {
                $country_string = '';
                $currency_string = '';
                $page_string = '?page=' . $next_page;
            }

            $items->map(function ($i, $index) use ($request) {
                $item = new Item();
                if ($request->currency) {
                    $user_currency_code = $request->currency;
                    $user_currency_symbol = $item->getCurrencySymbol($request->currency)->symbol_native;
                    $item_currency_code =  $item->getCityCurrency($i->city_id)->currency_code;

                    try {
                        $currencyService = new CurrencyService();
                        $rate = $currencyService->convertRates($user_currency_code, $item_currency_code);
                        $i['converted_price'] = strval(round($i->recommended_price / $rate, 2));
                    } catch (\Exception $e) {
                        $i['converted_price'] = '';
                    }
                } else {
                    $user_currency_code = '';
                    $user_currency_symbol = '';
                    $item_currency_code =  '';
                    $i['converted_price'] = '0.00';
                }

                $i['latitude'] = doubleval($i->latitude);
                $i['longitude'] = doubleval($i->longitude);
                $i['local_currency'] = $item->getCityCurrency($i->city_id)->symbol_native;
                $i['user_currency_code'] = $user_currency_code;
                $i['user_currency_symbol'] = $user_currency_symbol;
                $i['watchers'] = Recommendation::where('item_id', $i->item_id)->count();

                return $i;
            });

            $data = [
                'items' => $items,
                'next_page_url' => $request->url() . $country_string . $currency_string . $page_string
            ];

            return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
    * @OA\Get(
    *      path="/items/{item_id}",
    *      tags={"Items"},
    *      summary="Get Item",
    *      security={{"BearerAuth":{}}},
    *      @OA\Response(response=400, description="Invalid Token"),
    *      @OA\Response(response=401, description="Token Expired"),
    *      @OA\Response(response=404, description="Token Not Found"),
    *      @OA\Response(response=500, description="Internal Server Error"),
    *      @OA\Response(response=200, description="Items")
    * )
    */
    public function getItem(Request $request)
    {
        $data = Item::select(
            'items.item_id',
            'item_name',
            'city_id',
            'category_id',
            DB::raw('SUM(item_details.price)/COUNT(item_details.price) as ave_local_price')
        )
                        ->join('item_details', 'item_details.item_id', 'items.item_id')
                        ->where('items.item_id', $request->item_id)
                        ->with('tags')
                        ->get();

        $data->map(function ($i) use ($request) {
            $average_price = round(intval($i->ave_local_price), 2);
            $i['ave_local_price'] = strval($average_price);

            $item = new Item();
            if ($request->currency) {
                $user_currency_code = $request->currency;
                $user_currency_symbol = $item->getCurrencySymbol($request->currency)->symbol_native;
                $item_currency_code =  $item->getCityCurrency($i->city_id)->currency_code;

                $i['user_currency_code'] = $user_currency_code;
                $i['user_currency_symbol'] = $user_currency_symbol;
                $i['item_currency_code'] =  $item_currency_code;

                try {
                    $currencyService = new CurrencyService();
                    $rate = $currencyService->convertRates($user_currency_code, $item_currency_code);
                    $i['converted_price'] = strval(round($average_price / $rate, 2));
                } catch (\Exception $e) {
                    $i['converted_price'] = '';
                }
            } else {
                $i['user_currency_code'] = '';
                $i['user_currency_symbol'] = '';
                $i['item_currency_code'] =  '';
                $i['converted_price'] = '0.00';
            }

            $i['category_name'] = Category::find($i->category_id)->category_name;
            return $i;
        });

        return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/items",
     *      tags={"Items"},
     *      summary="Get All Items",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Items")
     * )
     */
    public function index(Request $request)
    {
        $data = $this->item->all();

        return $this->apiSuccessResponse($data, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * Report Item.
     */
    public function report(ReportRequest $request, Item $item)
    {
        $item->report(
            $request->input('reason_id'),
            $request->input('description'),
            $request->file('attachments', [])
        );

        return response()->json([], Response::HTTP_OK);
    }
}
