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
            'type'  => 'increase',
            'content' => 'Nạp tiền vào ví',
        ],
        'purchase' => [
            'label' => 'Thanh toán đơn hàng',
            'type'  => 'decrease',
            'content' => 'Thanh toán cho đơn hàng #:order_id',
        ],
        'refund' => [
            'label' => 'Hoàn tiền',
            'type'  => 'increase',
            'content' => 'Hoàn tiền cho đơn hàng #:order_id',
        ],
        'admin_adjust_increase' => [
            'label' => 'Điều chỉnh tăng',
            'type'  => 'increase',
            'content' => 'Admin cộng tiền',
        ],
        'admin_adjust_decrease' => [
            'label' => 'Điều chỉnh giảm',
            'type'  => 'decrease',
            'content' => 'Admin trừ tiền',
        ],
        'withdraw' => [
            'label' => 'Rút tiền',
            'type'  => 'decrease',
            'content' => 'Rút tiền khỏi ví',
        ],
    ],

];
