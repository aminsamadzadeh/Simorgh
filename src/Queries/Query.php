<?php

namespace AminSamadzadeh\Simorgh\Queries;

use Illuminate\Support\Facades\Schema;

abstract class Query implements Handler
{
    protected $query;
    protected $key;
    protected $value;
    protected $meta;
    private $nextHandler;
    protected $validOperators = ['like', '='];

    public function __construct(&$query, $key, $value, $meta = null)
    {
        $this->query = $query;
        $this->key = $key;
        $this->value = $value;
        $this->meta = $meta ?? [];
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

    public function getOperator()
    {
        if($this->isSetOperator())
            return $this->meta[$this->key]['op'];

        return '=';
    }

    protected function isSetOperator()
    {
        return
            isset($this->meta[$this->key]['op'])
            and in_array($this->meta[$this->key]['op'], $this->validOperators);
    }

}
