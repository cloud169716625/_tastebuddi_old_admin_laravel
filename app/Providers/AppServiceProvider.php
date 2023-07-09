<?php

namespace App\Providers;

use App\Models\Items\Item;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\Resource;

use App\Observers\RecommendationObserver;
use App\Models\Items\Recommendation;
use App\Observers\ItemObserver;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Resource::withoutWrapping();

        Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {
            $type = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];
            if (in_array($type, $parameters)) {
                return true;
            }
            return false;
        });

        Validator::replacer('image64', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':values', join(",", $parameters), $message);
        });

        Recommendation::observe(RecommendationObserver::class);

        if (in_array(config('app.env'), ['production', 'staging'])) {
            URL::forceScheme('https');
        }
    }
}
