<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TooManyRequestsException extends HttpException
{
    public function __construct(string $message = 'Too many requests.')
    {
        parent::__construct($message, 429);
    }
}
