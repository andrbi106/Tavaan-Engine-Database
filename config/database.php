<?php

declare(strict_types=1);

/*|--------------------------------------------------------------------------
 | Database Engine Configuration
 |--------------------------------------------------------------------------
 |
 | پیکربندی موتور database — همهٔ کلیدهای مستندشده در `docs/03-engines/database.md` بخش «پیکربندی».
 |
 |@see https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/database.md
 */

return [
    /*| پیش‌فرض موتور (driver/store یا حالت فعال) */
    'default' => env('DATABASE_DEFAULT', 'default'),

    /*| کلید کش (در صورت استفاده) */
    'prefix' => env('DATABASE_PREFIX', 'database:'),

    /*| تنظیمات لاگ */
    'log' => [
        'channel' => env('DATABASE_LOG_CHANNEL', 'database'),
    ],

    /*| Facade */
    'facade' => [
        'name' => 'DB',
        'accessor' => 'db',
    ],

    /*| سایر کلیدها (طبق مستندات) */
    'options' => [
        // TODO: کلیدهای اضافی طبق سند §۶ (پیکربندی)
    ],
];