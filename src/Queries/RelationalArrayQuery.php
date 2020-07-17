<?php

namespace AminSamadzadeh\Simorgh\Queries;

class RelationalArrayQuery extends Query
{
    public function build()
    {
        $relational = $this->getRelational();
        $key = array_pop($relational);
        $relation = implode('.', $relational);
        $value = $this->value;

        $this->query->whereHas($relation,
            function ($q) use ($key, $value) {
                $q->whereIn($key, $value);
            }
        );

    }

    public function validate()
    {
        return
            $this->getRelationalArray() !== false;
    }

    protected function getRelationalArray()
    {
        $res = explode('.', $this->key);
        if (count($res) > 1 and is_array($this->value)) {
            return $res;
        }
        return false;
    }
}
