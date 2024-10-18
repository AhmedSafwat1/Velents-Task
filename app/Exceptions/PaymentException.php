<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    protected $statusCode;

    // Constructor to accept a custom message and status code
    public function __construct($message = 'Payment failed', $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }
}
