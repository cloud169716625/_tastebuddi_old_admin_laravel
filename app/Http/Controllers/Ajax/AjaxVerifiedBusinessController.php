<?php

namespace App\Http\Controllers\Ajax;

use App\Actions\CreateItem;
use App\Http\Requests\CreateItemToVerifiedBusinessRequest;
use App\Http\Requests\TagItemToLocationRequest;
use App\Models\Items\ItemDetail;
use App\Models\Items\Location;
use App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface;
use Illuminate\Support\Facades\DB;

class AjaxVerifiedBusinessController extends AjaxBaseController
{
    /**
     * @var \App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface $verifiedBusinessInterface
     */
    private $verifiedBusinessInterface;

    /**
     * Create new AjaxBaseController instance
     *
     * @param \App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface $verifiedBusinessInterface
     */
    public function __construct(VerifiedBusinessItemsInterface $verifiedBusinessInterface)
    {
        $this->verifiedBusinessInterface = $verifiedBusinessInterface;
    }

    /**
     * Tag an item
     *
     * @param \App\Http\Requests\TagItemToLocationRequest $request
     * @param \App\Models\Items\Location $location
     * @return \Illuminate\Http\Response
     */
    public function tagItem(TagItemToLocationRequest $request, Location $location)
    {
        $data = $request->validated();

        DB::transaction(function () use ($location, $data) {

            $detail = ItemDetail::create(
                array_merge(
                    $data,
                    ['location_id' => $location->location_id]
                )
            );

            $verifiedBusinessItem = $location->verifiedBusinessItem()
                ->create(
                    array_merge(
                        $data,
                        [
                            'item_detail_id' => $detail->item_detail_id,
                            'location_id' => $location->location_id
                        ]
                    )
                );

            $verifiedBusinessItem->update(['item_detail_id' => $detail->item_detail_id]);
        });

        return $this->responseSuccess([]);
    }

    /**
     * Get tagged items
     *
     * @param int $locationId
     * @return \Illuminate\Http\Response
     */
    public function getTaggedItems(int $locationId)
    {
        $items = $this->verifiedBusinessInterface
            ->getVerifiedBusinessItemsByLocationId($locationId);

        return $this->responseSuccess($items);
    }

    /**
     * Untagged an item from location/verified businness
     *
     * @param \App\Models\Items\Location $location
     * @param int $verifiedBusinessItemId
     * @return \Illuminate\Http\Response
     */
    public function unTaggedItem(Location $location, int $verifiedBusinessItemId)
    {
        DB::transaction(function () use ($location, $verifiedBusinessItemId) {
            $item = $location->verifiedBusinessItem()
                        ->find($verifiedBusinessItemId);

            ItemDetail::query()->withAllStatus()->find($item->item_detail_id)->delete();

            $item->delete();
        });

        return $this->responseSuccess([]);
    }

    /**
     * Create an item for location/verified business
     *
     * @param \App\Http\Requests\CreateItemToVerifiedBusinessRequest $request
     * @param \App\Models\Items\Location $location
     * @return \Illuminate\Http\Response
     */
    public function createItem(CreateItemToVerifiedBusinessRequest $request, Location $location)
    {
        $data = $request->validated();

        (new CreateItem())($location, $data);

        return $this->responseSuccess([]);
    }
}
