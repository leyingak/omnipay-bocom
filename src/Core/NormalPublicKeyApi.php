<?php

namespace Omnipay\Bocom\Core;

use Omnipay\Bocom\Util\RsaUtil;

class NormalPublicKeyApi extends BocomPublicKeySpi
{
    private $publicKey;

    /**
     * @param $publicKey
     */
    public function __construct($publicKey)
    {
        $this->publicKey = $publicKey;
    }


    public function encrypt($content)
    {
        return RsaUtil::encrypt($content, $this->publicKey);
    }

    public function verify($content1, $content2)
    {
        return RsaUtil::verify($content1, $content2, $this->publicKey, $this->encryptType);
    }

}