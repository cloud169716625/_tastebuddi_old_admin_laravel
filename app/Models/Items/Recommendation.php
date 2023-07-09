<?php

namespace App\Models\Items;

use App\Models\BaseModel;
use App\Models\Traits\CanBeReported;
use App\Models\Users\Users;
use App\Scopes\RecommendationItemDetailApprovedScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Recommendation extends BaseModel
{
    use CanBeReported;
    use SoftDeletes;

    protected $table = 'recommendations';

    protected $primaryKey = 'recommendation_id';

    public $timestamps = true;

    protected $guarded = ['recommendation_id'];
    protected $hidden = ['created_at', 'updated_at'];


    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];


    public function getRecommendationsByCountry($country_id)
    {
        $recommendations = Recommendation::select(
            'recommendations.recommendation_id',
            'recommendations.item_id',
            'items.item_name',
            'recommendations.recommended_price',
            'items.city_id',
            'cities.city_name'
            )
            ->whereHas('user', function ($query) {
                $query->whereNull('disabled_at');
            })
            ->join('items', 'items.item_id', 'recommendations.item_id')
            ->join('cities', 'cities.city_id', 'items.city_id')
            ->where('cities.country_id', $country_id)
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->map(function ($recommendation) {
                $recommendation['image_url'] = ($recommendation->item->image)
                                            ? $recommendation->item->image->getFullUrl()
                                            : null;

                $recommendation->unsetRelation('item');

                return $recommendation;
            });

        return $recommendations;
    }

    public function getUserRecommendations(Request $r)
    {
        $this->setLpo($r);
        $this->fields = ['recommendations.user_id', 'b.item_id', 'b.item_name',
                         'c.city_id', 'c.location_id', 'c.place_id',
                         'c.location as location_name',
                         'c.lat_coordinate as latitude',
                         'c.lng_coordinate as longitude',
                         'c.address', 'recommendations.recommended_price'];

        if ($r->country_id) {
            $this->query = static::from($this->table . ' as recommendations')
                            ->join('locations as c', 'recommendations.location_id', 'c.location_id')
                            ->join('cities as d', 'c.city_id', 'd.city_id')
                            ->join('items as b', 'b.item_id', 'recommendations.item_id');
            $this->query->where('d.country_id', $r->country_id);
        } else {
            $this->query = static::from($this->table . ' as recommendations')
                            ->join('locations as c', 'recommendations.location_id', 'c.location_id')
                            ->join('items as b', 'b.item_id', 'recommendations.item_id');
        }

        $this->query->where('recommendations.user_id', $r->user_id);

        $this->order_by = 'recommendations.item_id';
        $this->order_direction = 'ASC';

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
        return $this->query->get($this->fields)->map(function ($recommendation) {
            $recommendation['image_url'] = ($recommendation->item->image)
                                         ? $recommendation->item->image->getFullUrl()
                                         : null;

            $recommendation->unsetRelation('item');

            return $recommendation;
        });
    }



    public function saveNewItemAsRecommendation(Request $request)
    {

        $location = Location::where('place_id', $request->place_id)->first();
        if (!$location) {
            $location = new Location;
            $location = $location->saveNewLocation($request->place_id);
        }

        $category = Category::where('category_name', 'Recommendations')->first();
        if (!$category) {
            $category = new Category();
            $category->category_name = "Recommendations";
            $category->save();
        }

        $item = Item::query()
                    ->where('item_name', $request->item_name)
                    ->where('city_id', $location->city_id)
                    ->first();

        if (!$item) {
            $item = new Item();
            $item->item_name = $request->item_name;
            $item->city_id = $location->city_id;
            $item->category_id = $category->category_id;
            $item->save();
        }

        $recommendation = new Recommendation;
        $recommendation->user_id = (int) $request->user_id;
        $recommendation->item_id = $item->item_id;
        $recommendation->location_id = $location->location_id;
        $recommendation->recommended_price = (double) $request->recommended_price;
        $recommendation->save();

        $details = new ItemDetail;
        $details->item_id = $item->item_id;
        $details->location_id = $location->location_id;
        $details->price = $request->recommended_price;
        $details->recommendation_id = $recommendation->recommendation_id;
        $details->save();

        return $recommendation;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function suggestedPrice(): HasOne
    {
        return $this->hasOne(ItemDetail::class, 'recommendation_id', 'recommendation_id');
    }

    /**
     * Scope
     */
    public function scopeAllSuggestedPrice(Builder $builder)
    {
        return $builder->withoutGlobalScope(new RecommendationItemDetailApprovedScope);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new RecommendationItemDetailApprovedScope);
    }
}
