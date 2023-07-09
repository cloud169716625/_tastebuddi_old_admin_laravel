<?php

namespace App\Scopes;

use App\Enums\ItemDetailStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RecommendationItemDetailApprovedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('suggestedPrice');
    }
}
