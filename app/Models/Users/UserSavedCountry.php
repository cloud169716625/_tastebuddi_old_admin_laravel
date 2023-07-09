<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use App\Models\Items\Country;

class UserSavedCountry extends BaseModel
{
    protected $table = 'user_saved_countries';
    protected $primaryKey = 'user_saved_country_id';
    protected $fillable = ['user_id', 'country_id'];
    protected $hidden = ['created_at', 'updated_at', 'country'];

    public function getUserCountries($user)
    {
        $u_countries = UserSavedCountry::
                            select(
                                'countries.country_id',
                                'countries.country_name',
                                // 'countries.background_url',
                                'countries.currency_code'
                            )
                            ->join('countries', 'countries.country_id', 'user_saved_countries.country_id')
                            ->where('user_saved_countries.user_id', $user->id)
                            ->get();

        $u_countries->each(function ($country) {
            $country->background_url = $country->country->background_url;
        });

        return $u_countries;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'countries');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }
}
