<?php

use App\Filter\BoothProductsSearchFilter;
use App\Models\BoothProduct;
use Illuminate\Support\Facades\DB;

function useMysqlSearchGrammar(): void
{
    config()->set('database.default', 'mysql');

    DB::purge('mysql');
}

function useSqliteSearchGrammar(): void
{
    config()->set('database.default', 'sqlite');

    DB::purge('sqlite');
}

afterEach(function (): void {
    useSqliteSearchGrammar();
});

it('uses MySQL full-text search for terms with at least three characters', function (): void {
    useMysqlSearchGrammar();

    $query = BoothProduct::query();

    (new BoothProductsSearchFilter)($query, 'coffee', 'search');

    expect($query->toSql())
        ->toContain('match (`name`, `description`) against (? in natural language mode)')
        ->and($query->getBindings())->toBe(['coffee']);
});

it('uses like search for short terms that MySQL full-text may not index', function (): void {
    useMysqlSearchGrammar();

    $query = BoothProduct::query();

    (new BoothProductsSearchFilter)($query, 'TV', 'search');

    expect($query->toSql())
        ->toContain('`name` like ?')
        ->toContain('`description` like ?')
        ->and($query->getBindings())->toBe(['%TV%', '%TV%']);
});

it('uses like search in SQLite test environments', function (): void {
    useSqliteSearchGrammar();

    $query = BoothProduct::query();

    (new BoothProductsSearchFilter)($query, 'coffee', 'search');

    expect($query->toSql())
        ->toContain('"name" like ?')
        ->toContain('"description" like ?')
        ->and($query->getBindings())->toBe(['%coffee%', '%coffee%']);
});

it('does not modify the query for blank or non-string search values', function (mixed $value): void {
    $query = BoothProduct::query();

    (new BoothProductsSearchFilter)($query, $value, 'search');

    expect($query->toSql())->toBe('select * from "booth_products"')
        ->and($query->getBindings())->toBeEmpty();
})->with(['blank' => '   ', 'non-string' => 123]);
