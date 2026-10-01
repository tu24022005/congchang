<?php

return [
    'service_fee' => 3000,
    'shipping_zones' => [
        'inner_city' => [
            'label' => 'Nội thành Hà Nội / TP. Hồ Chí Minh',
            'fee' => 15000,
        ],
        'other_city' => [
            'label' => 'Tỉnh/thành khác',
            'fee' => 25000,
        ],
        'remote' => [
            'label' => 'Huyện đảo / khu vực xa',
            'fee' => 35000,
        ],
    ],
    'shipping_providers' => [
        'GHN' => 'Giao Hàng Nhanh (GHN)',
        'GHTK' => 'Giao Hàng Tiết Kiệm (GHTK)',
        'Viettel Post' => 'Viettel Post',
        'J&T Express' => 'J&T Express',
        'Shopee Express' => 'Shopee Express',
    ],
    'shipping_provider_fees' => [
        'inner_city' => [
            'GHN' => 18000,
            'GHTK' => 16000,
            'Viettel Post' => 20000,
            'J&T Express' => 17000,
            'Shopee Express' => 15000,
        ],
        'other_city' => [
            'GHN' => 28000,
            'GHTK' => 26000,
            'Viettel Post' => 30000,
            'J&T Express' => 27000,
            'Shopee Express' => 25000,
        ],
        'remote' => [
            'GHN' => 40000,
            'GHTK' => 38000,
            'Viettel Post' => 42000,
            'J&T Express' => 39000,
            'Shopee Express' => 37000,
        ],
    ],
    'free_shipping_threshold' => 299000,
    'seo' => [
        'site_name' => 'Aloha Beauty - BeatyCare',
        'default_title' => 'BeatyCare 🌸 Mỹ phẩm & Chăm sóc sắc đẹp chính hãng',
        'default_description' => 'Khám phá thế giới mỹ phẩm, dưỡng da, chăm sóc cá nhân chính hãng tại BeatyCare (Aloha Beauty). Đảm bảo chất lượng, nhiều khuyến mãi và giao hàng nhanh toàn quốc.',
        'default_og_image' => 'images/og-default.svg',
    ],
];
