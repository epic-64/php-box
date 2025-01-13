<?php

declare(strict_types=1);

namespace Epic64\PhpBox;

use LogicException;

/**
 * A container that allows chaining transformations and assertions on a value.
 *
 * @template T of mixed
 */
class Box
{
    /**
     * @template U
     * @param U $value
     * @return Box<U>
     */
    public static function of($value): Box
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
     * Caution: This method will reuse values passed by reference (e.g. objects) to minimize performance overhead.
     * For a side effect free version, use pure() instead.
     *
     * @template U
     * @param callable(T): U $callback
     * @return Box<U>
     */
    public function map(callable $callback): Box
    {
        return new self($callback($this->value));
    }

    /**
     * Modify the box itself via a callback.
     *
     * Useful grouping multiple calls on a box into one call (e.g. for common validation rules).
     *
     * @template U
     * @param callable(self<T>): Box<U> $callback
     * @return Box<U>
     */
    public function mod(callable $callback): Box
    {
        return $callback($this);
    }

    /**
     * Unwrap the value and apply a final transformation on it.
     * Can be used instead of `unbox` to terminate the sequence.
     *
     * @template U
     * @param callable(T): U $callback
     * @return U
     */
    public function get(callable $callback)
    {
        return $callback($this->value());
    }

    /**
     * Unwrap the final value.
     *
     * @return T
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Run an assertion against the value.
     * Example of a simple strict equality check: ->assert(5)
     * Example of a callback check:               ->assert(fn($x) => $x > 5)
     *
     * @template U
     * @param U|callable(T):bool $check
     * @return Box<T>
     */
    public function assert(mixed $check, string $message = ''): Box
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

    /**
     * Run an assertion against the value and return it.
     *
     * @template U
     * @param U|callable(T):bool $check
     * @return T
     */
    public function assertGet(mixed $check): mixed
    {
        $this->assert($check);

        return $this->value();
    }

    /**
     * Dump the value to the console.
     *
     * @return Box<T>
     */
    public function dump(?string $message = null): Box
    {
        if ($message !== null) {
            echo $message . ': '; // @phpstan-ignore ekinoBannedCode.expression
        }

        var_dump($this->value); // @phpstan-ignore ekinoBannedCode.function

        return $this;
    }
}