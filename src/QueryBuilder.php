<?php

namespace AminSamadzadeh\Simorgh;

class QueryBuilder
{

    private static $queryOrders =
        [
            'SortQuery',
            'RelationalArrayQuery',
            'RelationalQuery',
            'ArrayQuery',
            'IntervalQuery',
            'SimpleQuery'
        ];

    public static function build(&$query, $key, $value)
    {
        $queriesObjects = [];
        foreach ( self::$queryOrders as $queryClass ){
            $class = __NAMESPACE__."\\Queries\\{$queryClass}";
            array_push($queriesObjects, new $class($query, $key, $value));
        }

        for($i = 0; $i < count($queriesObjects); $i++)
        {
            if(isset($queriesObjects[$i+1]))
                $queriesObjects[$i]->setNext($queriesObjects[$i+1]);
        }

        $queriesObjects[0]->handle();
    }
}
