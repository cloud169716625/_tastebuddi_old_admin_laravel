<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->resource[$offset]);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'item_id'               => $this->item_id,
            'item_name'             => $this->item_name,
            'city'                  => CityResource::make($this->city),
            'category_id'           => $this->category_id,
            'image'                 => new MediaResource($this->whenLoaded('image')),
            'deleted_at'            => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null,
            'custom_price_low'      => $this->custom_price_low,
            'custom_price_high'     => $this->custom_price_high,
            'custom_price_average'  => $this->custom_price_average,
        ];
    }
}
