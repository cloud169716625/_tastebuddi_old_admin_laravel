<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecommendationResource extends JsonResource
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
            'id'                => $this->recommendation_id,
            'item'              => ItemResource::make($this->item),
            'user'              => UsersResource::make($this->user),
            'location'          => LocationResource::make($this->location),
            'recommended_price' => $this->recommended_price,
            'created_at'        => $this->created_at->toDateTimeString(),
            'updated_at'        => $this->updated_at->toDateTimeString(),
            'deleted_at'        => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null
        ];
    }
}
