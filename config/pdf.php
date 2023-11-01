<?php

return [

    // Meta data
    'creator'  => env('PDF_CREATOR', 'Leltárkezelő'),
    'author'   => env('PDF_AUTHOR', ''),
    'title'    => env('PDF_TITLE', 'Leltár '.date('Y')),
    'subject'  => env('PDF_SUBJECT', 'Leltár'),
    'keywords' => env('PDF_KEYWORDS', 'leltár, '.date('Y')),

    // Header
    'logo'     => env('PDF_LOGO'),
    'stock'    => [
        'reporting' => DateTime::createFromFormat('Y-m-d', env('PDF_STOCK_REPORTING_YEAR', date('Y')).'-12-31'),
        'taking'    => DateTime::createFromFormat('Y-m-d', env('PDF_STOCK_TAKING_DATE', date('Y-m-d'))),
    ],

    // --
    'company'  => env('PDF_COMPANY'),

];
