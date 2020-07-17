<?php

namespace AminSamadzadeh\Simorgh\Queries;

class RelationalQuery extends Query
{
    public function build()
    {
        $relational = $this->getRelational();
        $key = array_pop($relational);
        $relation = implode('.', $relational);
        $value = $this->value;

        $this->query->whereHas($relation,
            function ($q) use ($key, $value) {
                $q->where($key, $value);
            }
        );

    }

    public function validate()
    {
        return
            $this->getRelational() !== false;
    }

    protected function getRelational()
    {
        $res = explode('.', $this->key);
        if (count($res) > 1) {
            return $res;
        }
        return false;
    }
}
