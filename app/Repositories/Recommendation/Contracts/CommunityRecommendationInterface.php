<?php

namespace App\Repositories\Recommendation\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CommunityRecommendationInterface
{
    /**
     * Get Location's community recommendations
     *
     * @param int $locationId
     */
    public function getCommunityRecommendations(int $locationId): Collection;
}
