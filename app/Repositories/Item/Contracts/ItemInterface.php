<?php

namespace App\Repositories\Item\Contracts;

interface ItemInterface
{
    /**
     * Get's an item by it's ID
     *
     * @param int
     */
    public function findById($itemId);

    /**
     * Get's an item by it's Name
     *
     * @param string
     */
    public function findByName($itemName);

    /**
     * Get's all items.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes an item.
     *
     * @param int
     */
    public function delete($itemId);

    /**
     * Updates an item.
     *
     * @param int
     * @param array
     */
    public function update($itemId, array $itemData);

    /**
     * Get Items By city id
     *
     * @param int $cityId
     */
    public function getItemsByCityId(int $cityId);
}
