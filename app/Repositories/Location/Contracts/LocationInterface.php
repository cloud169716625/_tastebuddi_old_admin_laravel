<?php

namespace App\Repositories\Location\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface LocationInterface
{
    /**
     * Get nearest location\business by given coordinates
     *
     * @param array $coordinnates
     * @param array $filters
     */
    public function getNearestVerifiedBusinesses(array $coordinnates, array $filters): Collection;

    /**
     * Get verified businesses/locations by city
     *
     * @param int $cityId
     * @param array $filters
     */
    public function getVerifiedBusinessesByCityId(int $cityId, array $filters): Collection;
}
