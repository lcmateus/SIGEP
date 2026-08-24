<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class EncryptedId
{
    public static function encrypt(string $value): string
    {
        return rtrim(strtr(Crypt::encryptString($value), '+/', '-_'), '=');
    }

    public static function decrypt(string $payload): ?string
    {
        $base64 = strtr($payload, '-_', '+/');
        $base64 .= str_repeat('=', (4 - strlen($base64) % 4) % 4);

        try {
            return Crypt::decryptString($base64);
        } catch (DecryptException) {
            return null;
        }
    }
}
