<?php

namespace App\Support\Concerns;

use App\Support\EncryptedId;

trait EncryptsRouteKey
{
    public function getRouteKey(): mixed
    {
        return EncryptedId::encrypt((string) parent::getRouteKey());
    }
}
