<?php

namespace Omnipay\Bocom\Response;

use Omnipay\Bocom\Common\Constants;
use Omnipay\Bocom\Contract\NotifyResponseContract;
use Omnipay\Bocom\Contract\ResponseContract;
use Omnipay\Bocom\Domain\BaseDto;
use Omnipay\Bocom\Request\AbstractH5Request;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * @method bool getTradeState()
 * @method string getOutTradeNo()
 * @method string getOrderId()
 * @method string getTotalAmount()
 * @method string getPayTime()
 * @method string getFeeType()
 *
 */
class BocomNotifyResponse extends BaseDto implements ResponseContract, NotifyResponseContract
{

    private $request;

    public function __construct($response, $request)
    {
        $this->request = $request;
        parent::__construct($response);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getRequestData()
    {
        if ($this->request instanceof AbstractH5Request) {
            try {
                return $this->request->getData();
            } catch (InvalidRequestException $ignored) {}
        }

        return null;
    }

    public function getData()
    {
        return $this->toArray();
    }

    public function getErrCode()
    {
        return '';
    }

    public function getMessage()
    {
        return '';
    }

    public function isSuccessful()
    {
        return $this->isBizSuccessful() && $this->isTradeSuccessful();
    }

    public function isBizSuccessful()
    {
        return true;
    }

    public function isTradeSuccessful()
    {
        return Constants::BOCOM_NOTIFY_SUCCESS == $this->getTradeState();
    }

    public function isPaid()
    {
        return $this->isTradeSuccessful();
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