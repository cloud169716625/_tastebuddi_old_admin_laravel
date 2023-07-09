<?php

namespace App\Providers;

use App\Repositories\Item\Contracts\ItemInterface;
use App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface;
use App\Repositories\Item\ItemRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Location\Contracts\LocationInterface;
use App\Repositories\Location\LocationRepository;
use App\Repositories\Recommendation\Contracts\CommunityRecommendationInterface;
use App\Repositories\Recommendation\RecommendationRepository;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register here the repositories and cointracts
     *
     * @var array $repositoriesAndContracts
     */
    private $repositoriesAndContracts = [
        ItemRepository::class => [
            ItemInterface::class,
            VerifiedBusinessItemsInterface::class,
        ],
        RecommendationRepository::class => [
            CommunityRecommendationInterface::class,
        ],
        LocationRepository::class => [
            LocationInterface::class,
        ]
    ];
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->repositoriesAndContracts as $repository => $contracts) {
            foreach ($contracts as $contract) {
                $this->app->bind($contract, $repository);
            }
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
