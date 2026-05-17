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

    'newsletter' => [
        'discount_percent' => env('CHOMIN_NEWSLETTER_DISCOUNT_PERCENT', 10),
        'discount_max' => env('CHOMIN_NEWSLETTER_DISCOUNT_MAX', 300),
        'coupon_valid_days' => env('CHOMIN_NEWSLETTER_COUPON_DAYS', 30),
        'popup_delay_ms' => env('CHOMIN_NEWSLETTER_POPUP_DELAY_MS', 25000),
    ],

    'shipping' => [
        'zone_surcharge' => [
            'south' => 20,
        ],
    ],

    'payment' => [
        'methods' => [
            'promptpay_slip' => [
                'enabled' => env('CHOMIN_PAYMENT_PROMPTPAY_ENABLED', true),
                'label_th' => 'PromptPay (อัปโหลดสลิป)',
                'label_en' => 'PromptPay (slip upload)',
            ],
            'cod' => [
                'enabled' => env('CHOMIN_PAYMENT_COD_ENABLED', true),
                'fee' => (float) env('CHOMIN_PAYMENT_COD_FEE', 30),
                'label_th' => 'เก็บเงินปลายทาง (COD)',
                'label_en' => 'Cash on delivery',
                'min_order' => (float) env('CHOMIN_PAYMENT_COD_MIN_ORDER', 0),
                'max_order' => (float) env('CHOMIN_PAYMENT_COD_MAX_ORDER', 50000),
            ],
            'bank_transfer' => [
                'enabled' => env('CHOMIN_PAYMENT_BANK_TRANSFER_ENABLED', false),
                'label_th' => 'โอนผ่านธนาคาร',
                'label_en' => 'Bank transfer',
            ],
        ],
    ],

    'abandoned_cart' => [
        'first_reminder_hours' => env('CHOMIN_ABANDONED_CART_FIRST_HOURS', 4),
        'second_reminder_hours' => env('CHOMIN_ABANDONED_CART_SECOND_HOURS', 24),
    ],

    'reviews' => [
        'request_window_days' => [
            'min' => env('CHOMIN_REVIEW_REQUEST_MIN_DAYS', 7),
            'max' => env('CHOMIN_REVIEW_REQUEST_MAX_DAYS', 8),
        ],
    ],

    'referral' => [
        'cookie_name' => 'chomin_referral',
        'cookie_ttl_minutes' => (int) env('CHOMIN_REFERRAL_COOKIE_TTL_MINUTES', 60 * 24 * 30),
        'referrer_bonus_points' => (int) env('CHOMIN_REFERRAL_REFERRER_BONUS', 200),
        'referee_bonus_points' => (int) env('CHOMIN_REFERRAL_REFEREE_BONUS', 100),
    ],

    'gift_cards' => [
        'denominations' => [500, 1000, 2000, 5000],
    ],

    'returns' => [
        'eligible_days' => (int) env('CHOMIN_RETURN_ELIGIBLE_DAYS', 30),
    ],

    'tiers' => [
        'bronze' => [
            'min_spend' => 0,
            'name_th' => 'Bronze',
            'name_en' => 'Bronze',
            'points_multiplier' => 1.0,
            'shipping_perk' => null,
            'early_access_days' => 0,
            'birthday_bonus' => 100,
        ],
        'silver' => [
            'min_spend' => 5000,
            'name_th' => 'Silver',
            'name_en' => 'Silver',
            'points_multiplier' => 1.25,
            'shipping_perk' => null,
            'early_access_days' => 1,
            'birthday_bonus' => 250,
        ],
        'gold' => [
            'min_spend' => 15000,
            'name_th' => 'Gold',
            'name_en' => 'Gold',
            'points_multiplier' => 1.5,
            'shipping_perk' => 'priority',
            'early_access_days' => 3,
            'birthday_bonus' => 500,
        ],
        'platinum' => [
            'min_spend' => 40000,
            'name_th' => 'Platinum',
            'name_en' => 'Platinum',
            'points_multiplier' => 2.0,
            'shipping_perk' => 'express',
            'early_access_days' => 7,
            'birthday_bonus' => 1000,
        ],
    ],

    'social' => [
        'instagram_handle' => env('CHOMIN_IG_HANDLE', 'chomin.th'),
        'instagram_url' => env('CHOMIN_IG_URL', 'https://www.instagram.com/chomin.th/'),
        'instagram_token' => env('CHOMIN_IG_TOKEN'),
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
