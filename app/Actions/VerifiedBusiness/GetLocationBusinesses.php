<?php

namespace App\Actions\VerifiedBusiness;

use App\Models\Items\Location;
use App\Repositories\Location\Contracts\LocationInterface;
use App\Services\GeoLocation;
use Illuminate\Database\Eloquent\Collection;

class GetLocationBusinesses
{
    /**
     * @var \App\Repositories\Location\Contracts\LocationInterface $locationInterface
     */
    private $locationInterface;

    /**
     * Create new GetLocationBusinesses instance
     *
     * @param \App\Repositories\Location\Contracts\LocationInterface $locationInterface
     */
    public function __construct(
        LocationInterface $locationInterface
    ) {
        $this->locationInterface = $locationInterface;
    }

    /**
     * Get Verified Businesses
     *
     * @param \App\Models\Items\Location $location
     * @param array $filters
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Location $location, array $filters): Collection
    {
        $coordinates = $this->getCoordinates($location, $filters['coordinates'] ?? null);

        $businesses = $this->locationInterface
            ->getNearestVerifiedBusinesses($coordinates, $filters);

        return $businesses;
    }

    /**
     * Get coordinates to be used on getting the nearest verified businesses
     * @param \App\Models\Items\Location $location
     * @param null|array $userCoordinates
     * @return array
     */
    private function getCoordinates(Location $location, $userCoordinates): array
    {
        $coordinates = [
            'lng' => $location->lng_coordinate,
            'lat' => $location->lat_coordinate,
        ];

        if ($userCoordinates) {
            if ($this->isWithinTheCity($coordinates, $userCoordinates)) {
                return $userCoordinates;
            }
        }

        return $coordinates;
    }

    /**
     * Check if user is still within the city
     * @param array  $locationCoordinates
     * @param array $userCoordinates
     * @return bool
     */
    private function isWithinTheCity(array $locationCoordinates, array $userCoordinates): bool
    {
        //distance in kilometer to consider if user is still within the city
        $distanceToconsider = 10;

        $distance = (new GeoLocation())
                    ->computeDistanceInKilometers($userCoordinates, $locationCoordinates);

        return ($distance <= $distanceToconsider);
    }
}
