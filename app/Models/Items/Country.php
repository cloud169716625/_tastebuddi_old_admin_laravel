<?php

namespace App\Models\Items;

use App\Enums\MediaCollectionType;
use App\Models\BaseModel;
use App\Models\Media;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Country extends BaseModel implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'countries';

    protected $primaryKey = 'country_id';

    public $timestamps = true;

    protected $guarded = ['country_id'];
    protected $hidden = ['pivot', 'background', 'flag'];


    /**
     * Register a Medica Collection callback for set up image converion properties.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionType::COUNTRY_FLAG_IMAGE)
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')->width(254);
            });

        $this->addMediaCollection(MediaCollectionType::COUNTRY_BACKGROUND)
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
    public function flag()
    {
        return $this->hasOne(Media::class, 'model_id')
            ->where('collection_name', MediaCollectionType::COUNTRY_FLAG_IMAGE);
    }

    /**
     * Define a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function background()
    {
        return $this->hasOne(Media::class, 'model_id')
            ->where('collection_name', MediaCollectionType::COUNTRY_BACKGROUND);
    }

    private function rules()
    {
        if ($this->country_id) {
            // validation rules for updated countries
            return [];
        }

        return [
            'country_name'         => 'required|string|max:45',
            'capital'      => 'required|string|max:45',
            'full_name'      => 'max:150',
            'country_numeric_code'      => 'max:10',
            'country_alpha_code_2'      => 'max:2',
            'country_alpha_code_3'      => 'max:3',
            'tl_domain'      => 'max:3',
            'currency_code'      => 'required|string|max:3',
            'currency_name'      => 'required|string|max:45',
            'symbol_native' => 'max:191'
        ];
    }

    public function getCollection(Request $r)
    {
        $this->setLpo($r);
        $this->fields = ['a.*'];

        $this->query = static::from($this->table . ' as a');
        // apply filters here

        if ($r->q) {
            $this->query->where('country_name', 'like', '%'. $r->q .'%');
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

    public function cities()
    {
        return $this->hasMany(City::class, 'country_id', 'country_id');
    }

    public function userPreference()
    {
        return $this->belongsTo(UserPreference::class);
    }



    public function store(Request $r)
    {
        if (!$this->validate($r)) {
            return false;
        }

        $this->fill($r->all());

        $country = Country::find($this->country_id);

        if (!$country) {
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



    private function validate($request)
    {

        $validator = \Validator::make($request->all(), $this->rules());

        if ($this->country_id) {
        } else {
            if ($validator->fails()) {
                $this->errors = $validator->errors()->first();
                return false;
            }
        }

        return true;
    }

    /**
     * Flag URL based on Uploaded Image
     * 
     * @return string|null
     */
    public function getFlagUrlAttribute()
    {
        return $this->flag
            ? $this->flag->getFullUrl()
            : null;
    }

    /**
     * Background URL based on Uploaded Image
     * 
     * @return string|null
     */
    public function getBackgroundUrlAttribute()
    {
        return $this->background 
            ? $this->background->getFullUrl()
            : null;
    }
}
