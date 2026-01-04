<?php

namespace Omnipay\Bocom\Util;

use InvalidArgumentException;
use Omnipay\Common\Exception\RuntimeException;

final class RsaUtil
{

    const DEFAULT_PADDING = OPENSSL_PKCS1_PADDING;

    /**
     * Build a PEM formatted key.
     * $type: 'PUBLIC KEY' | 'PRIVATE KEY' | 'RSA PRIVATE KEY'
     */
    private static function toPem($rawKey, $type)
    {
        $rawKey = trim($rawKey);
        if (strpos($rawKey, 'BEGIN') !== false && strpos($rawKey, 'END') !== false) {
            return $rawKey;
        }

        $rawKey = str_replace(array("\r", "\n", " "), "", $rawKey);
        $body   = chunk_split($rawKey, 64, "\n");

        return "-----BEGIN $type-----\n$body-----END $type-----\n";
    }

    private static function opensslErrors()
    {
        $errs = array();
        while ($msg = openssl_error_string()) {
            $errs[] = $msg;
        }
        return $errs ? implode(' | ', $errs) : 'unknown openssl error';
    }

    private static function getPublicKey($publicKey)
    {
        $pem = self::toPem($publicKey, 'PUBLIC KEY');
        $res = openssl_pkey_get_public($pem);
        if ($res === false) {
            throw new RuntimeException('Invalid public key: ' . self::opensslErrors());
        }

        return $res;
    }

    /**
     * Try PRIVATE KEY (PKCS#8) first, then RSA PRIVATE KEY (PKCS#1).
     */
    private static function getPrivateKey($privateKey)
    {
        $pemPkcs8 = self::toPem($privateKey, 'PRIVATE KEY');
        $res = openssl_pkey_get_private($pemPkcs8);

        if ($res === false) {
            $pemPkcs1 = self::toPem($privateKey, 'RSA PRIVATE KEY');
            $res = openssl_pkey_get_private($pemPkcs1);
        }

        if ($res === false) {
            throw new RuntimeException('Invalid private key: ' . self::opensslErrors());
        }

        return $res;
    }

    /**
     * 公钥加密（支持分段）
     * 返回 base64(ciphertext)
     */
    public static function encrypt($content, $publicKey, $padding = self::DEFAULT_PADDING)
    {
        $pub = self::getPublicKey($publicKey);
        $details = openssl_pkey_get_details($pub);
        if (!$details || empty($details['bits'])) {
            throw new RuntimeException('Failed to read public key details.');
        }

        $keyBytes = intval($details['bits'] / 8);

        // PKCS1 padding 开销 11；OAEP 大约 42（仅粗略，实际以实现为准）
        $maxChunk = ($padding === OPENSSL_PKCS1_PADDING) ? ($keyBytes - 11) : ($keyBytes - 42);
        if ($maxChunk <= 0) {
            throw new RuntimeException('Invalid key size for encryption.');
        }

        $encrypted = '';
        $chunks = str_split($content, $maxChunk);

        foreach ($chunks as $chunk) {
            $out = '';
            $ok = openssl_public_encrypt($chunk, $out, $pub, $padding);
            if (!$ok) {
                throw new RuntimeException('Encrypt failed: ' . self::opensslErrors());
            }
            $encrypted .= $out;
        }

        return base64_encode($encrypted);
    }

    /**
     * 私钥解密（支持分段）
     * 入参为 base64(ciphertext)
     * 返回明文 plaintext
     */
    public static function decrypt($base64Cipher, $privateKey, $padding = self::DEFAULT_PADDING)
    {
        $pri = self::getPrivateKey($privateKey);
        $details = openssl_pkey_get_details($pri);
        if (!$details || empty($details['bits'])) {
            throw new RuntimeException('Failed to read private key details.');
        }

        $cipher = base64_decode($base64Cipher, true);
        if ($cipher === false) {
            throw new InvalidArgumentException('Ciphertext is not valid base64.');
        }

        $keyBytes = intval($details['bits'] / 8);

        $decrypted = '';
        $chunks = str_split($cipher, $keyBytes);

        foreach ($chunks as $chunk) {
            $out = '';
            $ok = openssl_private_decrypt($chunk, $out, $pri, $padding);
            if (!$ok) {
                throw new RuntimeException('Decrypt failed: ' . self::opensslErrors());
            }
            $decrypted .= $out;
        }

        return $decrypted;
    }

    /**
     * 签名（SHA1 / SHA256）
     * $opensslAlgo: OPENSSL_ALGO_SHA1 或 OPENSSL_ALGO_SHA256
     * 返回 base64(signature)
     */
    public static function sign($content, $privateKey, $opensslAlgo = OPENSSL_ALGO_SHA256)
    {
        if ($opensslAlgo !== OPENSSL_ALGO_SHA1 && $opensslAlgo !== OPENSSL_ALGO_SHA256) {
            throw new InvalidArgumentException('Only support OPENSSL_ALGO_SHA1 or OPENSSL_ALGO_SHA256.');
        }

        $pri = self::getPrivateKey($privateKey);
        $signature = '';

        $ok = openssl_sign($content, $signature, $pri, $opensslAlgo);
        if (!$ok) {
            throw new RuntimeException('Sign failed: ' . self::opensslErrors());
        }

        return base64_encode($signature);
    }

    /**
     * 验签
     * 返回 true/false；openssl_verify 出错会抛异常
     */
    public static function verify($content, $base64Signature, $publicKey, $opensslAlgo = OPENSSL_ALGO_SHA256)
    {
        if ($opensslAlgo !== OPENSSL_ALGO_SHA1 && $opensslAlgo !== OPENSSL_ALGO_SHA256) {
            throw new InvalidArgumentException('Only support OPENSSL_ALGO_SHA1 or OPENSSL_ALGO_SHA256.');
        }

        $pub = self::getPublicKey($publicKey);
        $sig = base64_decode($base64Signature, true);
        if ($sig === false) {
            throw new InvalidArgumentException('Signature is not valid base64.');
        }

        $ret = openssl_verify($content, $sig, $pub, $opensslAlgo);
        if ($ret === -1) {
            throw new RuntimeException('Verify failed: ' . self::opensslErrors());
        }

        return $ret === 1;
    }

}