<?php

namespace Epic64\PhpBox;

use LogicException;
use Throwable;

/**
 * A container that allows chaining transformations and assertions on a value.
 * Will catch exceptions and return them as a value.
 *
 * @template T
 */
class TryBox
{
    /**
     * @template U
     * @param U $value
     * @return TryBox<U>
     */
    public static function of($value): TryBox
    {
        return new self($value);
    }

    /**
     * @param T $value
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * Apply a transformation function to the value.
     *
     * @template U
     * @param callable(T): U $callback
     * @return TryBox<U>|TryBox<Throwable>
     */
    public function map(callable $callback): TryBox
    {
        try {
            return new self($callback($this->value));
        } catch (Throwable $e) {
            return new self($e);
        }
    }

    /**
     * Apply a transformation function to the box itself
     *
     * This method will always return a new instance of Box, even for objects.
     *
     * @template U
     * @param callable(self<T>): TryBox<U> $callback
     * @return TryBox<U>|TryBox<Throwable>
     */
    public function flatMap(callable $callback): TryBox
    {
        try {
            return $callback($this);
        } catch (Throwable $e) {
            return new self($e);
        }
    }

    /**
     * Unbox the value.
     *
     * @return T|Throwable
     */
    public function unbox(): mixed
    {
        return $this->value;
    }

    /**
     * Unwrap the value and apply a final transformation on it.
     * Can be used instead of `unbox` to terminate the sequence.
     *
     * @template U
     * @param callable(T): U $callback
     * @return U
     *
     * @throws Throwable
     */
    public function get(callable $callback)
    {
        if ($this->value instanceof Throwable) {
            throw $this->value;
        }

        return $callback($this->value);
    }

    /**
     * Assert that the value is equal to the expected value.
     *
     * @param T $expected
     * @return TryBox<T>|TryBox<Throwable>
     */
    public function assert(mixed $expected): TryBox
    {
        try {
            return $this->performAssertion($expected);
        } catch (\Throwable $e) {
            return new self($e);
        }
    }

    /**
     * Run an assertion against the value.
     * Example of a simple strict equality check: ->assert(5)
     * Example of a callback check:               ->assert(fn($x) => $x > 5)
     *
     * @template U
     * @param U|callable(T):bool $check
     * @return TryBox<T>|TryBox<Throwable>
     */
    public function performAssertion(mixed $check, string $message = ''): TryBox
    {
        $isClosure = is_callable($check);

        $pass = $isClosure
            ? $check($this->value)
            : $this->value === $check;

        if (! $pass) {
            $report = $isClosure
                ? 'Value did not pass the callback check.'
                : sprintf(
                    'Failed asserting that two values are the same. Expected %s, got %s.',
                    var_export($check, true),
                    var_export($this->value, true)
                );

            if ($message !== '') {
                $report = $message . ' | ' . $report;
            }

            throw new LogicException($report);
        }

        return $this;
    }
}