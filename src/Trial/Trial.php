<?php

declare(strict_types=1);

namespace Epic64\PhpBox\Trial;

use Throwable;

/**
 * @template T
 */
abstract class Trial
{
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
