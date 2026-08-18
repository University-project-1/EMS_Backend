<?php

test('success responses expose a consistent API envelope', function (): void {
    $response = successResponse(['status' => 'ready'], 'Created', 201);

    expect($response->getStatusCode())->toBe(201);
    expect($response->getData(true))->toMatchArray([
        'status' => true,
        'message' => 'Created',
        'data' => ['status' => 'ready'],
    ]);
});

test('error responses expose a consistent API envelope', function (): void {
    $response = errorResponse('Validation failed', [
        'email' => ['The email field is required.'],
    ], 422);

    expect($response->getStatusCode())->toBe(422);
    expect($response->getData(true))->toMatchArray([
        'status' => false,
        'message' => 'Validation failed',
        'errors' => [
            'email' => ['The email field is required.'],
        ],
    ]);
});
