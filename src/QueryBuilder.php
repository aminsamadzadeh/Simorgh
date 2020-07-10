<?php

namespace Amin\Simorgh;

class QueryBuilder
{

    private static $queryOrders =
        [
            'SimpleQuery'
        ];

    public static function build(&$query, $key, $value)
    {
        $queriesObjects = [];
        foreach ( self::$queryOrders as $queryClass ){
            $class = __NAMESPACE__."\\Queries\\{$queryClass}";
            array_push($queriesObjects, new $class($query, $key, $value));
        }

        $firstHandler = array_pop($queriesObjects);

        $handler = $firstHandler;
        foreach($queriesObjects as $object)
            $handler = $handler->setNext($object);

        $firstHandler->handle();
    }
}
