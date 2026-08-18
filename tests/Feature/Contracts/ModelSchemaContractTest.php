<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

function applicationModelClasses(): Collection
{
    return collect(File::allFiles(app_path('Models')))
        ->map(function (SplFileInfo $file): string {
            $relativePath = $file->getRelativePathname();
            $class = str_replace(['/', '\\'], '\\', substr($relativePath, 0, -4));

            return 'App\\Models\\'.$class;
        })
        ->sort()
        ->values();
}

test('all model files resolve to Eloquent models', function (): void {
    $models = applicationModelClasses();

    expect($models)->not->toBeEmpty();

    $models->each(function (string $model): void {
        expect(is_subclass_of($model, Model::class))
            ->toBeTrue("{$model} must extend ".Model::class);
    });
});

test('every application model maps to a migrated database table', function (): void {
    applicationModelClasses()->each(function (string $model): void {
        $table = (new $model)->getTable();

        expect(Schema::hasTable($table))
            ->toBeTrue("{$model} expects the [{$table}] table to be migrated");
    });
});
