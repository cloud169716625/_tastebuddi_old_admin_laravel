<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\ReportRequest;
use App\Models\Items\Recommendation;
use App\Models\Items\City;
use App\Models\Users\Users;
use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Services\GoogleLocationService;
use Illuminate\Http\Response;

class ApiRecommendationsController extends ApiBaseController
{
    /**
     * @OA\Get(
     *      path="/cities/{city_id}/items/{item_id}/recommendations",
     *      tags={"Recommendations"},
     *      summary="Get recommendations",
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
     *      @OA\Response(response=200, description="Recommendations")
     * )
     */
    public function getItemRecommendations(Request $request, $city_id, $item_id)
    {
        try {
            $recomms = Recommendation::
                                join('locations', 'locations.location_id', 'recommendations.location_id')
                                ->whereHas('user', function ($query) {
                                    $query->whereNull('disabled_at');
                                })
                                ->where('item_id', $item_id)
                                ->inRandomOrder()
                                ->take(3)->get();

            $currency = City::
                    select('symbol_native')
                    ->join('countries', 'cities.country_id', '=', 'countries.country_id')
                    ->where('cities.city_id', $city_id)
                    ->first();

            $recommendations = [];
            foreach ($recomms as $key => $recommendation) {
                /** @var \App\Models\Users\Users */
                $user = Users::where('id', $recommendation->user_id)->first();

                $user_image = ($user && $user->isDeleted()) ? env('APP_URL') .'/images/user-default.png' : $user->profile_photo_url;
                $recommendations[$key]['recommended_id'] = $recommendation->recommendation_id;
                $recommendations[$key]['item_id'] = $recommendation->item_id;
                $recommendations[$key]['city_id'] = intval($city_id);
                $recommendations[$key]['country_id'] = City::find($city_id)->country_id;
                $recommendations[$key]['currency_symbol'] = $currency->symbol_native;
                $recommendations[$key]['price'] = $recommendation->recommended_price;
                $recommendations[$key]['location'] = $recommendation->location;
                $recommendations[$key]['address'] = $recommendation->address;
                $recommendations[$key]['lat_coordinate'] = $recommendation->lat_coordinate;
                $recommendations[$key]['lng_coordinate'] = $recommendation->lng_coordinate;
                $recommendations[$key]['address'] = $recommendation->address;
                $recommendations[$key]['user_image'] = $user_image ?
                                    $user_image : env('APP_URL') .'/images/user-default.png';
                $recommendations[$key]['user_id'] = $recommendation->user_id;
            }

            return $this->apiSuccessResponse($recommendations, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/locations/{location_id}/recommendations",
     *      tags={"Recommendations"},
     *      summary="Get recommendations",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="location_id",
     *          description="Location ID",
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
     *      @OA\Response(response=200, description="Recommendations")
     * )
     */
    public function getPlaceRecommendations(Request $request, $location_id)
    {
        try {
            $recommendations = Recommendation::
                                select(
                                    'recommendation_id',
                                    'user_id',
                                    'items.item_id',
                                    'item_name',
                                    'recommended_price',
                                    'locations.city_id',
                                    'locations.lat_coordinate',
                                    'locations.lng_coordinate',
                                    'locations.address'
                                )
                                ->whereHas('user', function ($query) {
                                    $query->whereNull('disabled_at');
                                })
                                ->join('items', 'items.item_id', 'recommendations.item_id')
                                ->join('locations', 'locations.location_id', 'recommendations.location_id')
                                ->where('recommendations.location_id', $location_id)
                                ->inRandomOrder()
                                ->take(3)->get();

            $recommendations->map(function ($i, $index) use ($request) {
                $i['country_id'] = City::find($i->city_id)->country_id;
                $item = new Item;
                $i['local_currency'] = $item->getCityCurrency($i->city_id)->symbol_native;
                $user = Users::where('id', $i->user_id)->first();
                $i['user_name'] = $user->first_name;
                $i['user_image'] = $user->profile_photo_url ?
                                        $user->profile_photo_url : env('APP_URL') .'/images/user-default.png';

                $i['image_url'] = ($i->item->image)
                                ? $i->item->image->getFullUrl()
                                : null;

                $i->unsetRelation('item');

                return $i;
            });

            return $this->apiSuccessResponse($recommendations, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/details",
     *      tags={"Recommendations"},
     *      summary="Get place details",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="recommendations"
     *      )
     * )
     */
    public function getPlaceDetails(Request $request, $place_id)
    {
        try {
            $place = new GoogleLocationService();
            $place = $place->placeDetails($place_id);

            return $this->apiSuccessResponse($place, true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * Report User.
     */
    public function report(ReportRequest $request, Recommendation $recommendation)
    {
        $recommendation->report(
            $request->input('reason_id'),
            $request->input('description'),
            $request->file('attachments', [])
        );

        return response()->json([], Response::HTTP_OK);
    }
}
