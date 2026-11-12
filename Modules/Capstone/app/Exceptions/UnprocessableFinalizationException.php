<?php

namespace Modules\Capstone\Exceptions;

use InvalidArgumentException;

class UnprocessableFinalizationException extends InvalidArgumentException
{
    /** @param array<string, mixed> $details */
    public function __construct(string $message, public readonly array $details = [])
    {
        parent::__construct($message);
    }
}
