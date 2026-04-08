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
        'purchase_account_installment_first' => [
            'label'     => 'Mua nick trả góp - lần 1',
            'direction' => 'out',
            'content'   => 'Thanh toán mua nick trả góp lần 1',
        ],
        'purchase_account_installment_second' => [
            'label'     => 'Mua nick trả góp - lần 2',
            'direction' => 'out',
            'content'   => 'Thanh toán mua nick trả góp lần 2',
        ],

        'purchase_account_deposit' => [
            'label'     => 'Đặt cọc mua nick',
            'direction' => 'out',
            'content'   => 'Thanh toán đặt cọc mua nick',
        ],

        'purchase_account_deposit_completed' => [
            'label'     => 'Thanh toán còn lại sau đặt cọc',
            'direction' => 'out',
            'content'   => 'Thanh toán phần còn lại cho đơn đặt cọc mua nick',
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
        'refund_account_deposit' => [
            'label' => 'Hoàn tiền đặt cọc mua nick',
            'direction'  => 'in',
            'content' => 'Hoàn tiền cho đơn đặt cọc mua nick',
        ],
        'refund_account_installments' => [
            'label' => 'Hoàn tiền mua nick trả góp',
            'direction'  => 'in',
            'content' => 'Hoàn tiền cho đơn hàng mua nick trả góp',
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
