<?php

namespace AminSamadzadeh\Simorgh\Queries;

use Illuminate\Support\Facades\Schema;

abstract class Query implements Handler
{
    protected $query;
    protected $key;
    protected $value;
    private $nextHandler;

    public function __construct(&$query, $key, $value)
    {
        $this->query = $query;
        $this->key = $key;
        $this->value = $value;
    }

    abstract public function build();

    abstract public function validate();

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle()
    {
        if($this->validate()){
            $this->build();
            return null;
        }

        if ($this->nextHandler)
            return $this->nextHandler->handle();

        return null;
    }

}
