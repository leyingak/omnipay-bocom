<?php

namespace Omnipay\Bocom\Request;

use InvalidArgumentException;
use Omnipay\Bocom\Response\BocomNotifyResponse;
use Omnipay\Bocom\Util\AesUtil;
use Omnipay\Bocom\Util\ArrUtil;
use Omnipay\Bocom\Util\RsaUtil;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseRequest extends AbstractH5Request
{

    public function isNeedEncrypt()
    {
        return false;
    }

    public function getUriPath()
    {
        return '';
    }

    public function sendData($data = [])
    {
        return new BocomNotifyResponse($data, $this);
    }

    /**
     * @throws InvalidResponseException
     */
    public function getData()
    {
        $notifyParams = $this->getParameters();
        $bizContentStr = $this->toBizContentStr($notifyParams);

        $passed = $this->bocomPublicKeyApi->verify($bizContentStr, $notifyParams['sign']);
        if (!$passed) {
            throw new InvalidResponseException('Invalid notify');
        }

        $aesKey = RsaUtil::decrypt($notifyParams['encrypt_key'], $this->getPrivateKey());
        if (empty($aesKey)) {
            throw new InvalidResponseException('Invalid encrypt_key');
        }

        $plainText = AesUtil::decrypt($notifyParams['biz_content'], $aesKey);
        return ArrUtil::toArray($plainText);
    }

    private function toBizContentStr($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Invalid notify data");
        }

        $keys = ['biz_content', 'msg_id', 'timestamp', 'encrypt_key'];

        $parts = [];
        foreach ($keys as $k) {
            if (!array_key_exists($k, $data)) {
                throw new InvalidArgumentException("Missing field: $k");
            }
            $parts[] = json_encode($k, JSON_UNESCAPED_SLASHES) . ':' .
                json_encode($data[$k], JSON_UNESCAPED_SLASHES);
        }

        return implode(',', $parts);
    }

    public function validFields()
    {
        return null;
    }

}