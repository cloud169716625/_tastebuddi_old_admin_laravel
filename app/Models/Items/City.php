<?php

namespace App\Models\Items;

use App\Models\BaseModel;
use Illuminate\Http\Request;

class City extends BaseModel
{
    protected $table = 'cities';

    protected $primaryKey = 'city_id';

    public $timestamps = true;

    protected $guarded = ['city_id'];


    private function rules()
    {
        if ($this->country_id) {
            // validation rules for updated countries
            return [];
        }

        return [
            'city_name'         => 'required|string|max:150',
            'country_id'      => 'required',
            'country_code'      => 'string|max:10'
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'city_id', 'city_id');
    }

    public function getCollection(Request $r)
    {

        // dd($r);
        $this->setLpo($r);
        $this->fields = ['a.*'];

        $this->query = static::from($this->table . ' as a');
        // apply filters here

        if ($r->q) {
            $this->query->where('city_name', 'like', '%'. $r->q .'%');
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


    public function store(Request $r)
    {
        if (!$this->validate($r)) {
            return false;
        }

        $this->fill($r->all());

        $city = City::find($this->city_id);

        if (!$city) {
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

        if ($this->city_id) {
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
}
