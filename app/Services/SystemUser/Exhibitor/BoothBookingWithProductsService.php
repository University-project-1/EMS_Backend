<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\BoothRequestDTO;
use App\DTOs\SystemUser\CompanyDTO;
use App\Models\BoothRequest;
use App\Models\SystemUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Throwable;

final class BoothBookingWithProductsService
{
    private const int MAX_PRODUCTS = 500;

    private const array HEADINGS = ['name', 'price', 'description'];

    public function __construct(
        private readonly BoothRequestService $boothRequestService,
    ) {}

    public function book(SystemUser $user, BoothRequestDTO $bookingDTO, ?CompanyDTO $companyDTO, UploadedFile $productsFile): BoothRequest
    {
        $products = $this->readProducts($productsFile);

        return DB::transaction(function () use ($user, $bookingDTO, $companyDTO, $products, $productsFile): BoothRequest {
            $boothRequest = $this->boothRequestService->confirmBoothBooking($user, $bookingDTO, $companyDTO);
            $now = now();

            $boothRequest->products()->insert(array_map(
                fn (array $product, int $index): array => $product + [
                    'booth_request_id' => $boothRequest->getKey(),
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $products,
                array_keys($products),
            ));

            $boothRequest
                ->addMedia($productsFile)
                ->usingFileName('products_catalog.xlsx')
                ->toMediaCollection('products_catalog');

            return $boothRequest->load(['products', 'services', 'company.logoMedia', 'company.bannerMedia']);
        });
    }

    private function readProducts(UploadedFile $productsFile): array
    {
        try {
            $reader = IOFactory::createReaderForFile($productsFile->getRealPath());

            if (! $reader instanceof Xlsx) {
                throw new \RuntimeException('Unsupported spreadsheet format.');
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($productsFile->getRealPath());
            $sheetCount = $spreadsheet->getSheetCount();
            $rows = $spreadsheet->getSheet(0)->toArray(null, false, false, false);
            $spreadsheet->disconnectWorksheets();
        } catch (Throwable) {
            throw ValidationException::withMessages(['products_file' => [__('booth.products_catalog.unreadable')]]);
        }

        if ($sheetCount !== 1) {
            throw ValidationException::withMessages(['products_file' => [__('booth.products_catalog.single_worksheet')]]);
        }

        $headings = array_map(fn (mixed $value): string => Str::lower(trim((string) $value)), array_shift($rows) ?? []);

        if ($headings !== self::HEADINGS) {
            throw ValidationException::withMessages(['products_file' => [__('booth.products_catalog.headings')]]);
        }

        $rows = array_values(array_filter($rows, fn (array $row): bool => collect($row)->contains(fn (mixed $value): bool => filled($value))));

        if ($rows === [] || count($rows) > self::MAX_PRODUCTS) {
            throw ValidationException::withMessages(['products_file' => [__('booth.products_catalog.product_count', ['min' => 1, 'max' => self::MAX_PRODUCTS])]]);
        }

        $products = array_map(fn (array $row): array => [
            'name' => Str::squish((string) ($row[0] ?? '')),
            'price' => trim((string) ($row[1] ?? '')),
            'description' => trim((string) ($row[2] ?? '')),
        ], $rows);

        $validator = Validator::make($products, [
            '*.name' => ['required', 'string', 'max:191'],
            '*.price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            '*.description' => ['required', 'string', 'max:4000'],
        ]);

        $validator->after(function ($validator) use ($products): void {
            $names = [];

            foreach ($products as $index => $product) {
                $name = Str::lower($product['name']);

                if ($name !== '' && isset($names[$name])) {
                    $validator->errors()->add("{$index}.name", __('booth.products_catalog.duplicate_name', ['row' => $names[$name] + 2]));
                }

                $names[$name] = $index;
            }
        });

        if ($validator->fails()) {
            $errors = [];

            foreach ($validator->errors()->toArray() as $attribute => $messages) {
                [$row, $field] = explode('.', $attribute, 2);
                $errors[] = __('booth.products_catalog.row_error', ['row' => ((int) $row) + 2, 'field' => __('validation.attributes.'.$field), 'message' => $messages[0]]);
            }

            throw ValidationException::withMessages(['products_file' => $errors]);
        }

        return array_map(fn (array $product): array => [
            'name' => $product['name'],
            'price' => number_format((float) $product['price'], 2, '.', ''),
            'description' => $product['description'],
        ], $products);
    }
}
