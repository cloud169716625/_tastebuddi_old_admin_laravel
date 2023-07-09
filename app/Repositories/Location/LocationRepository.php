<?php

namespace App\Repositories\Location;

use App\Models\Items\Location;
use App\Repositories\Location\Contracts\LocationInterface;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository implements LocationInterface
{
    /**
     * Get nearest location\business by given coordinates
     *
     * @param array $coordinnates
     * @param array $filters
     */
    public function getNearestVerifiedBusinesses(array $coordinnates, array $filters): Collection
    {
        //in kilometer
        $radius = 3956;

        $locations = Location::join('item_details', 'item_details.location_id', 'locations.location_id')
        ->selectRaw("
            locations.*, ((ACOS(SIN({$coordinnates['lat']} * PI() / 180) *
            SIN(locations.lat_coordinate * PI() / 180) + COS({$coordinnates['lat']} * PI() / 180) *
            COS(locations.lat_coordinate * PI() / 180) * COS(({$coordinnates['lng']} - locations.lng_coordinate) *
            PI() / 180)) * 180 / PI()) * 60 * 1.1515)
            as distance
        ")
        ->when(isset($filters['verified_only']), function ($query) use ($filters) {
            $query->where('locations.is_verified', $filters['verified_only'] == 'true');
        })
        ->when(isset($filters['city_id']), function ($query) use ($filters) {
            $query->where('locations.city_id', $filters['city_id']);
        })
        ->when(isset($filters['item_id']), function ($query) use ($filters) {
            $query->where('item_details.item_id', $filters['item_id']);
        })
        ->whereHas('details', function ($query) {
            $query->where(function ($query) {
                $query->whereDoesntHave('recommendation')
                    ->orWhereHas('recommendation.user', function ($query) {
                        $query->whereNull('disabled_at');
                    });
            });
        })
        ->having('distance', '<=', $radius)
        ->distinct('locations.location_id')
        ->orderBy('distance', 'ASC')
        ->limit(50)
        ->get()
        ->map(function ($location) {
            $item = $location->items()->whereHas('image')->inRandomOrder()->first();

            $location->image_url = ($item) ? $item->image->getFullUrl() : null;

            return $location;
        });

        return $locations;
    }

    /**
     * Get verified businesses/locations by city
     *
     * @param int $cityId
     * @param array $filters
     */
    public function getVerifiedBusinessesByCityId(int $cityId, array $filters): Collection
    {
        $businesses = Location::where('city_id', $cityId)
            ->where('is_verified', true)
            ->when(isset($filters['limit']), function ($query) use ($filters) {
                $query->limit($filters['limit']);
            })
            ->get();


        return $businesses;
    }
}
