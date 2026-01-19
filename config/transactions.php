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
        'purchase_account' => [
            'label' => 'Thanh toán đơn hàng mua nick',
            'direction'  => 'out',
            'content' => 'Thanh toán cho đơn hàng mua nick',
        ],
        'purchase_carrot' => [
            'label' => 'Thanh toán đơn hàng Carrot',
            'direction'  => 'out',
            'content' => 'Thanh toán cho đơn hàng nạp Carrot',
        ],
        'purchase_ninja_coin' => [
            'label' => 'Thanh toán đơn hàng mua xu ninja',
            'direction'  => 'out',
            'content' => 'Thanh toán cho đơn hàng mua xu ninja',
        ],
        'refund' => [
            'label' => 'Hoàn tiền mua nick',
            'direction'  => 'in',
            'content' => 'Hoàn tiền cho đơn hàng mua nick',
        ],
        'refund_carrot' => [
            'label' => 'Hoàn tiền nạp  Carrot',
            'direction'  => 'in',
            'content' => 'Hoàn tiền cho đơn hàng nạp Carrot',
        ],
        'refund_ninja_coin' => [
            'label' => 'Hoàn tiền nạp mua xu ninja',
            'direction'  => 'in',
            'content' => 'Hoàn tiền cho đơn hàng mua xu ninja',
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
