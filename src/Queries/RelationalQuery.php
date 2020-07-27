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
        $op = $this->getOperator();

        $this->query->whereHas($relation,
            function ($q) use ($key, $value, $op) {
                if($op == 'like')
                    $q->where($key, $op, "%{$value}%");
                else
                    $q->where($key, $op, $value);
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
