<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $table = 'user_preferences';

    protected $guarded = [ 'user_preference_id' ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class);
    }
}
