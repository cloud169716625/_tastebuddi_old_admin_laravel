<?php

namespace App\Listeners\Notifications;

use App\Events\Notifications\NotifyRecommendItemPrice;
use App\Models\Device;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class RecommendItemPrice
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NotifyRecommendItemPrice $event)
    {
        $tokens = $event->data->tokens;
        $item_id = $event->data->item_id;
        $city_id = $event->data->city_id;
        $item_name = $event->data->item_name;
        $item_price = number_format($event->data->item_price, 2, ".", ",");
        $item_currency = $event->data->item_currency;
        $item_currency_code = $event->data->item_currency_code;
        $lat = $event->data->lat;
        $lng = $event->data->lng;

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $optionBuilder->setContentAvailable(true);

        $notificationBuilder = new PayloadNotificationBuilder('Price Update');
        $notificationBuilder->setBody("New recommended price for $item_name: $item_currency_code $item_currency $item_price")
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['name' => $item_name])
            ->addData(['item_id' => $item_id])
            ->addData(['city_id' => $city_id])
            ->addData(['item_currency_code' => $item_currency_code])
            ->addData(['lat' => $lat])
            ->addData(['lng' => $lng])
            ->addData(['price' => $item_price]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

//        $token = "f0V5aAjGW7I:APA91bGlshinKXIAWR97SotBcihmmZ5ZxhZeZ5z-IXIZX1E64eVl3KOmKa8dMnGdHuwpXtfsvMtW5l03JZUmWWcoB2PfOZB1OZ58LqoMZa83Ih9B20i5Flu9wpAAP9mcBAwHCsnZwXvl";

//        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        // multi-device
        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();
    }
}
