<?php

namespace AminSamadzadeh\Simorgh;

use Illuminate\Support\Facades\Schema;

trait Filterable
{

    protected $filterName = "filter";
    protected $request;
    protected $filterQuery;

    public function scopeFilter($query, $request)
    {
        $this->request = $request;
        $this->filterQuery = $query;
        return $this->buildQuery();
    }

    protected function buildQuery()
    {
        $attributes = $this->getRequestAttributes();
        if(is_null($attributes))
            return $this->filterQuery;

        $meta = $this->getFilterMeta();

        foreach($this->filterableFromArray() as $key => $value)
            QueryBuilder::build($this->filterQuery, $key, $value, $meta);
    }

    public function getFilterable()
    {
        if(isset($this->filterable))
            return $this->filterable;
        return [];
    }

    public function getRequestAttributes()
    {
        if(isset($this->request[$this->filterName]))
            return
                array_filter($this->request[$this->filterName]);
    }

    public function getFilterMeta()
    {
        return
            array_merge($this->getDefaultMeta(), $this->getRequestMeta());
    }

    protected function getDefaultMeta()
    {
        return $this->filterMeta ?? [];
    }

    protected function getRequestMeta()
    {
        if(
            isset($this->request['filter-meta'])
            and is_array($this->request['filter-meta'])
        )
            return $this->request['filter-meta'];

        return [];
    }

    protected function filterableFromArray()
    {
        $attributes = $this->getRequestAttributes();

        if (count($this->getFilterable()) > 0) {
            return
                array_intersect_key($attributes, array_flip($this->getFilterable()))
                + $this->skipSort();
        }

        return
            array_filter(
                $attributes,
                array($this, 'schemaHasColumn'),
                ARRAY_FILTER_USE_KEY
            ) + $this->skipSort();
    }

    protected function skipSort()
    {
        $attributes = $this->getRequestAttributes();
        if(array_key_exists('sort', $attributes))
            return ['sort' => $attributes['sort']];
        return [];
    }

    public function schemaHasColumn($key)
    {
        return Schema::hasColumn($this->getTable(), $key);
    }

}
