<?php

declare(strict_types=1);

namespace Epic64\PhpBox\TryMonad;

use Throwable;

/**
 * @template T
 * @extends Trial<T>
 */
final class Success extends Trial
{
    /**
     * @param T $value
     */
    public function __construct(private mixed $value)
    {
    }

    public function isSuccess(): bool
    {
        return true;
    }

    public function isFailure(): bool
    {
        return false;
    }

    /**
     * @template U
     * @param callable(T):U $f
     * @return Success<U>|Failure<mixed>
     */
    public function map(callable $f): Success | Failure
    {
        try {
            $value = $f($this->value);
            return new Success($value);
        } catch (Throwable $e) {
            return new Failure($e);
        }
    }

    public function flatMap(callable $f): Trial
    {
        return $f($this->value);
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function getOrElse(mixed $default): mixed
    {
        return $this->value;
    }
}
