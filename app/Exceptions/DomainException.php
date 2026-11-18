<?php

namespace App\Exceptions;

use RuntimeException;

class DomainException extends RuntimeException
{
    public function __construct(string $message, public string $errorCode = 'domain_error')
    {
        parent::__construct($message);
    }
}
