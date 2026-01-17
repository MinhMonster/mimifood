<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Transaction Types
    |--------------------------------------------------------------------------
    | Danh sách các loại giao dịch ví (wallet transactions).
    | type: increase = cộng tiền, decrease = trừ tiền
    */

    'types' => [
        'top_up' => [
            'label' => 'Nạp tiền',
            'direction'  => 'in',
            'content' => 'Nạp tiền vào ví',
        ],
        'purchase' => [
            'label' => 'Thanh toán đơn hàng',
            'direction'  => 'out',
            'content' => 'Thanh toán cho đơn hàng',
        ],
        'refund' => [
            'label' => 'Hoàn tiền',
            'direction'  => 'in',
            'content' => 'Hoàn tiền cho đơn hàng',
        ],
        'admin_adjust_increase' => [
            'label' => 'Điều chỉnh tăng',
            'direction'  => 'in',
            'content' => 'Admin cộng tiền',
        ],
        'admin_adjust_decrease' => [
            'label' => 'Điều chỉnh giảm',
            'direction'  => 'out',
            'content' => 'Admin trừ tiền',
        ],
        'withdraw' => [
            'label' => 'Rút tiền',
            'direction'  => 'out',
            'content' => 'Rút tiền khỏi ví',
        ],
    ],

];
