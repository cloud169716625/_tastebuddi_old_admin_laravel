<?php

namespace App\Models\Items;

use App\Enums\MediaCollectionType;
use App\Enums\QuartileType;
use App\Models\BaseModel;
use App\Models\Media;
use App\Models\Traits\CanBeReported;
use App\Models\Users\Users;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use DB;
use Facades\App\Repositories\CurrencyRatesRepository;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Item extends BaseModel implements HasMedia
{
    use CanBeReported;
    use SoftDeletes;
    use HasMediaTrait;

    protected $table = 'items';

    protected $primaryKey = 'item_id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = ['item_id'];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'converted_price' => 'string'
    ];

    /**
     * Register a Medica Collection callback for set up image converion properties.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionType::ITEM_IMAGE)
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
            ->where('collection_name', MediaCollectionType::ITEM_IMAGE);
    }


    private function rules()
    {
        if ($this->category_id) {
            // validation rules for updated countries
            return [
                'custom_price_low'  => [
                    'numeric',
                    'required_with:custom_price_high',
                    'nullable',
                    'lte:custom_price_high',
                    'not_in:0',
                ],
                'custom_price_high'  => [
                    'numeric',
                    'required_with:custom_price_low',
                    'nullable',
                    'gte:custom_price_low',
                ],
                'custom_price_average' => [
                    'numeric',
                    'nullable',
                    'not_in:0',
                    'required_with_all:custom_price_low,custom_price_high'
                ]
            ];
        }

        return [
            'item_name'         => 'required|string|max:150',
            'image_url'         => 'max:500',
            'city_id'           => 'required',
            'category_id'       => 'required',
            'custom_price_low'  => [
                'numeric',
                'required_with:custom_price_high',
                'nullable',
                'lte:custom_price_high',
                'not_in:0',
                'min:0'
            ],
            'custom_price_high'  => [
                'numeric',
                'required_with:custom_price_low',
                'nullable',
                'gte:custom_price_low',
                'min:0'
            ],
            'custom_price_average' => [
                'numeric',
                'nullable',
                'not_in:0',
                'min:0',
                'required_with_all:custom_price_low,custom_price_high'
            ]
        ];
    }

    public function getCollection(Request $r, $city_id)
    {
        $this->setLpo($r);
        $this->fields = ['items.*'];

        $this->query = static::from($this->table);

        $rate = 0;

        if ($r->currency) {
            $user_currency_code = $r->currency;
            $item_currency_code = $this->getCityCurrency($city_id)->currency_code;

            try {
                $currencyService = new CurrencyService();
                $rate = $currencyService->convertRates($user_currency_code, $item_currency_code);
            } catch (\Exception $e) {

            }
        }

        $this->query->whereHas('details', function ($query) {
            $query->where(function ($query) {
                $query->whereDoesntHave('recommendation')
                    ->orWhereHas('recommendation.user', function ($query) {
                        $query->whereNull('disabled_at');
                    });
            })
            ->where(function ($query) {
                $query->whereDoesntHave('verifiedBusinessItem')
                    ->orWhereHas('verifiedBusinessItem.location', function ($query) {
                        $query->where('is_verified', true);
                    });
            });
        });

        $this->query->where('items.city_id', $city_id)->with('tags');

        $this->order_by = 'items.item_id';
        $this->order_direction = 'ASC';

        if ($r->return_total) {
            $this->total = $this->query->count();
        }

        $this->assignLpo();

        return $this->query->get($this->fields)->map(function ($item) use ($rate) {

            $averagePrice = $item->getAveragePrice();

            $item->ave_local_price = bcdiv($averagePrice, 1, 2);

            $item->converted_price = $rate > 0
                                ? bcdiv($averagePrice, $rate, 2)
                                : 0;

            $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
            $item->unsetRelation('image');

            return $item;
        });
    }

    public function getCityCurrency($city_id)
    {
        return City::select('currency_code', 'symbol_native')
            ->join('countries', 'cities.country_id', '=', 'countries.country_id')
            ->where('cities.city_id', $city_id)
            ->first();
    }

    public function getCurrencySymbol($currency)
    {
        return Country::select('symbol_native')
            ->where('currency_code', $currency)
            ->first();
    }

    public function getCurrency()
    {

        $country_id = City::where('city_id', $this->city_id)->pluck('country_id');

        $country = collect(Country::where('country_id', $country_id)->pluck('symbol_native'))->toArray();
        return $country[0];
    }

    public function getCurrencyCode()
    {

        $country_id = City::where('city_id', $this->city_id)->pluck('country_id');

        $country = collect(Country::where('country_id', $country_id)->pluck('currency_code'))->toArray();
        return $country[0];
    }

    public function getItemCollection(Request $r)
    {
        $this->setLpo($r);
        $this->fields = ['a.*', 'b.city_name as city_name', 'c.category_name as category_name'];

        $this->query = static::from($this->table)
            ->leftJoin('cities as b', 'b.city_id', '=', 'a.city_id')
            ->leftJoin('categories as c', 'c.category_id', '=', 'a.category_id');

        if ($r->searchQueryName) {
            $this->query->where('a.item_name', 'like', '%' . $r->searchQueryName . '%');
        }

        if (is_numeric($r->searchQueryCity)) {
            $this->query->where('a.city_id', $r->searchQueryCity);
        }

        if (is_numeric($r->searchQueryCategory)) {
            $this->query->where('a.category_id', $r->searchQueryCategory);
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

        $item = Item::find($this->item_id);

        if (!$item) {
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

    public function getItemsByCountry(Request $r, $country_id)
    {
        $rate = 0;

        if ($r->currency) {
            $user_currency_code = $r->currency;
            $item_currency_code = Country::where('country_id', $country_id)->first()->currency_code;

            try {
                $currencyService = new CurrencyService();
                $rate = $currencyService->convertRates($user_currency_code, $item_currency_code);
            } catch (\Exception $e) {
                $rate = 0;
            }
        } else {
            $rate = 0;
        }

        $category = Item::select('category_id')
                            ->join('cities', 'cities.city_id', 'items.city_id')
                            ->where('cities.country_id', $country_id)
                            ->groupBy('category_id')
                            ->inRandomOrder()
                            ->first();
        $items = [];
        if($category){
            $items = Item::select(
                'items.item_id',
                'items.item_name',
                'items.city_id',
                'categories.category_name',
                'cities.city_name',
                'items.custom_price_average'
            )
                        ->whereHas('details', function ($query) {
                            $query->where(function ($query) {
                                $query->whereDoesntHave('recommendation')
                                    ->orWhereHas('recommendation.user', function ($query) {
                                        $query->whereNull('disabled_at');
                                    });
                            })->where(function ($query) {
                                $query->whereDoesntHave('verifiedBusinessItem')
                                    ->orWhereHas('verifiedBusinessItem.location', function ($query) {
                                        $query->where('is_verified', true);
                                    });
                            });
                        })
                        ->join('cities', 'cities.city_id', 'items.city_id')
                        ->join('categories', 'categories.category_id', 'items.category_id')
                        ->where('cities.country_id', $country_id)
                        ->where('items.category_id', $category->category_id)
                        ->with('tags')
                        ->inRandomOrder()
                        ->take(3)
                        ->get()
                        ->map(function ($item) use ($rate) {
                            $averagePrice = $item->getAveragePrice();

                            $item->ave_local_price = bcdiv($averagePrice, 1, 2);

                            $item->converted_price = $rate > 0
                                                ? bcdiv($averagePrice, $rate, 2)
                                                : 0;

                            $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
                            $item->unsetRelation('image');

                            return $item;
                        });
        }

        return $items;
    }

    public function getItemsByKeyword(Request $r, $keyword)
    {

        $items = Item::select(
            'items.item_id',
            'items.item_name',
            'items.image_url',
            'items.city_id',
            'categories.category_name',
            DB::raw('(SELECT ROUND(SUM(item_details.price)/COUNT(item_details.price), 2)
                                FROM item_details WHERE item_details.item_id = items.item_id) as ave_local_price'),
            DB::raw('(SELECT ROUND((SUM(item_details.price)/COUNT(item_details.price))*' . $rate . ', 2 )
                                FROM item_details WHERE item_details.item_id = items.item_id) as converted_price')
        )
            ->join('cities', 'cities.city_id', 'items.city_id')
            ->join('categories', 'categories.category_id', 'items.category_id')
            ->where('cities.country_id', $country_id)
            ->where('items.category_id', $category->category_id)
            ->with('tags')
            ->inRandomOrder()
            ->take(3)->get();


        return $items;
    }

    public function searchItemsByCountry(Request $r, $country_id, $keyword)
    {
        $this->setLpo($r);
        $this->fields = ['items.*', 'b.city_id', 'b.city_name'];

        $this->query = static::from($this->table)->with('tags')->join('cities as b', 'b.city_id', '=', 'items.city_id');

        $rate = 0;

        if ($r->currency) {
            $user_currency_code = $r->currency;
            $item_currency_code = Country::where('country_id', $country_id)->first()->currency_code;

            try {
                $currencyService = new CurrencyService();
                $rate = $currencyService->convertRates($user_currency_code, $item_currency_code);
            } catch (\Exception $e) {

            }
        }

        $this->query->whereHas('details', function ($query) {
            $query->where(function ($query) {
                $query->whereDoesntHave('recommendation')
                    ->orWhereHas('recommendation.user', function ($query) {
                        $query->whereNull('disabled_at');
                    });
            })->where(function ($query) {
                $query->whereDoesntHave('verifiedBusinessItem')
                    ->orWhereHas('verifiedBusinessItem.location', function ($query) {
                        $query->where('is_verified', true);
                    });
            });
        });

        $this->query->where('items.item_name', 'like', '%' . $keyword . '%');
        $this->query->where('b.country_id', $country_id);

        $this->order_by = 'items.item_id';
        $this->order_direction = 'ASC';

        $this->assignLpo();

        return $this->query->get($this->fields)->map(function ($item) use ($rate) {
            $averagePrice = $item->getAveragePrice();

            $item->ave_local_price = bcdiv($averagePrice, 1, 2);

            $item->converted_price = $rate > 0
                                ? bcdiv($averagePrice, $rate, 2)
                                : 0;

            $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
            $item->unsetRelation('image');

            return $item;
        });
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

    public function category()
    {
        return $this->hasMany(Category::class, 'category_id', 'category_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    public function details()
    {
        return $this->hasMany(ItemDetail::class, 'item_id', 'item_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'item_tags', 'item_id', 'tag_id')->withTimestamps();
    }

    public function watchlist()
    {
        return $this->belongsToMany(Users::class)->withTimestamps();
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'item_id', 'item_id');
    }

    public function getAveragePrice(): string
    {
        $customAveragePrice = $this->custom_price_average;

        if (filled($customAveragePrice)) return bcdiv($customAveragePrice, 1, 2);

        $prices = $this->details()
            ->withoutDisabledRecommendation()
            ->withoutUnverifiedBusiness()
            ->get();

        $pricesCount = $prices->count();

        if ($pricesCount == 1) return bcdiv($prices->pluck('price')->first(), 1, 2);
        if ($pricesCount == 2) return bcdiv($prices->pluck('price')->avg(), 1, 2);
        if ($pricesCount == 3) return bcdiv($prices->pluck('price')->slice(2, 1)->first(), 1, 2);

        return bcdiv(
                $this->getPriceQuartile($prices, QuartileType::SECOND_QUARTILE),
                1,
                2
            );
    }

    public function getFirstQuartilePrice(): string
    {
        $customPriceLow = $this->custom_price_low;

        if (filled($customPriceLow)) return bcdiv($customPriceLow, 1, 2);

        $prices = $this->details()
            ->withoutDisabledRecommendation()
            ->withoutUnverifiedBusiness()
            ->get();
        
        $pricesCount = $prices->count();

        if ($pricesCount == 1) return bcdiv($prices->pluck('price')->first(), 1, 2);
        if ($pricesCount <= 3) return bcdiv($prices->sortBy('price')->pluck('price')->first(), 1, 2);

        return bcdiv(
                $this->getPriceQuartile($prices, QuartileType::FIRST_QUARTILE),
                1,
                2
            );
    }

    public function getThirdQuartilePrice(): string
    {
        $customPriceHigh = $this->custom_price_high;

        if (filled($customPriceHigh)) return bcdiv($customPriceHigh, 1, 2);

        $prices = $this->details()
            ->withoutDisabledRecommendation()
            ->withoutUnverifiedBusiness()
            ->get();

        $pricesCount = $prices->count();

        if ($pricesCount == 1) return bcdiv($prices->pluck('price')->first(), 1, 2);
        if ($pricesCount <= 3) return bcdiv($prices->sortBy('price')->pluck('price')->last(), 1, 2);

        return bcdiv(
                $this->getPriceQuartile($prices, QuartileType::THIRD_QUARTILE),
                1,
                2
            );
    }

    private function getPriceQuartile(Collection $itemDetails, float $quartile)
    {
        if (! $itemDetails->count()) return 0;

        $sorted = $itemDetails->sortBy('price');

        $pos    = ($sorted->count() - 1) * $quartile;
        $base   = floor($pos);
        $rest   = $pos - $base;

        $array  = $sorted->pluck('price')->toArray(); 

        if (isset($array[$base + 1])) {
            return $array[$base] + $rest * ($array[$base + 1] - $array[$base]);
        }

        return $array[$base];
    }
}
