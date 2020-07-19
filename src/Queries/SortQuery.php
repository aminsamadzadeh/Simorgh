<?php

namespace AminSamadzadeh\Simorgh\Queries;

class SortQuery extends Query
{
    public function build()
    {
        $this->query->orderBy($this->value, $this->getSortOrder());
    }

    public function validate()
    {
        return $this->key == 'sort';
    }

    protected function getSortOrder()
    {
        if(!array_key_exists('sort-order', $this->meta))
            return 'asc';

        if(!in_array($this->meta['sort-order'], ['asc', 'desc']))
            return 'asc';

        return $this->meta['sort-order'];
    }

}
