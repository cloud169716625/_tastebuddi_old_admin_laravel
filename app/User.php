<?php

namespace App;

use App\Models\Users\Users;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Users implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    /**
     * Get the identifier that will be
     * stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom
     * claims to be added to the JWT
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
