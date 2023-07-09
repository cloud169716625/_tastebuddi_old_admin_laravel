<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $hidden = ['pivot'];
    protected $fillable = ['user_id', 'item_id'];
}
