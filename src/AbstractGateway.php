<?php

namespace Omnipay\Bocom;

use Omnipay\Bocom\Common\Constants;
use Omnipay\Bocom\Request\CompletePurchaseRequest;
use Omnipay\Bocom\Request\MisPreOrderRequest;
use Omnipay\Common\AbstractGateway as CommonAbstractGateway;

abstract class AbstractGateway extends CommonAbstractGateway
{

    public function getDefaultParameters()
    {
        return [
            'environment' => 'production',
        ];
    }

    public function setEnvironment($environment)
    {
        return $this->setParameter('environment', $environment);
    }

    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }

    public function setPublicKey($publicKey)
    {
        return $this->setParameter('publicKey', $publicKey);
    }

    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
    }

    public function setPrivateKey($privateKey)
    {
        return $this->setParameter('privateKey', $privateKey);
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setAppId($value)
    {
        return $this->setParameter('appId', $value);
    }

    public function getAppId()
    {
        return $this->getParameter('appId');
    }

    public function setOpenPlatformPublicKey($value)
    {
        return $this->setParameter('openPlatformPublicKey', $value);
    }

    public function getOpenPlatformPublicKey()
    {
        return $this->getParameter('openPlatformPublicKey');
    }

    public function setMchId($value)
    {
        return $this->setParameter('mchId', $value);
    }

    public function getMchId()
    {
        return $this->getParameter('mchId');
    }

    protected function createRequest($class, array $parameters)
    {
        $requestBody = [
            Constants::REQUEST_BODY_KEY => $parameters
        ];

        if ($class == MisPreOrderRequest::class) {
            $requestBody[Constants::REQUEST_BODY_MCH_ID_KEY] = $this->getMchId();
        } else if ($class == CompletePurchaseRequest::class) {
            $requestBody = $parameters;
        } else {
            $requestBody[Constants::REQUEST_BODY_MCHT_ID_KEY] = $this->getMchId();
        }

        return parent::createRequest($class, $requestBody);
    }

}