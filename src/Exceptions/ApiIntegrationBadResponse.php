<?php

namespace App\Exceptions;

class ApiIntegrationBadResponse extends \Exception
{
    public function __construct($message = 'Bad response from API', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}