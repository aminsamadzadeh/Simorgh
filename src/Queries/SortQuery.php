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
        return 'asc';
    }

}
