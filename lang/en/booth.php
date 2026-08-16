<?php

return [
    'list_success' => 'Booths retrieved successfully.',
    'show_success' => 'Booth retrieved successfully.',
    'update_success' => 'Booth updated successfully.',
    'book_success' => 'booking confirmed successfully, needs admin confirmation',
    'products_catalog' => [
        'unreadable' => 'The products catalog must be a valid XLSX file.',
        'single_worksheet' => 'The products catalog must contain exactly one worksheet.',
        'headings' => 'The first row must contain exactly: name, price, description.',
        'product_count' => 'The products catalog must contain between :min and :max products.',
        'duplicate_name' => 'The product name is duplicated with row :row.',
        'row_error' => 'Row :row, :field: :message',
    ],
];
