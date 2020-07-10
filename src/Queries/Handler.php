<?php

namespace Amin\Simorgh\Queries;

interface Handler
{
    public function setNext(Handler $handler): Handler;

    public function handle();
}
