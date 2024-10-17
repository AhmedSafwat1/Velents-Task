<?php

namespace App\Support\Filter\Handler\Order;

use App\Support\Filter\BaseFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ToFilter extends BaseFilter
{
    /**
     * Filter records based on a given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder instance.
     * @param string $value The resolved value of the filtration key sent in the query string.
     *
     * @return void
     */
    public function filter(Builder $builder, $value)
    {
        if ($value) {
            $builder
                 ->where("created_at", "<=", $value . " 23:59:.59")
            ;
            ;
        }
    }
}
