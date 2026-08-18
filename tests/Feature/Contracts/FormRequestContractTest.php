<?php

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

function requestClasses(): Collection
{
    return collect(File::allFiles(app_path('Http/Requests')))
        ->map(function (SplFileInfo $file): string {
            $relativePath = $file->getRelativePathname();
            $class = str_replace(['/', '\\'], '\\', substr($relativePath, 0, -4));

            return 'App\\Http\\Requests\\'.$class;
        })
        ->sort()
        ->values();
}

function formRequestClasses(): Collection
{
    return requestClasses()
        ->filter(fn (string $class): bool => is_subclass_of($class, FormRequest::class))
        ->values();
}

test('all request files resolve to application classes', function (): void {
    $classes = requestClasses();

    expect($classes)->not->toBeEmpty();

    $classes->each(function (string $class): void {
        expect(class_exists($class))->toBeTrue("{$class} must be autoloadable");
    });
});

test('every form request explicitly declares authorization and validation rules', function (): void {
    $requests = formRequestClasses();

    expect($requests)->not->toBeEmpty();

    $requests->each(function (string $class): void {
        $reflection = new ReflectionClass($class);

        expect($reflection->hasMethod('authorize'))->toBeTrue("{$class} must declare authorize()");
        expect($reflection->getMethod('authorize')->getDeclaringClass()->getName())
            ->toBe($class, "{$class} must declare its own authorize() method");
        expect($reflection->hasMethod('rules'))->toBeTrue("{$class} must declare rules()");
        expect($reflection->getMethod('rules')->getDeclaringClass()->getName())
            ->toBe($class, "{$class} must declare its own rules() method");

        $rules = (new $class)->rules();

        expect($rules)->toBeArray();
        expect($rules)->not->toBeEmpty();

        foreach ($rules as $field => $fieldRules) {
            expect($field)->toBeString()->not->toBe('');
            expect($fieldRules)->not->toBeEmpty();
        }
    });
});

test('shared validation rule providers return a non-empty rule set', function (): void {
    $providers = requestClasses()
        ->reject(fn (string $class): bool => is_subclass_of($class, FormRequest::class));

    expect($providers)->not->toBeEmpty();

    $providers->each(function (string $class): void {
        expect(method_exists($class, 'get'))->toBeTrue("{$class} must expose a static get() rule provider");

        $rules = $class::get();

        expect($rules)->toBeArray();
        expect($rules)->not->toBeEmpty();
    });
});
