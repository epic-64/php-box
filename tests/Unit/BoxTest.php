<?php

use Epic64\PhpBox\Box;

test('we can box, transform and unbox a value', function () {
    $result = Box::of(5)
        ->pipe(fn($x) => $x * 2)
        ->pipe(fn($x) => $x + 1)
        ->unbox();

    expect($result)->toBe(11);
});

test('we can combine a pipe and unbox into one statement by using pull', function () {
    $result = Box::of(5)
        ->pipe(fn($x) => $x * 2)
        ->pull(fn($x) => $x + 1);

    expect($result)->toBe(11);
});

test('assertion by value fails with a meaningful message', function () {
    $expectedMessage = "Failed asserting that two values are the same. Expected 'HELLO', got 'Hello'";

    expect(fn() => Box::of('Hello')->assert('HELLO'))
        ->toThrow(LogicException::class, $expectedMessage);
});

test('assertion by value passes when the values are the same', function () {
    $result = Box::of('Hello')->pipe(strtoupper(...))->assert('HELLO')->unbox();

    expect($result)->toBe('HELLO');
});

test('assertion by callback fails when check is not passed', function () {
    $expectedMessage = 'Value did not pass the callback check';

    expect(fn() => Box::of(5)->assert(fn($x) => $x < 5)) // @phpstan-ignore smaller.alwaysFalse
        ->toThrow(LogicException::class, $expectedMessage);
});

test('assertion by callback passes when check is passed', function () {
    $object = Box::of((object)['number' => 5])
        ->assert(fn($x) => (object)['number' => 5] == $x)
        ->assert(fn($x) => $x->number > 0) // @phpstan-ignore greater.alwaysTrue
        ->unbox();

    expect($object->number)->toBe(5);
});

test('we can chain multiple transformations and assertions', function () {
    $result = Box::of('Hello')
        ->pipe(strtoupper(...))->assert('HELLO')
        ->pipe(strrev(...))->assert('OLLEH')
        ->pipe(str_split(...))->assert(['O', 'L', 'L', 'E', 'H'])
        ->pipe(fn($arr) => array_map(ord(...), $arr))->assert([79, 76, 76, 69, 72])
        ->pipe(array_sum(...))->assert(372)->assert(fn($x) => $x > 0)
        ->pull(fn($x) => $x + 100);

    expect($result)->toBe(472);
});