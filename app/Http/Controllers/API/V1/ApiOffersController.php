<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Items\ExclusiveOffer;
use Illuminate\Http\Request;

class ApiOffersController extends ApiBaseController
{
    /**
     * @OA\Get(
     *      path="/offers",
     *      tags={"Offers"},
     *      summary="Get offers",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Offers")
     * )
     */
    public function getOffers()
    {
        try {
            $offers = ExclusiveOffer::all();

            return $this->apiSuccessResponse(compact('offers'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    /**
     * @OA\Get(
     *      path="/offers/{offer_id}",
     *      tags={"Offers"},
     *      summary="Get offers' details",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Token Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Offers")
     * )
     */
    public function getOfferDetails(Request $request, $offer_id)
    {
        try {
            $offer = ExclusiveOffer::find($offer_id);

            return $this->apiSuccessResponse(compact('offer'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
}
