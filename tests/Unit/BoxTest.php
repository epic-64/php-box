<?php

use Epic64\PhpBox\Box;

test('we can box, transform and unbox a value', function () {
    $result = Box::of(5)
        ->map(fn($x) => $x * 2)
        ->map(fn($x) => $x + 1)
        ->value();

    expect($result)->toBe(11);
});

test('we can combine a pipe and unbox into one statement by using pull', function () {
    $result = Box::of(5)
        ->map(fn($x) => $x * 2)
        ->get(fn($x) => $x + 1);

    expect($result)->toBe(11);
});

test('assertion by value fails with a meaningful message', function () {
    $expectedMessage = "Failed asserting that two values are the same. Expected 'HELLO', got 'Hello'";

    expect(fn() => Box::of('Hello')->assert('HELLO'))
        ->toThrow(LogicException::class, $expectedMessage);
});

// @phpstan-ignore-next-line method.notFound
test('assertion by value passes when the values are the same', function (mixed $input, mixed $output) {
    $result = Box::of($input)->assert($output)->value();

    expect($result)->toBe($input);
})->with([
    ['HELLO', 'HELLO'],
    [5, 5],
    [5.5, 5.5],
    [true, true],
    [false, false],
    [null, null],
    [[], []],
    [[1, 2, 3], [1, 2, 3]],
    [['a' => 1, 'b' => 2], ['a' => 1, 'b' => 2]],
    ['str_split', 'str_split'],  // making sure functions as strings are treated as strings
    // [(object)[], (object)[]], // this one will fail because two objects have different references
]);

test('assertion by value fails for two equal objects', function () {
    $object1 = (object)['number' => 5];
    $object2 = (object)['number' => 5];

    expect(fn() => Box::of($object1)->assert($object2))->toThrow(LogicException::class);
});

test('assertion by value passes for two equal objects with the same reference', function () {
    $object = (object)['number' => 5];

    $result = Box::of($object)->assert($object)->value();

    expect($result->number)->toBe(5);
});

test('assertGet returns the value when the assertion passes', function () {
    $result = Box::of(5)->assertGet(5);

    expect($result)->toBe(5);
});

test('assertGet throws an exception when the assertion fails', function () {
    expect(fn() => Box::of(5)->assertGet(6))->toThrow(LogicException::class);
});

test('flatMap allows us to replace the box itself', function () {
    $result = Box::of(5)
        ->mod(fn(Box $x) => $x->assert(5)->map(fn($x) => $x + 1))
        ->value();

    expect($result)->toBe(6);
});

test('use flatMap to compose actions', function () {
    $isValidEmail = function (Box $box) {
        return $box
            ->assert(fn(mixed $x)  => is_string($x), 'Not a string')
            ->assert(fn(string $x) => strlen($x) > 0, 'Too short')
            ->assert(fn(string $x) => strlen($x) < 256, 'Too long')
            ->assert(fn(string $x) => filter_var($x, FILTER_VALIDATE_EMAIL), 'Not an email');
    };

    expect(fn() => Box::of('asdf')->mod($isValidEmail)->value())
        ->toThrow(LogicException::class, 'Not an email | Value did not pass the callback check.');
});

// This test is not here to "lock in" desired behavior. It is here to document a pitfall.
test('performing a mutation on an object using map() will produce side effects', function () {
    $object = (object)['number' => 5];

    // Avoid code like this.
    // Problem 1: The original object is mutated
    // Problem 2: The object is thrown away and instead $value will simply be 10
    $newObject = Box::of($object)->map(fn($x) => $x->number = 10)->value();

    expect($object->number)->toBe(10, 'Bad: Original object is mutated');
    expect($newObject)->toBeInt('Bad: $newObject is not an object, instead it is an int.');
});

test('assertion by callback passes when check is passed', function () {
    $object = Box::of((object)['number' => 5])
        ->assert(fn($x) => (object)['number' => 5] == $x)
        ->assert(fn($x) => $x->number > 0) // @phpstan-ignore greater.alwaysTrue
        ->value();

    expect($object->number)->toBe(5);
});

test('we can chain multiple transformations and assertions', function () {
    $result = Box::of('Hello')
        ->map(strtoupper(...))->assert('HELLO')
        ->map(strrev(...))->assert('OLLEH')
        ->map(str_split(...))->assert(['O', 'L', 'L', 'E', 'H'])
        ->map(fn($arr) => array_map(ord(...), $arr))->assert([79, 76, 76, 69, 72])
        ->map(array_sum(...))->assert(372)->assert(fn($x) => $x > 0)
        ->get(fn($x) => $x + 100);

    expect($result)->toBe(472);
});