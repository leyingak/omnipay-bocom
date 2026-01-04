<?php

namespace Omnipay\Bocom\Request;

class MisPreOrderRequest extends AbstractH5Request
{

    public function isNeedEncrypt()
    {
        return false;
    }

    public function getUriPath()
    {
        return '/api/walletpay/misPreOrder/v3';
    }

    public function setRequestBody($data)
    {
        $data['mch_id'] = $this->getMchId();
        return parent::setRequestBody($data);
    }

}