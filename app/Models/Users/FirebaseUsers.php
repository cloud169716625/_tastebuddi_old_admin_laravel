<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Http\Request;

class FirebaseUsers extends BaseModel{

    protected $table        = 'users_firebase';
    protected $primaryKey   = 'fid';

    public $timestamps      = false;

    protected $fillable     = [ 'device_token' , 'user_id' ];
    protected $appends      = [];
    protected $hidden       = [];

    /**
     * @param $user_id
     * @return static
     */
    public static function byUserId( $user_id )
    {
        // @TODO check for expired device tokens as well

        $firebase = static::where( 'user_id',
            $user_id )->first();

        if( $firebase ){
            return $firebase;
        }

        return false;
    }

    public function store( Request $r )
    {
        $validator = \Validator::make( $r->all() , [
            'device_token'  => 'required',
            'user_id'       => 'required'
        ]);

        if( $validator->fails() ){
            $this->errors = $validator->errors()->all();
            return false;
        }

        $this->fill( $r->all() );
        $pk = $this->primaryKey;

        if( $r->$pk  ){
            $this->exists = true;
        }else{
            $this->added_at = date('Y-m-d H:i:s' );
            $expire_at = strtotime( date( 'Y-m-d H:i:s' ) ) + 72000;
            $this->expire_at = date( 'Y-m-d H:i:s' , $expire_at ); //expire after twenty hours
        }

        $this->save();

        return $this;
    }

    /**
     * Returns all users who are currently able to receive notifications
     *
     * @param Request $r
     * @return mixed
     */
    public function getActive( )
    {
        $now = date( 'Y-m-d H:i:s');

        return    static::where( 'push_notification_enabled' , 1  )
                ->where( 'expire_at' , '>' , $now )
                ->limit( 500 )
                ->get();
    }
    
    public function activeCount()
    {
        $now = date( 'Y-m-d H:i:s');

        return    static::where( 'push_notification_enabled' , 1  )
            ->where( 'expire_at' , '>' , $now )
            ->count();
    }

}