<?php

namespace App\Observers;

use App\Models\Items\Recommendation;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class RecommendationObserver
{
    /**
     * Handle the recommendation "created" event.
     *
     * @param  \App\Recommendation  $recommendation
     * @return void
     */
    public function created(Recommendation $recommendation)
    {

    }

    /**
     * Handle the recommendation "updated" event.
     *
     * @param  \App\Recommendation  $recommendation
     * @return void
     */
    public function updated(Recommendation $recommendation)
    {
        //
    }

    /**
     * Handle the recommendation "deleted" event.
     *
     * @param  \App\Recommendation  $recommendation
     * @return void
     */
    public function deleted(Recommendation $recommendation)
    {
        //
    }

    /**
     * Handle the recommendation "restored" event.
     *
     * @param  \App\Recommendation  $recommendation
     * @return void
     */
    public function restored(Recommendation $recommendation)
    {
        //
    }

    /**
     * Handle the recommendation "force deleted" event.
     *
     * @param  \App\Recommendation  $recommendation
     * @return void
     */
    public function forceDeleted(Recommendation $recommendation)
    {
        //
    }
}
