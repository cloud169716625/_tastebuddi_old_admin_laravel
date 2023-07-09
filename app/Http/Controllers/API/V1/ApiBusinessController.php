<?php

namespace App\Http\Controllers\API\V1;

use App\Actions\VerifiedBusiness\GetLocationBusinesses;
use App\Models\Items\Location;
use App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface;
use Illuminate\Http\Request;
use App\Repositories\Location\Contracts\LocationInterface;
use App\Repositories\Recommendation\Contracts\CommunityRecommendationInterface;
use App\Services\GoogleLocationService;

class ApiBusinessController extends ApiBaseController
{
    /**
     * @var \App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface $verifiedBusinessItemsInterface
     */
    private $verifiedBusinessItemsInterface;

    /**
     * @var \App\Repositories\Recommendation\Cotracts\CommunityRecommendationInterface $communityRecommendationInterface
     */
    private $communityRecommendationInterface;

    /**
     * Create new AjaxBaseController instance
     *
     * @param \App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface $verifiedBusinessItemsInterface
     * @param \App\Repositories\Recommendation\Cotracts\CommunityRecommendationInterface $communityRecommendationInterface
     */
    public function __construct(
        VerifiedBusinessItemsInterface $verifiedBusinessItemsInterface,
        CommunityRecommendationInterface $communityRecommendationInterface
    ) {
        $this->verifiedBusinessItemsInterface = $verifiedBusinessItemsInterface;
        $this->communityRecommendationInterface = $communityRecommendationInterface;
    }

    /**
     * Get Verified Businesses
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\Items\Location $location
     * @inject \App\Actions\VerifiedBusiness\GetLocationBusinesses $getLocationBusinesses
     * @return \Illuminate\Http\Response
     */
    public function getLocationBusinesses(Request $request, Location $location, GetLocationBusinesses $getLocationBusinesses)
    {
        $data = [
            'businesses' => $getLocationBusinesses($location, $request->all()),
            'community_recommendations' => $this->communityRecommendationInterface
                ->getCommunityRecommendations($location->location_id)
        ];

        return $this->apiSuccessResponse($data);
    }

    /**
     * Get location items
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\Items\Location $location
     * @return \Illuminate\Http\Response
     */
    public function getBusinessDetails(Request $request, Location $location)
    {
        $place = new GoogleLocationService();
        $placeDetails = $place->placeDetails($location->place_id);

        $placeDetails['is_verified'] = $location->is_verified;

        if ($location->is_verified) {
            $placeDetails['travelbuddi_recommendations'] = $this->verifiedBusinessItemsInterface
                ->getVerifiedBusinessItemsByLocationId($location->location_id);
        }


        $placeDetails['community_recommendations'] = $this->communityRecommendationInterface
            ->getCommunityRecommendations($location->location_id);

        return $this->apiSuccessResponse($placeDetails);
    }
}
