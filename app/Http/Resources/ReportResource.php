<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasMappedResource;
use App\Models\Items\Item;
use App\Models\Items\Recommendation;
use App\Models\Users\Users;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    use HasMappedResource;

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
     * The list of Models class to be mapped to a resource
     *
     * @return array
     */
    protected function mappedResource() : array
    {
        return [
            Users::class            => UsersResource::class,
            Recommendation::class   => RecommendationResource::class,
            Item::class             => ItemResource::class,
        ];
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
            'id'                => $this->id,
            'reportable'        => $this->whenLoaded('reportable', function () {
                                        return $this->getMappedResource($this->reportable);
                                    }),
            'reportable_type'   => $this->reportable_type,
            'report_type'       => $this->report_type,
            'reported_by'       => UsersResource::make($this->whenLoaded('reporter')),
            'reason'            => ReportCategoryResource::make($this->whenLoaded('reason')),
            'description'       => $this->description,
            'created_at'        => $this->created_at->toDateTimeString(),
            'updated_at'        => $this->updated_at->toDateTimeString(),
            'attachments'       => MediaResource::collection($this->whenLoaded('attachments'))
        ];
    }
}
