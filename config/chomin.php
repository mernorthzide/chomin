<?php

return [
    'locales' => [
        'default' => 'th',
        'supported' => ['th', 'en'],
        'labels' => [
            'th' => 'ไทย',
            'en' => 'English',
        ],
    ],
    'custom_options' => [
        'collar' => [
            'label' => 'คอเสื้อ',
            'options' => [
                'french-collar' => 'French Collar',
                'italian-collar' => 'Italian Collar',
                'mandarin-collar' => 'Mandarin Collar',
                'button-down' => 'Button Down',
                'band-collar' => 'Band Collar',
            ],
        ],
        'cuff' => [
            'label' => 'ปลายแขน',
            'options' => [
                'one-button' => '1 Button',
                'two-button' => '2 Button',
                'french-cuff' => 'French Cuff',
            ],
        ],
        'pocket' => [
            'label' => 'กระเป๋า',
            'options' => [
                'no-pocket' => 'No Pocket',
                'yes-pocket' => 'Yes Pocket',
            ],
        ],
    ],
    'product_option_defaults' => [
        'cm-classic-custom-shirt' => [
            'collar' => 'french-collar',
            'cuff' => 'one-button',
            'pocket' => 'no-pocket',
        ],
        'cm-workday-shirt' => [
            'collar' => 'italian-collar',
            'cuff' => 'one-button',
            'pocket' => 'no-pocket',
        ],
        'cm-soft-pastel-shirt' => [
            'collar' => 'french-collar',
            'cuff' => 'two-button',
            'pocket' => 'no-pocket',
        ],
        'cm-statement-color-shirt' => [
            'collar' => 'button-down',
            'cuff' => 'two-button',
            'pocket' => 'yes-pocket',
        ],
        'cm-mandarin-minimal-shirt' => [
            'collar' => 'mandarin-collar',
            'cuff' => 'one-button',
            'pocket' => 'no-pocket',
        ],
    ],
];
