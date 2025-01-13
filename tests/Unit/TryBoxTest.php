<?php

namespace Tests\Unit;

use Epic64\PhpBox\TryBox;
use RuntimeException;
use Throwable;

test('TryBox returns exception as value', function () {
    $result = TryBox::of(5)
        ->map(fn($value) => throw new RuntimeException('boo'))
        ->map(fn($value) => $value * 2)
        ->map(fn($value) => $value + 1)
        ->value();

    expect($result)
        ->toBeInstanceOf(RuntimeException::class)
        ->and($result->getMessage())->toBe('boo'); // @phpstan-ignore method.nonObject
});

test('TryBox value can be used when nothing is thrown', function () {
    $result = TryBox::of(1)
        ->map(fn($value) => $value + 1)
        ->map(fn($value) => $value + 1)
        ->value();

    // manual narrowing of $result from int|Throwable to int
    if ($result instanceof Throwable) {
        throw $result;
    }

    $result = $result + 1;

    expect($result)->toBe(4);
});

test('Using rip() on the TryBox will narrow the type but risk throwing an exception', function () {
    $result = TryBox::of(1)
        ->map(fn($value) => $value + 1)
        ->map(fn($value) => $value + 1)
        ->rip(); // Can throw exception but the type is narrowed to int

    $result = $result + 1;

    expect($result)->toBe(4);
});

test('Using rip() on TryBox with error will cause exception', function () {
    $box = TryBox::of(1)
        ->map(fn($value) => throw new RuntimeException('boo'))
        ->map(fn($value) => $value + 1);

    expect(fn() => $box->rip())->toThrow(RuntimeException::class, 'boo');
});