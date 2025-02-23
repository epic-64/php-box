<?php

declare(strict_types=1);

namespace Epic64\PhpBox\TryMonad;

use Throwable;

/**
 * @template T
 * @extends Trial<T>
 */
final class Failure extends Trial
{
    /**
     * @param Throwable $exception
     */
    public function __construct(private readonly Throwable $exception)
    {
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function isFailure(): bool
    {
        return true;
    }

    /**
     * @return Failure<T>
     */
    public function map(callable $f): Failure
    {
        return $this; // Failure propagates without applying function
    }

    public function flatMap(callable $f): Trial
    {
        return $this; // Failure propagates without applying function
    }

    public function get(): mixed
    {
        throw $this->exception;
    }

    public function getOrElse(mixed $default): mixed
    {
        return $default;
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }
}
