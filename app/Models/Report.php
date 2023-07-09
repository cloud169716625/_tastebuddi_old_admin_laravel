<?php

namespace App\Models;

use App\Enums\MediaCollectionType;
use App\Models\Traits\InteractsWithReportableTypes;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Report extends BaseModel implements HasMedia
{
    use HasMediaTrait;
    use InteractsWithReportableTypes;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reported_by',
        'description'
    ];

    protected $appends = [
        'report_type'
    ];

    /**
     * Register a Medica Collection callback for set up image converion properties.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionType::REPORT_ATTACHMENTS)
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')->width(254);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reportable()
    {
        return $this->morphTo()->withTrashed();
    }


    /**
     * Define an inverse one-to-one or many relationship the reason the report was filed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reason()
    {
        return $this->belongsTo(ReportCategory::class, 'reason_id');
    }

    /**
     * Define an inverse one-to-one or many relationship to the User who filed the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reporter()
    {
        return $this->belongsTo(Users::class, 'reported_by');
    }

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Media::class, 'model_id')
            ->where('collection_name', MediaCollectionType::REPORT_ATTACHMENTS);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * return the reportable type it's friendly name.
     *
     * @param string $value
     * @return string
     */
    public function getReportTypeAttribute()
    {
        $type = array_search($this->reportable_type, array_merge($this->getReportableTypes(), Relation::$morphMap));

        if ($type !== false) {
            return $type;
        }

        return $this->reportable_type;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * A wrapper to filter result by reportable type.
     *
     * @param Builder $query
     * @param string|array $type
     * @return void
     */
    public function scopeHasReportType(Builder $query, $types)
    {
        $query->hasMorph('reportable', Arr::only($this->getReportableTypes(), $types));
    }

    /**
     * Filter result by search query.
     *
     * @todo Improve Search performance for a large dataset.
     *
     * @param Builder $query
     * @param string $search
     * @return void
     */
    public function scopeSearch(Builder $query, string $search)
    {
        $query->whereHas('reporter', function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }
}
