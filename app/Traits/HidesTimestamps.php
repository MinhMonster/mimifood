<?php

namespace App\Traits;

trait HidesTimestamps
{
    public function getHidden()
    {
        return array_merge(parent::getHidden(), [
            'password',
            'author_id',
            'purchase_price',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
    }
}
