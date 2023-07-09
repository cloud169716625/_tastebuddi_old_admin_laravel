<?php

namespace App\Actions;

use App\Enums\MediaCollectionType;
use App\Models\Items\Item;
use App\Models\Items\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class CreateItem
{
    /**
     * Create an item for locatin/verified business
     *
     * @param \App\Models\Items\Location $location
     * @param array $data
     * @return void
     */
    public function __invoke(Location $location, array $data, bool $forVerifiedBusiness = true): void
    {
        DB::transaction(function () use ($location, $data, $forVerifiedBusiness) {
            $item =  Item::create($this->getItemData($location, $data));

            $this->uploadThumbNail($item, $data['thumb_nail']);

            $itemDetail = $item->details()
                            ->create(
                                $this->getItemDetailsData(
                                    $location->location_id,
                                    $data['price']
                                )
                            );

            if ($forVerifiedBusiness) {
                $location->verifiedBusinessItem()
                    ->create(
                        $this->getVerifiedBusinessItemData(
                            $item->item_id,
                            $data['price'],
                            $itemDetail->item_detail_id
                        )
                    );
            }
        });
    }

    /**
     * Get verified business item data to be saved
     *
     * @param int $itemId
     * @param float $price
     * @param int $detailId
     * @return array
     */
    private function getVerifiedBusinessItemData(int $itemId, float $price, int $detailId): array
    {
        return [
            'item_id'           => $itemId,
            'price'             => $price,
            'item_detail_id'    => $detailId
        ];
    }

    /**
     * Get Item details data to be saved
     *
     * @param int $locationId
     * @param float $price
     * @return array
     */
    private function getItemDetailsData(int $locationId, float $price): array
    {
        return [
            'location_id' => $locationId,
            'price' => $price,
        ];
    }

    /**
     * Gte Item data to be saved
     * @param \App\Models\Items\Location $location
     * @param array $data
     * @return array
     */
    private function getItemData(Location $location, array $data): array
    {
        return [
            'item_name' => $data['item_name'],
            'category_id' => $data['category_id'],
            'city_id' => $location->city_id,
        ];
    }

    /**
     * Upload the item thumb nail
     *
     * @param \App\Models\Items\Item $item
     * @param $file
     * @return void
     */
    private function uploadThumbNail(Item $item, $file): void
    {
        /** @var \Illuminate\Http\Testing\File */
        $image = $file;

        $name = Str::random(30);
        $hashName = explode('.', $image->hashName())[0];

        $item->addMedia($image)
            ->usingName("{$name}{$hashName}")
            ->usingFileName($image->hashName())
            ->toMediaCollection(MediaCollectionType::ITEM_IMAGE);
    }
}
