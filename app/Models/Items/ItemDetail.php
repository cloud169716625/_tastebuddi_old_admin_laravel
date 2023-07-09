<?php

namespace App\Models\Items;

use App\Models\Users\Users;
use App\Models\VerifiedBusinessItem;
use App\Scopes\ItemDetailApprovedScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ItemDetail extends Model
{
    protected $table = 'item_details';
    protected $primaryKey = 'item_detail_id';
    protected $guarded = ['item_detail_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'items');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'locations');
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class, 'recommendation_id', 'recommendation_id');
    }

    public function verifiedBusinessItem(): HasOne
    {
        return $this->hasOne(VerifiedBusinessItem::class, 'item_detail_id', 'item_detail_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    /**
     * Scope
     */
    public function scopeWithAllStatus(Builder $builder)
    {
        return $builder->withoutGlobalScope(new ItemDetailApprovedScope);
    }

    public function scopeWithoutDisabledRecommendation(Builder $builder): void
    {
        $builder->where(function ($query) {
            $query->whereDoesntHave('recommendation')
                ->orWhereHas('recommendation.user', function ($query) {
                    $query->whereNull('disabled_at');
                });
        });
    }

    public function scopeWithoutUnverifiedBusiness(Builder $builder): void
    {
        $builder->where(function ($query) {
            $query->whereDoesntHave('verifiedBusinessItem')
                ->orWhereHas('verifiedBusinessItem.location', function ($query) {
                    $query->where('is_verified', true);
                });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ItemDetailApprovedScope);
    }
}
