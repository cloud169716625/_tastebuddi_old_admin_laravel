<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class Device extends Model
{
    protected $primaryKey = 'device_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
