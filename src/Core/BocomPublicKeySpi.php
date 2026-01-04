<?php

namespace Omnipay\Bocom\Core;

use Omnipay\Bocom\Common\Constants;

abstract class BocomPublicKeySpi
{
    protected $encryptType = OPENSSL_ALGO_SHA256;

    abstract public function encrypt($content);

    abstract public function verify($content1, $content2);

}