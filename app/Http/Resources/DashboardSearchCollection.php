<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Items\Country;
use App\Models\Items\City;
use App\Models\Items\Item;
use App\Models\Items\ItemDetail;

class DashboardSearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'places' => '',
            'prices' => ''
        ];
    }
}
