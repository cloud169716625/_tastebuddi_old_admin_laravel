<?php

namespace App\Models\Auth;

use App\Mails\ResetPasswordEmail;
use App\Models\BaseModel;
use App\Models\Users\Users;
use App\Validators\ResetPasswordValidator;
use DB;
use Illuminate\Http\Request;
use JWTAuth;

class PasswordReset extends BaseModel
{
    public $incrementing = false;
    protected $primaryKey = null;

    /**
     *  The attributes that are mass assignable.
     *
     *  @var array
     */
    protected $fillable = [ 'email', 'token' ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
    ];

    const UPDATED_AT = null;

    private function rules()
    {
        return [
            'email'     => 'required|string|email|exists:users',
        ];
    }

    public function validationMessages()
    {
        return [
            'email.required'     => 'Please fill in your e-mail address.',
            'email.email'        => 'Email address format is invalid.',
            'email.exists'       => 'This email address was not registered in our system.',
        ];
    }

    public function store( Request $r )
    {
        $validator   =  \Validator::make( $r->all() , $this->rules() );

        if( $validator->fails() ){
            $this->errors = $validator->errors()->all();
            return false;
        }

        $this->fill( $r->all() );

        try{
            $this->save();
        }catch( \Exception $e ) {
            $this->errors = $e->getMessage();
            return false;
        }

        return $this;
    }

    /**
     *
     * @param $request
     * @return array
     *
     */
    public function sendResetToken( Request $request )
    {

        try {

            PasswordReset::where( 'email',  $request->email )->delete();

            // Tokens are 5 capitalized random characters
            $token  = strtoupper( str_random( 5 ) );
            $request->merge( [ 'token' => \Hash::make( $token ) ] );

            if( ! $this->store( $request ) ){
                return false;
            }

            $user   = Users::where( 'email' , $request->email )->first();

            \Mail::to( $request->email )->send( new ResetPasswordEmail( $user, $token ));


            return $this;

        } catch( \Exception $e ){

            $this->errors[]     =   $e->getMessage();
            return false;
        }

    }

     /**
     * Reset the password using token and password
     *
     * @param $request
     * @return bool
     */
    public function resetPassword($request)
    {
        $validator = \Validator::make( $request->all(), ResetPasswordValidator::rules(), ResetPasswordValidator::messages() );

        if ($validator->fails() ){
            $this->addError( $validator->errors()->first() );
            return false;
        }

        $password_reset   =   PasswordReset::where( 'email', $request->email )->first();

        if( ! $password_reset ){
            $this->addError( 'Email '.$request->email.' did not request for a password reset' );
            return false;
        }

        // check for expiration... if created_at is more than 1 hr then invalidate the request
        // remove or modify this code if token should not expire or expires not a hr

        if( time() > ( strtotime( $password_reset->created_at ) + 3600 ) ){
            $this->addError( 'Token expired. You have to reset password within an hour after request' );
            return false;
        }

        if( \Hash::check( $request->token , $password_reset->token ) ){
            try{
		$user   =  Users::where( 'email',  $request->email )->first();
           	//$user->password = \Hash::make( $request->password );
               	$user->attributes['password'] = \Hash::make( $request->password );
		$user->save();
            }catch( \Exception $e ){
                $this->addError( ' Exception found : '.$e->getMessage() );
                return false;
            }

            return true;
        }

        $this->addError( 'Invalid Token' );
        return false;

    }
}
