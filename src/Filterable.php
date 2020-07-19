<?php

namespace AminSamadzadeh\Simorgh;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait Filterable
{

    protected $filters = [];
    protected $filterName = "filter";

    public function scopeFilter($query, Request $request)
    {
        $attributes = $request->input($this->filterName);
        return $this->buildQuery($query, $attributes);
    }

    protected function buildQuery($query, $attributes = null)
    {
        if(is_null($attributes))
            return;

        foreach($this->filterableFromArray($attributes) as $key => $value)
            QueryBuilder::build($query, $key, $value);
    }


    public function getFilterable()
    {
        if(isset($this->filterable))
            return $this->filterable;
        return [];
    }


    protected function filterableFromArray(array $attributes)
    {
        $attributes = array_filter($attributes);

        if (count($this->getFilterable()) > 0) {
            return
                array_intersect_key($attributes, array_flip($this->getFilterable()))
                + $this->skipSort($attributes);
        }

        return
            array_filter(
                $attributes,
                array($this, 'schemaHasColumn'),
                ARRAY_FILTER_USE_KEY
            ) + $this->skipSort($attributes);
    }

    protected function skipSort(array $attributes)
    {
        if(array_key_exists('sort', $attributes))
            return ['sort' => $attributes['sort']];
        return [];
    }

    public function schemaHasColumn($key)
    {
        return Schema::hasColumn($this->getTable(), $key);
    }
}
