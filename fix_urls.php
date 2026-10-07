<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// header_menu
$headerMenu = [
    [
        'label' => 'About Us',
        'url' => '/#about'
    ],
    [
        'label' => 'Tracking',
        'url' => '/tracking'
    ],
    [
        'label' => 'Price',
        'url' => '/#pricing'
    ],
    [
        'label' => 'Service',
        'url' => '/#service'
    ],
    [
        'label' => 'Contact Us',
        'url' => '/#contact'
    ]
];

DB::table('settings')->where('title', 'header_menu')->update(['value' => serialize($headerMenu)]);

// footer_quick_link_menu
$footerMenu = [
    [
        'label' => 'Track Products',
        'url' => '/tracking'
    ],
    [
        'label' => 'Contact Us',
        'url' => '/#contact'
    ],
    [
        'label' => 'Privacy Policy',
        'url' => '/privacy-policy'
    ],
    [
        'label' => 'Terms & Conditions',
        'url' => '/terms-and-conditions'
    ]
];
DB::table('settings')->where('title', 'footer_quick_link_menu')->update(['value' => serialize($footerMenu)]);

echo "Menus fixed to local URLs.\n";
