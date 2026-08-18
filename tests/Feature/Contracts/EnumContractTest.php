<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

function enumClasses(): Collection
{
    return collect(File::allFiles(app_path('Enum')))
        ->map(function (SplFileInfo $file): string {
            $relativePath = $file->getRelativePathname();
            $class = str_replace(['/', '\\'], '\\', substr($relativePath, 0, -4));

            return 'App\\Enum\\'.$class;
        })
        ->sort()
        ->values();
}

test('all application enum files resolve to enums', function (): void {
    $classes = enumClasses();

    expect($classes)->not->toBeEmpty();

    $classes->each(function (string $class): void {
        expect(enum_exists($class))->toBeTrue();
    });
});

test('backed enum values are unique and non-empty', function (): void {
    enumClasses()->each(function (string $class): void {
        $reflection = new ReflectionEnum($class);

        if (! $reflection->isBacked()) {
            return;
        }

        $values = array_map(
            fn (BackedEnum $case): int|string => $case->value,
            $class::cases(),
        );

        expect($values)->toHaveCount(count(array_unique($values)));

        foreach ($values as $value) {
            if (is_string($value)) {
                expect(trim($value))->not->toBe('');
            }
        }
    });
});
