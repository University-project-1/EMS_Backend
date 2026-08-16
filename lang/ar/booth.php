<?php

return [
    'list_success' => 'تم جلب الأجنحة بنجاح.',
    'show_success' => 'تم جلب الجناح بنجاح.',
    'update_success' => 'تم تحديث الجناح بنجاح.',
    'book_success' => 'تم تأكيد طلب الحجز بنجاح، بانتظار موافقة الإدارة.',
    'products_catalog' => [
        'unreadable' => 'يجب أن يكون ملف منتجات الجناح بصيغة XLSX صالحة.',
        'single_worksheet' => 'يجب أن يحتوي ملف منتجات الجناح على ورقة عمل واحدة فقط.',
        'headings' => 'يجب أن يحتوي الصف الأول حصراً على الأعمدة: name, price, description.',
        'product_count' => 'يجب أن يحتوي ملف منتجات الجناح على عدد منتجات بين :min و :max.',
        'duplicate_name' => 'اسم المنتج مكرر مع الصف رقم :row.',
        'row_error' => 'الصف رقم :row، الحقل :field: :message',
    ],
];
