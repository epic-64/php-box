<?php

namespace Tests\Unit;

use Epic64\PhpBox\TryMonad\Success;
use RuntimeException;

test('trial map applies functions to value', function () {
    $result = new Success(5)
        ->map(fn($value) => $value * 2)
        ->map(fn($value) => $value + 1)
        ->get();

    expect($result)->toBe(11);
});

test('trial flatmap avoids nesting', function () {
    $result = new Success(5)
        ->map(fn($value) => $value * 2)
        ->flatMap(fn($value) => new Success($value + 1))
        ->get();

    expect($result)->toBe(11);
});

test('trial getOrElse returns value if success', function () {
    $result = new Success(5)
        ->map(fn($value) => $value * 2)
        ->map(fn($value) => $value + 1)
        ->getOrElse(0);

    expect($result)->toBe(11);
});

test('trial getOrElse returns default if failure', function () {
    $result = new Success(5)
        ->map(fn($value) => throw new \RuntimeException('boo'))
        ->map(fn($value) => $value + 1)
        ->getOrElse(0);

    expect($result)->toBe(0);
});

test('failure get throws exception', function () {
    $result = new Success(5)
        ->map(fn($value) => throw new RuntimeException('boo'))
        ->map(fn($value) => $value + 1);

    expect(fn() => $result->get())->toThrow(RuntimeException::class, 'boo');
});