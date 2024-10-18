<?php

namespace App\Support\Filter;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Scope a query to use filters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder  Eloquent builder isntance.
     * @param  array  $filters  Array of filters to use. Structure: ["filter-name" => FilterClass::class]
     * @return void
     */
    public function scopeFilter(Builder $builder, array $filterRequest, array $filters = [])
    {

        (new FiltrationEngine($builder, $filterRequest))->plugFilters($filters)->run();
    }
}
