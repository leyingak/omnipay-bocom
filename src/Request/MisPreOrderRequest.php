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

    public function validFields()
    {
        return [
            'mch_id'           => true,
            'front_notify_url' => true,
            'title'            => false,
            'fee_type'         => false,
            'device_info'      => true,
            'time_expire'      => true,
            'total_amount'     => true,
            'sp_ip'            => false,
            'time_start'       => false,
            'trans_type'       => true,
            'out_trade_no'     => true,
            'notify_url'       => false,
            'detail'           => false,
            'mobile_phone'     => false,
            'partner_id'       => false,
            'shop_id'          => false,
            'cus_name'         => false,
            'cert_type'        => false,
            'cert_no'          => false,
            'modify_flag'      => false,
        ];
    }

}