<?php

namespace App\Traits\Model;

trait BaseTrait
{
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format($this->getDateFormat());
    }
}
