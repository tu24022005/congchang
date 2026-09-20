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
];
