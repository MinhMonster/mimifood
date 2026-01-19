<?php

namespace App\Traits;

trait HasThumbnail
{
    /**
     * UI thumbnail dùng chung cho tất cả model có images[]
     */
    public function getThumbnailAttribute()
    {
        if (!isset($this->images) || !is_array($this->images)) {
            return null;
        }

        return $this->images[0] ?? null;
    }
}
