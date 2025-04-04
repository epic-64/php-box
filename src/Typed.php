<?php

declare(strict_types=1);

namespace Epic64\PhpBox;

use RuntimeException;

readonly class Typed
{
    public function __construct(private mixed $value)
    {
    }

    public static function of(mixed $value): self
    {
        return new self($value);
    }

    /**
     * Get the value as a specific type.
     * @template U
     *
     * @param class-string<U> $type
     *
     * @return U
     *
     */
    public function asClass(string $type)
    {
        if ($this->value instanceof $type) {
            return $this->value;
        }

        throw new RuntimeException("Value is not of type {$type}");
    }

    public function asInt(): int
    {
        if (is_int($this->value)) {
            return $this->value;
        }

        throw new RuntimeException('Value is not of type int');
    }

    public function asString(): string
    {
        if (is_string($this->value)) {
            return $this->value;
        }

        throw new RuntimeException('Value is not of type string');
    }

    public function asFloat(): float
    {
        if (is_float($this->value)) {
            return $this->value;
        }

        throw new RuntimeException('Value is not of type float');
    }

    public function asBool(): bool
    {
        if (is_bool($this->value)) {
            return $this->value;
        }

        throw new RuntimeException('Value is not of type bool');
    }

    /**
     * @return int[]
     */
    public function asIntArray(): array
    {
        if (
            is_array($this->value)
            && array_reduce($this->value, fn($carry, $item) => $carry && is_int($item), true)
        ) {
            // The above check is too advanced for static analysis.
            // However we can now be sure of the type.

            /** @var int[] */
            return $this->value;
        }

        throw new RuntimeException('Value is not of type int[]');
    }

    /**
     * @return string[]
     */
    public function asStringArray(): array
    {
        if (
            is_array($this->value)
            && array_reduce($this->value, fn($carry, $item) => $carry && is_string($item), true)
        ) {
            // The above check is too advanced for static analysis.
            // However we can now be sure of the type.

            /** @var string[] */
            return $this->value;
        }

        throw new RuntimeException('Value is not of type string[]');
    }

    /**
     * @template U
     *
     * @param class-string<U> $type
     *
     * @return array<U>
     */
    public function asArray(string $type): array
    {
        if (
            is_array($this->value)
            && array_reduce($this->value, fn($carry, $item) => $carry && $item instanceof $type, true)
        ) {
            return $this->value;
        }

        throw new RuntimeException('Value is not of type ' . $type . '[]');
    }
}
