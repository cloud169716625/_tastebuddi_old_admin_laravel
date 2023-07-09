<?php

namespace App\Models\Items;

use App\Models\BaseModel;

class ExclusiveOffer extends BaseModel
{
    protected $table = 'exclusive_offers';

    protected $primaryKey = 'exclusive_offer_id';

    public $timestamps = true;

    protected $guarded = ['exclusive_offer_id'];
    protected $hidden = ['created_at', 'updated_at'];
}
