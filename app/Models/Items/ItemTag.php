<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Model;

class ItemTag extends Model
{
    protected $table = 'item_tags';

    protected $primaryKey = 'item_tag_id';

    public function items()
    {
        return $this->belongsToMany(Item::class, 'items');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tags');
    }
}
