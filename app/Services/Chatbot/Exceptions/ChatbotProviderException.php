<?php

namespace App\Services\Chatbot\Exceptions;

use RuntimeException;

/**
 * A provider could not produce an answer (unreachable, misconfigured, API
 * error, limit reached). Messages are written for administrators and never
 * contain credentials.
 */
class ChatbotProviderException extends RuntimeException
{
    public static function make(string $provider, string $message): self
    {
        return new self("[{$provider}] {$message}");
    }
}
