<?php

namespace AminSamadzadeh\Simorgh\Queries;

interface Handler
{
    public function setNext(Handler $handler): Handler;

    public function handle();
}
