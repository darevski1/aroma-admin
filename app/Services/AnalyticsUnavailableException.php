<?php

namespace App\Services;

use RuntimeException;
use Throwable;

/** Google Analytics cannot be used right now; the message is safe to show to an admin. */
class AnalyticsUnavailableException extends RuntimeException
{
    public function __construct(string $message, public readonly string $reason = 'api_error', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
