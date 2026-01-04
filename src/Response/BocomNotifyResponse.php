<?php

namespace Omnipay\Bocom\Response;

use Omnipay\Bocom\Common\Constants;
use Omnipay\Bocom\Contract\ResponseContract;
use Omnipay\Bocom\Domain\BaseDto;

/**
 * @method bool getTradeState()
 * @method string getOutTradeNo()
 * @method string getOrderId()
 * @method string getTotalAmount()
 * @method string getPayTime()
 * @method string getFeeType()
 *
 */
class BocomNotifyResponse extends BaseDto implements ResponseContract
{

    public function isBizSuccessful()
    {
        return true;
    }

    public function isTradeSuccessful()
    {
        return Constants::BOCOM_NOTIFY_SUCCESS == $this->getTradeState();
    }

    protected function schema()
    {
        return [
            'communication_url' => 'string',
            'mch_wid'           => 'string',
            'mch_id'            => 'string',
            'order_id'          => 'string',
            'out_trade_no'      => 'string',
            'trade_state'       => 'string',
            'fee_type'          => 'string',
            'total_amount'      => 'string',
            'pay_time'          => 'string',
        ];
    }
}