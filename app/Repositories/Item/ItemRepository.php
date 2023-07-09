<?php

namespace App\Repositories\Item;

use App\Models\Items\Item;
use App\Repositories\Item\Contracts\ItemInterface;
use App\Repositories\Item\Contracts\VerifiedBusinessItemsInterface;

class ItemRepository implements ItemInterface, VerifiedBusinessItemsInterface
{
    /**
     * Get's an item by it's ID
     *
     * @param int
     * @return collection
     */
    public function findById($itemId)
    {
        return Item::where('item_id', $itemId)->firstOrFail();
    }

    /**
     * Get's an item by it's Name
     *
     * @param string
     * @return collection
     */
    public function findByName($itemName)
    {
        return Item::where('item_name', $itemName)->firstOrFail();
    }

    /**
     * Get's all items.
     *
     * @return mixed
     */
    public function all()
    {
        return Item::all();
    }


    /**
     * Deletes an item.
     *
     * @param int
     */
    public function delete($itemId)
    {
        Item::destroy($itemId);
    }

    /**
     * Updates an item.
     *
     * @param int
     * @param array
     */
    public function update($itemId, array $itemData)
    {
        Item::find($itemId)->update($itemData);
    }

    /**
     * Get Items By city id
     *
     * @param int $cityId
     */
    public function getItemsByCityId(int $cityId)
    {
        $keyword = request()->get('search', '');

        $items = Item::where('city_id', $cityId)
            ->where('item_name', 'LIKE', "%{$keyword}%")
            ->get();

        return $items;
    }

    /**
     * Get verified business/location items
     *
     * @param int $locationId
     * @param array $filters
     */
    public function getVerifiedBusinessItemsByLocationId(int $locationId, array $filters = [])
    {
        $items = Item::join('verified_business_items', 'verified_business_items.item_id', 'items.item_id')
            ->join('cities', 'cities.city_id', 'items.city_id')
            ->join('countries', 'countries.country_id', 'cities.country_id')
            ->where('verified_business_items.location_id', $locationId)
            ->with('category')
            ->when(isset($filters['limit']), function ($query) use ($filters) {
                $query->take($filters['limit']);
            })
            ->select(
                'verified_business_items.id',
                'items.*',
                'verified_business_items.price',
                'countries.symbol_native',
            )
            ->get()
            ->map(function ($item) {
                $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
                $item->unsetRelation('image');

                return $item;
            });

        return $items;
    }

    /**
     * Get verified business/location items by city
     *
     * @param int $cityId
     * @param array $filters
     */
    public function getVerifiedBusinessItemsByCityId(int $cityId, array $filters = [])
    {
        $items = Item::join('verified_business_items', 'verified_business_items.item_id', 'items.item_id')
        ->where('items.city_id', $cityId)
        ->with('category')
        ->when(isset($filters['limit']), function ($query) use ($filters) {
            $query->take($filters['limit']);
        })
        ->select('verified_business_items.id', 'items.*', 'verified_business_items.price')
        ->get()
        ->map(function ($item) {
            $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
            $item->unsetRelation('image');

            return $item;
        });

        return $items;
    }
}
