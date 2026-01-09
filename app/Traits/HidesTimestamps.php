<?php

namespace App\Traits;

trait HidesTimestamps
{
    public function getHidden()
    {
        return array_merge(parent::getHidden(), [
            'author_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
    }
}
