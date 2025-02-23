<?php

declare(strict_types=1);

namespace Epic64\PhpBox\TryMonad;

use Throwable;

/**
 * @template T
 */
abstract class Trial
{
    /**
     * @template U
     * @param callable(T):U $f
     * @return Success<U>|Failure<Throwable>
     */
    abstract public function map(callable $f): mixed;

    /**
     * @template U
     * @param callable(T):Trial<U> $f
     * @return Trial<U>
     */
    abstract public function flatMap(callable $f): Trial;

    /**
     * @return bool
     */
    abstract public function isSuccess(): bool;

    /**
     * @return bool
     */
    abstract public function isFailure(): bool;

    /**
     * @return T
     */
    abstract public function get(): mixed;

    /**
     * @param T $default
     * @return T
     */
    abstract public function getOrElse(mixed $default): mixed;
}
