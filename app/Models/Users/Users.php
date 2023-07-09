<?php

namespace App\Models\Users;

use App\Enums\MediaCollectionType;
use App\Mails\ResendVerificationCodeEmail;
use App\Mails\SendVerificationCodeEmail;
use App\Models\BaseModel;
use App\Models\Items\Country;
use App\User;
use App\Models\Items\Item;
use App\Helpers\Utils;
use App\Models\Media;
use App\Models\Traits\CanBeReported;
use Illuminate\Http\Request;
use JWTAuth;
use Intervention\Image\ImageManagerStatic as Image;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;

class Users extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    HasMedia
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;
    use CanBeReported;
    use HasMediaTrait;

    protected $table = 'users';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['id', 'email', 'first_name', 'last_name', 'password', 'mobile_number', 'is_allowed', 'disabled_at'];
    protected $hidden = ['password', 'remember_token', 'pivot'];
    protected $appends = ['full_name', 'profile_photo_url'];
    protected $casts = [
        'is_allowed' => 'boolean',
    ];

    public $sql;
    public $bindings;

    /**
     * Register a Medica Collection callback for set up image converion properties.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionType::USER_AVATAR)
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')->width(254);
            });
    }

    /**
     * Define a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image()
    {
        return $this->hasOne(Media::class, 'model_id')
            ->where('collection_name', MediaCollectionType::USER_AVATAR);
    }

    /**
     * @return array
     */
    private function rules()
    {
        if ($this->id) {
            // validation rules for updated users
            return [];
        }

        return [
            'email'         => 'required|string|email|max:50|unique:users',
            'password'      => 'required|string|min:8|max:24|regex:/[a-z]/|regex:/[A-Z]/|'
        ];
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate($request)
    {
        $validator = \Validator::make($request->all(), $this->rules());

        if ($this->id) {
            // Usernames must not be modified

            //Not sure yet about this one, but TravelBuddi can skip login


            /* if( $request->email && $request->email != $this->email ){

                $validator->errors()->add( 'email', 'Not allowed to modify Email or Username' );
                $this->errors = $validator->errors()->first();
                return false;

            } */
        } else {
            if ($validator->fails()) {
                $this->errors = $validator->errors()->first();
                return false;
            }
        }

        return true;
    }

    /**
     * Common saving method for the model
     *
     * @param Request $r
     * @return $this|bool
     *
     */

    public function store(Request $r)
    {
        if (!$this->validate($r)) {
            return false;
        }

        $this->fill($r->all());

        $user = Users::find($this->id);

        if (!$user) {
            // do stuff for new users here

            //send email
            // \Mail::to( $this->email )->send( new SendVerificationCodeEmail( $this ) );


            //send text
            // $this->sendSms($r);
        } else {
            $this->exists = true;
        }

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }

        return $this;
    }

    public function getCollection(Request $r)
    {
        $this->setLpo($r);
        $this->fields = ['a.*'];

        $this->query = static::from($this->table . ' as a');
        // apply filters here

        if ($r->q) {
            // $this->query->where('email', $r->q);
            $this->query->where('email', 'like', '%'. $r->q .'%')
            ->orWhere('first_name', 'like', '%'. $r->q .'%')
            ->orWhere('last_name', 'like', '%'. $r->q.'%');
        }

        if ($r->return_total) {
            $this->total = $this->query->count();
        }

        $this->assignLpo();

        if ($r->return_builder) {
            return $this->query;
        }

        if ($r->paginate) {
            return $this->query->paginate();
        }

        return $this->query->get($this->fields);
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = \Hash::make($password);
        }
    }

    public function sendSms(Request $request)
    {
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $userMobile = config('app.twilio')['TWILIO_FROM_NUMBER'];

        $client = new Client($accountSid, $authToken);

        try {
            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
                $this->mobile_number,
                array(
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => $userMobile,
                    // the body of the text message you'd like to send
                    'body' => 'AMC Verification code: ' . $this->verification_code
                )
            );
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getJwtToken()
    {
        // the JWTSubject class is App/User and not $this
        $jwt_subject = (new User())->find($this->id);

        if (!$jwt_subject) {
            return false;
        }

        return JWTAuth::fromUser($jwt_subject);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function emailExists($email)
    {
        $user =  static::where('email', $email)
            ->first();

        return $user;
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function resendVerificationCode()
    {
        $this->verification_code = $this->generateVerificationCode();

        try {
            $this->save();
            \Mail::to($this->email)->send(new ResendVerificationCodeEmail($this));
        } catch (\Exception  $e) {
            $this->addError($e->getMessage());
            return false;
        }

        return $this;
    }

    public function verify($verification_code)
    {
        if (!$this->id) {
            $this->addError('Unknown user id');
            return false;
        }

        if (!$verification_code) {
            $this->addError('Verification code must not be empty');
            return false;
        }

        if ($this->is_verified) {
            $this->addError('User was already verified');
            return false;
        }

        if ($verification_code != $this->verification_code) {
            $this->addError('Incorrect verification code');
            return false;
        }

        $this->verification_code = $verification_code;
        $this->is_verified = date('Y-m-d H:i:s');
        $this->save();

        return $this;
    }

    /**
     * @param Request $request
     * @return $this|bool
     * @deprecated 1
     */

    public function uploadProfilePhoto(Request $request)
    {
        if (! $this->id) {
            $this->addError('Unknown user') ;
            return false;
        }


        $uniqname  = date('Ymd') . $this->id . '_' . uniqid() . '.png';
        $photo_url = url('storage/images/users/'. $this->id. '/' . $uniqname);
        $dir_path  = storage_path() . '/app/public/images/users/'. $this->id . '/';

        if (! is_dir($dir_path)) {
            mkdir($dir_path, 755, true);
        }

        Image::make(file_get_contents($request->photo))
                ->fit(200)
                ->save($dir_path . $uniqname);

        $this->profile_photo_url = $photo_url;

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->addError($e->getMessage()) ;
            return false;
        }

        if ($request->with_thumbnails) {
            $this->has_thumbnails = true;
        }

        return $this;
    }



    /**
     * User image path is designed so that it would be easy to have a daily backup
     * without downloading everything
     *
     * @return string
     *
     */
    private function generateUserImagePath()
    {
        $md  =  date('md');
        $y  =  date('Y');

        return '/images/' . $y . '/' . $md . '/' . Utils::convertInt($this->id) . '/';
    }

    private function generateVerificationCode()
    {
        $code = strtoupper(str_random(6));

        $has_code = static::where('verification_code', $code)->count();

        if ($has_code) {
            return $this->generateVerificationCode();
        }

        return $code;
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'user_saved_countries', 'user_id', 'country_id')
                        ->select('countries.country_id', 'countries.country_name', 'countries.background_url');
    }

    public function watchlist()
    {
        return $this->belongsToMany(Item::class, 'watchlists', 'user_id', 'item_id')->withTimestamps();
    }

    public function recommendations()
    {
        return $this->belongsToMany(Item::class, 'recommendations', 'user_id', 'item_id', 'item_id')->withTimestamps();
    }

    /**
     * Helper to disable user
     *
     * @return void
     */
    public function disable(): void
    {
        if (!$this->isDisabled()) {
            $this->update(['disabled_at' => now()]);
        }
    }

    /**
     * Helper to enable user
     *
     * @return void
     */
    public function enable(): void
    {
        if ($this->isDisabled()) {
            $this->update(['disabled_at' => null]);
        }
    }

    /**
     * Helper to check if user is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return filled($this->disabled_at);
    }

    /**
     * Helper to check if user is deleted
     */
    public function isDeleted(): bool
    {
        return filled($this->deleted_at);
    }

    /**
     * Profile Photo URL accessor.
     * 
     * @return string|null
     */
    public function getProfilePhotoUrlAttribute()
    {
        $imageUrl = ($this->image)
            ? $this->image->getFullUrl()
            : null;

        $this->unsetRelation('image');

        return $imageUrl;
    }
}
