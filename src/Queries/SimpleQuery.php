<?php

namespace Amin\Simorgh\Queries;

class SimpleQuery extends Query
{
    public function build()
    {
        $this->query->where($this->key, $this->value);
    }

    public function validate()
    {
        return true;
    }

}
