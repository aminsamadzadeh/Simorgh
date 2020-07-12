<?php

namespace AminSamadzadeh\Simorgh\Queries;

class IntervalQuery extends Query
{
    public function build()
    {
        $interval = $this->getInterval();
        $this->query->whereBetween($this->key, [$interval[2], $interval[3]]);
    }

    public function validate()
    {
        return
            $this->getInterval() !== false;
    }

    protected function getInterval()
    {
        $pattern = '/^(\[|\()(.*),(.*)(\]|\))$/';
        if (preg_match($pattern, $this->value, $match))
        {
            if(is_null($match[2]) and is_null($match[3]))
                return false;

            return $match;
        }
        return false;
    }
}
