<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'tag_id';
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_tags', 'item_id', 'tag_id')->withTimestamps();
    }
}
