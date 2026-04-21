<?php

declare(strict_types=1);

namespace App\Exceptions;

final class MethodNotAllowedException extends HttpException
{
    public function __construct(array $allowedMethods)
    {
        parent::__construct('Method not allowed.', 405);
    }
}
