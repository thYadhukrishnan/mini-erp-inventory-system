<?php

namespace App\Exceptions;

use DomainException;

class InvalidStockQuantityException extends DomainException
{
    public function __construct(string $message = 'Stock quantity must be greater than zero.')
    {
        parent::__construct($message);
    }
}
