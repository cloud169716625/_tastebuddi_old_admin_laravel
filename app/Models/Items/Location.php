<?php

namespace App\Models\Items;

use Illuminate\Http\Request;
use App\Models\BaseModel;
use App\Services\GoogleLocationService;
use App\Models\VerifiedBusinessItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Location extends BaseModel
{
    protected $table = 'locations';
    protected $primaryKey = 'location_id';
    protected $hidden = ['created_at', 'updated_at', 'pivot'];
    protected $guarded = ['location_id'];
    protected $casts = ['is_verified' => 'boolean'];


    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_details', 'location_id', 'item_id');
    }

    public function details()
    {
        return $this->hasMany(ItemDetail::class, 'location_id', 'location_id');
    }

    private function rules()
    {
        if ($this->category_id) {
            // validation rules for updated countries
            return [];
        }

        return [
            'location'         => 'required|string',
            'address'         => 'required|string',
            'lat_coordinate'         => 'required',
            'lng_coordinate'         => 'required',
            'place_id'         => [
                'required',
                Rule::unique('locations', 'place_id')->ignore($this->location_id, 'location_id')
            ]
        ];
    }

    public function store(Request $r)
    {
        if (!$this->validate($r)) {
            return false;
        }

        $this->fill($r->all());

        $location = Location::find($this->location_id);

        if (!$location) {
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

        // if ($this->location_id) {

        // } else {
            if ($validator->fails()) {
                $this->errors = $validator->errors()->first();
                return false;
            }
        // }

        return true;
    }

    public function getCollection(Request $r)
    {
        $this->setLpo($r);
        $this->fields = ['a.*'];

        $this->query = static::from($this->table . ' as a');
        // apply filters here

        if ($r->q) {
            $this->query->where('location', 'like', '%' . $r->q . '%');
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

    public function saveNewLocation($place_id)
    {
        $place = new GoogleLocationService();
        $place = $place->placeDetails($place_id);

        $location = new Location();
        $location->place_id = $place_id;
        $location->location = $place['name'];
        $location->address = $place['address'];
        $location->lat_coordinate = $place['latitude'];
        $location->lng_coordinate = $place['longitude'];

        $country = Country::where('country_name', $place['country'])->first();
        $city = City::where('city_name', $place['city'])->where('country_id', $country->country_id)->first();
        if (!$city) {
            $city = new City();
            $city->city_name = $place['city'];
            $city->country_id = $country->country_id;
            $city->save();
        }

        $location->city_id = $city->city_id;
        $location->save();

        return $location;
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    /**
     * Link verified business items
     */
    public function verifiedBusinessItem()
    {
        return $this->hasMany(VerifiedBusinessItem::class, 'location_id', 'location_id');
    }

    /**
     * Item Details
     */
    public function itemDetails(): HasMany
    {
        return $this->hasMany(ItemDetail::class, 'location_id', 'location_id');
    }
}
