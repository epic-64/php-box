<?php

namespace Tests\Unit;

use Epic64\PhpBox\TryBox;
use Throwable;

test('TryBox can catch exceptions', function () {
    $result = TryBox::of(5)
        ->map(fn(int $value) => $value / 1)
        ->map(fn(int $value) => $value * 2)
        ->value();

    expect($result)->toBeInstanceOf(Throwable::class);
});

test('TryBox result can be used when nothing is thrown', function () {
    $result = TryBox::of(5)
        ->map(fn(int $value) => $value * 2)
        ->map(fn(int $value) => $value + 1)
        ->rip();

    expect($result * 2)->toBe(11);
});