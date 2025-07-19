<?php

namespace Smartpings\Messaging;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class LoggingService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Logs an error message.
     */
    protected function logError(string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }

    /**
     * Logs an exception.
     */
    protected function logException(\Exception $exception, string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->critical($message, array_merge($context, [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]));
        }
    }
}
