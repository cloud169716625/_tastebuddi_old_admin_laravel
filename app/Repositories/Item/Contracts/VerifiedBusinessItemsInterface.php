<?php

namespace App\Repositories\Item\Contracts;

interface VerifiedBusinessItemsInterface
{
    /**
     * Get verified business/location items by location
     *
     * @param int $locationId
     * @param array $filters
     */
    public function getVerifiedBusinessItemsByLocationId(int $locationId, array $filters = []);

    /**
     * Get verified business/location items by city
     *
     * @param int $cityId
     * @param array $filters
     */
    public function getVerifiedBusinessItemsByCityId(int $cityId, array $filters = []);
}
