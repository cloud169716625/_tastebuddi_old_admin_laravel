<?php

namespace App\Actions;

use App\Events\Notifications\NotifyRecommendItemPrice;
use App\Models\Device;
use App\Models\Items\ItemDetail;
use App\Models\Users\Watchlist;

class NotifyWatchersOnPriceChange
{
    /**
     * The item price that relates to the recommendation
     * that will be used as a reference when notifying
     * users that is watching an item.
     */
    private ItemDetail $itemDetail;

    /**
     * Create a new instance.
     */
    public function __construct(ItemDetail $itemDetail)
    {
        $this->itemDetail = $itemDetail;
    }

    /**
     * Create Notifications.
     */
    public function execute(): void
    {
        /** @var \App\Models\Items\Recommendation */
        $recommendation = $this->itemDetail->recommendation;

        /** @var \App\Models\Items\Location */
        $location       = $recommendation->location;

        /** @var \App\Models\Items\Item */
        $item           = $recommendation->item;

        /** @var \Illuminate\Support\Collection */
        $watchers       = Watchlist::where('item_id', $item->id)->pluck('user_id');
        
        /** @var \Illuminate\Support\Collection */
        $contributors   = $item->recommendations->pluck('user_id');
        
        /** @var \Illuminate\Support\Collection */
        $notifyList     = $watchers->merge($contributors)->unique();
        
        /** @var \Illuminate\Support\Collection */
        $tokens         = Device::whereIn('user_id', $notifyList->toArray())->pluck('device_token');

        if (! $tokens->count()) return;

        $data = new \stdClass();
        $data->tokens = $tokens->toArray();
        $data->item_id = $item->item_id;
        $data->city_id = $item->city_id;
        $data->item_name = $item->item_name;
        $data->item_price = (float) $recommendation->recommended_price;
        $data->item_currency = $item->getCurrency();
        $data->item_currency_code = $item->getCurrencyCode();
        $data->lat = $location->lat_coordinate;
        $data->lng = $location->lng_coordinate;
        $data->data = [
            'tokens' => $data->tokens,
            'item_id' => $data->item_id,
            'city_id' => $data->city_id,
            'item_name' => $data->item_name,
            'item_price' => $data->item_price,
            'item_currency' => $data->item_currency,
            'item_currency_code' => $data->item_currency_code,
            'lat' => $data->lat,
            'lng' => $data->lng
        ];

        event(new NotifyRecommendItemPrice($data));
    }
}