<?php

namespace App\Repositories\Recommendation;

use App\Models\Items\City;
use App\Models\Items\Item;
use App\Models\Items\Recommendation;
use App\Models\Users\Users;
use App\Models\VerifiedBusinessItem;
use App\Repositories\Recommendation\Contracts\CommunityRecommendationInterface;
use Illuminate\Database\Eloquent\Collection;

class RecommendationRepository implements CommunityRecommendationInterface
{
    /**
     * Get Location's community recommendations
     *
     * @param int $locationId
     */
    public function getCommunityRecommendations(int $locationId): Collection
    {
        $recommendations = Recommendation::select(
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
            ->join('items', 'items.item_id', 'recommendations.item_id')
            ->join('locations', 'locations.location_id', 'recommendations.location_id')
            ->whereHas('user', fn ($query) => $query->whereNull('disabled_at'))
            ->where('recommendations.location_id', $locationId)
            ->inRandomOrder()
            ->take(3)->get();

        $recommendations->map(function ($i) {
            $i['country_id'] = City::find($i->city_id)->country_id;
            $item = new Item();
            $i['local_currency'] = $item->getCityCurrency($i->city_id)->symbol_native;
            $user = Users::where('id', $i->user_id)->first();
            $i['user_name'] = $user->first_name;
            $i['user_image'] = $user->profile_photo_url ?
                $user->profile_photo_url : env('APP_URL') . '/images/user-default.png';

            return $i;
        });

        return $recommendations;
    }
}
