<?php

use App\Enum\Status;
use App\Models\BoothProduct;
use App\Services\SystemUser\Exhibitor\BoothBookingWithProductsService;
use App\Services\SystemUser\Exhibitor\BoothRequestService;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function boothProductsFile(array $rows): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray($rows);

    $path = tempnam(sys_get_temp_dir(), 'booth-products-');
    (new Xlsx($spreadsheet))->save($path);
    $spreadsheet->disconnectWorksheets();

    return new UploadedFile(
        $path,
        'products.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true,
    );
}

function readBoothProducts(UploadedFile $file): array
{
    $service = new BoothBookingWithProductsService(Mockery::mock(BoothRequestService::class));
    $method = new ReflectionMethod($service, 'readProducts');

    return $method->invoke($service, $file);
}

afterEach(function (): void {
    Mockery::close();
});

it('reads and normalizes a valid products catalog', function (): void {
    $products = readBoothProducts(boothProductsFile([
        ['name', 'price', 'description'],
        ['  Coffee   Machine ', '1250.5', 'Compact automatic machine'],
    ]));

    expect($products)->toBe([
        [
            'name' => 'Coffee Machine',
            'price' => '1250.50',
            'description' => 'Compact automatic machine',
        ],
    ]);
});

it('rejects a catalog with invalid headings', function (): void {
    expect(fn () => readBoothProducts(boothProductsFile([
        ['title', 'price', 'description'],
        ['Coffee Machine', '1250.50', 'Compact automatic machine'],
    ])))->toThrow(ValidationException::class);
});

it('rejects duplicate product names regardless of casing or whitespace', function (): void {
    expect(fn () => readBoothProducts(boothProductsFile([
        ['name', 'price', 'description'],
        ['Coffee Machine', '1250.50', 'Compact automatic machine'],
        [' coffee machine ', '999.00', 'Duplicate product'],
    ])))->toThrow(ValidationException::class);
});

it('returns English products catalog validation messages', function (): void {
    app()->setLocale('en');

    try {
        readBoothProducts(boothProductsFile([
            ['title', 'price', 'description'],
            ['Coffee Machine', '1250.50', 'Compact automatic machine'],
        ]));
    } catch (ValidationException $exception) {
        expect($exception->errors()['products_file'][0])
            ->toBe('The first row must contain exactly: name, price, description.');
    } finally {
        app()->setLocale(config('app.locale'));
    }
});

it('rejects a product price that exceeds the database precision', function (): void {
    expect(fn () => readBoothProducts(boothProductsFile([
        ['name', 'price', 'description'],
        ['Coffee Machine', '10000000000.00', 'Compact automatic machine'],
    ])))->toThrow(ValidationException::class);
});

it('constrains the public products scope to approved booth requests', function (): void {
    $query = BoothProduct::query()->forApprovedBooths();

    expect($query->toSql())->toContain('exists')
        ->and($query->getBindings())->toContain(Status::APPROVED->value);
});
