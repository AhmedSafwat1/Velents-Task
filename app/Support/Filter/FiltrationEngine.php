<?php

namespace App\Support\Filter;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Database\Eloquent\Builder;

class FiltrationEngine
{
    /**
     * Request  Filters .
     *
     * @var array $requestFilters
     */
    protected $requestFilters;

    /**
     * Builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
    * Array of filters to be applied.
    *
    * @var array
    */
    protected $filters = [
    ];



    /**
     * Constructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder instance.
     * @param  @var array $requestFilters.
     */
    public function __construct(Builder $builder, array $requestFilters)
    {
        $this->builder = $builder;
        $this->requestFilters = $requestFilters;
    }

    /**
     * Add filters to the engine.
     *
     * @param array $filters Array of filters to add to the engine. Structure: ["filter-name" => FilterClass::class]
     *
     * @return FiltrationEngine
     */
    public function plugFilters(array $filters = [])
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Apply the filters on the builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->relevantFilters() as $filterName => $value) {
            $filter = $this->resolveFilter($filterName);
            $value =  !is_array($value) ? $filter->getMappings()[$value] ?? $value : $value;
            $filter->filter($this->builder, $value);
        }
    }

    /**
     * Get a new filter instance using a given filter name.
     *
     * @param string $filter The filter name.
     *
     * @return \App\Support\Filter\BaseFilter
     */
    public function resolveFilter($filter)
    {
        if (!isset($this->filters[$filter])) {
            throw new Exception("Could not resolve filter associated with name: '{$filter}'");
        }

        return new $this->filters[$filter]();
    }

    /**
     * Extract the relevant (key, value) pairs from the query string based on the
     * filters in this filtration engine instance.
     *
     * @return array
     */
    protected function relevantFilters()
    {
        return Arr::only($this->requestFilters, array_keys($this->filters));
    }
}
