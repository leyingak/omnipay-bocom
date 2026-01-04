<?php

namespace Omnipay\Bocom;

use Omnipay\Bocom\Common\Constants;
use Omnipay\Bocom\Request\CompletePurchaseRequest;
use Omnipay\Bocom\Request\MisPreOrderRequest;
use Omnipay\Bocom\Request\MisQueryOrderRequest;
use Omnipay\Bocom\Request\MisRefundRequest;

class H5PayGateway extends AbstractGateway
{

    public function getName()
    {
        return 'BoCom H5 Pay Gateway';
    }

    public function purchase($options = [])
    {
        return $this->createRequest(MisPreOrderRequest::class, $options);
    }

    public function query($options = [])
    {
        if (!isset($options['trans_type'])) {
            $options['trans_type'] = Constants::TRADE_TYPE_CONSUME;
        }

        return $this->createRequest(MisQueryOrderRequest::class, $options);
    }

    public function completePurchase($options = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    public function refund($options = [])
    {
        return $this->createRequest(MisRefundRequest::class, $options);
    }

    public function queryRefund($options = [])
    {
        $options['trans_type'] = Constants::TRADE_TYPE_REFUND;
        return $this->createRequest(MisQueryOrderRequest::class, $options);
    }

}