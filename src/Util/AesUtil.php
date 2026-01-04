<?php

namespace Omnipay\Bocom\Util;

use RuntimeException;

class AesUtil
{

    public static function decrypt($encryptedB64, $keyB64)
    {
        if ($encryptedB64 === '') {
            return '';
        }

        $cipherBytes = base64_decode($encryptedB64, true);
        if ($cipherBytes === false) {
            throw new RuntimeException('Invalid base64 encrypted content');
        }

        $key = base64_decode($keyB64, true);
        if ($key === false) {
            throw new RuntimeException('Invalid base64 key');
        }

        $keyLen = strlen($key);
        if (!in_array($keyLen, [16, 24, 32], true)) {
            throw new RuntimeException("Invalid AES key length: {$keyLen} bytes");
        }

        $cipherName = '';
        if ($keyLen === 16) {
            $cipherName = 'AES-128-CBC';
        } elseif ($keyLen === 24) {
            $cipherName = 'AES-192-CBC';
        } else {
            $cipherName = 'AES-256-CBC';
        }

        $iv = str_repeat("\0", 16);

        $plain = openssl_decrypt($cipherBytes, $cipherName, $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            throw new RuntimeException('OpenSSL decrypt failed: ' . openssl_error_string());
        }

        return $plain;
    }

}