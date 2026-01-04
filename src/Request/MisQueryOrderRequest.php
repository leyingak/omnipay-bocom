<?php

namespace Omnipay\Bocom\Request;

class MisQueryOrderRequest extends AbstractH5Request
{

    public function isNeedEncrypt()
    {
        return false;
    }

    public function getUriPath()
    {
        return '/api/walletpay/misQueryOrder/v1';
    }

    public function setRequestBody($data)
    {
        $data['mcht_id'] = $this->getMchId();
        return parent::setRequestBody($data);
    }

    public function validFields()
    {
        return [
            'trans_type'    => true,
            'orig_trace_no' => false,
            'sys_order_no'  => false,
            'mcht_order_no' => false,
            'mcht_id'       => true,
            'partner_id'    => false,
        ];
    }

}