<?php

namespace Epic64\PhpBox;

use LogicException;
use Throwable;

/**
 * A container that allows chaining transformations and assertions on a value.
 * Will catch exceptions and return them as a value.
 *
 * @template T
 * @template E
 */
class TryBox
{
    /**
     * @template U
     * @param U $value
     * @return TryBox<U, null>
     */
    public static function of($value): TryBox
    {
        /** @var TryBox<U, null> $box */
        $box = new self($value, null);

        return $box;
    }

    /**
     * @param T $value
     */
    public function __construct(
        private $value,
        private ?Throwable $error = null
    ) {
    }

    /**
     * Apply a transformation function to the value.
     *
     * @template U
     * @param callable(T): U $callback
     * @return TryBox<U, null>|TryBox<T, Throwable>
     */
    public function map(callable $callback): TryBox
    {
        if ($this->error !== null) {
            return $this;
        }

        return $this->try($callback);
    }

    /**
     * Unbox the value, which might be anything including a throwable.
     *
     * @return Throwable|T
     */
    public function value()
    {
        return $this->error ?? $this->value;
    }

    /**
     * @return T
     *
     * @throws Throwable
     */
    public function rip()
    {
        if ($this->error !== null) {
            throw $this->error;
        }

        return $this->value;
    }

    /**
     * @template U
     * @param callable(T):U $callback
     * @return TryBox<U, null>|TryBox<T, Throwable>
     */
    private function try(callable $callback): TryBox
    {
        try {
            /** @var TryBox<U, null> $result */
            $result = new self($callback($this->value), null);
        } catch (Throwable $e) {
            /** @var TryBox<T, Throwable> $result */
            $result = new self($this->value, $e);
        }

        return $result;
    }
}