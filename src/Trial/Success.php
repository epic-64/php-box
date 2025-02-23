<?php

declare(strict_types=1);

namespace Epic64\PhpBox\Trial;

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
     * @return Success<U>|Failure<Throwable>
     */
    public function map(callable $f): Success | Failure
    {
        try {
            return new Success($f($this->value));
        } catch (Throwable $e) {
            /** @var Failure<Throwable> $failure */
            $failure = new Failure($e);

            return $failure;
        }
    }

    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function getOrElse(mixed $default): mixed
    {
        return $this->value;
    }
}
