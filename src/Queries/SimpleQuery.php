<?php

namespace AminSamadzadeh\Simorgh\Queries;

class SimpleQuery extends Query
{
    public function build()
    {
        if($this->getOperator() == 'like')
            return $this->likeWhere();

        return $this->eqlWhere();
    }

    protected function eqlWhere()
    {
        $this->query->where($this->key, $this->getOperator(), $this->value);
    }

    protected function likeWhere()
    {
        $this->query->where($this->key, $this->getOperator(), "%{$this->value}%");
    }
    public function validate()
    {
        return true;
    }

}
