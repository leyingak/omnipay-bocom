<?php

namespace Omnipay\Bocom\Response;

use Omnipay\Bocom\Common\Constants;
use Omnipay\Bocom\Contract\ResponseContract;
use Omnipay\Bocom\Domain\BaseDto;
use Omnipay\Bocom\Request\AbstractH5Request;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * @method getBizState()
 * @method getRspCode()
 * @method getRspMsg()
 * @method BocomHeadResponse getRspHead()
 * @method getRspBody()
 */
class BocomDataResponse extends BaseDto implements ResponseContract
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

    public function isSuccessful()
    {
        return $this->isBizSuccessful() && $this->isTradeSuccessful();
    }

    public function getData()
    {
        return $this->toArray();
    }

    public function isBizSuccessful()
    {
        return Constants::RESPONSE_STATE_SUCCESS == $this->getBizState();
    }

    public function isTradeSuccessful()
    {
        $head = $this->getRspHead();
        if (empty($head)) {
            return false;
        }

        return Constants::TRADE_RESPONSE_CODE_SUCCESS == $head->getResponseCode();
    }

    public function getErrCode()
    {
        $head = $this->getRspHead();
        if (empty($head)) {
            return $head->getResponseCode();
        }

        return '';
    }

    public function getMessage()
    {
        $head = $this->getRspHead();
        if (empty($head)) {
            return $head->getResponseMsg();
        }

        return '';
    }

    protected function schema()
    {
        return [
            'biz_state' => 'string',
            'rsp_code'  => 'string',
            'rsp_msg'   => 'string',
            'rsp_head'  => ['class' => BocomHeadResponse::class],
            'rsp_body'  => 'array',
        ];
    }

}