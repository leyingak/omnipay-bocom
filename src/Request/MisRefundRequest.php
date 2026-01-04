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

    public function validFields()
    {
        return [
            'orig_trace_no'      => false,
            'sys_order_no'       => false,
            'orig_mcht_order_no' => false,
            'refund_amount'      => true,
            'mcht_id'            => true,
            'refund_amount_type' => false,
            'extend_info'        => false,
            'mcht_order_no'      => false,
            'notify_url'         => false,
            'partner_id'         => false,
        ];
    }

}