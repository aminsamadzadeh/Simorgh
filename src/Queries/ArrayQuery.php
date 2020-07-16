<?php

namespace AminSamadzadeh\Simorgh\Queries;

class ArrayQuery extends Query
{
    public function build()
    {
        $this->query->whereIn($this->key, $this->value);
    }

    public function validate()
    {
        return is_array($this->value);
    }

}
