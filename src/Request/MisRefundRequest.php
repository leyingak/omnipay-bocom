<?php

namespace Omnipay\Bocom\Request;

class MisRefundRequest extends AbstractH5Request
{

    public function isNeedEncrypt()
    {
        return false;
    }

    public function getUriPath()
    {
        return '/api/walletpay/misRefund/v1';
    }

    public function setRequestBody($data)
    {
        $data['mcht_id'] = $this->getMchId();
        return parent::setRequestBody($data);
    }

}