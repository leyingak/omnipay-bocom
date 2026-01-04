<?php

namespace Omnipay\Bocom\Contract;

interface ResponseContract
{

    public function isBizSuccessful();

    public function isTradeSuccessful();

    public function isSuccessful();

    public function getErrCode();

    public function getMessage();

    public function getData();

    public function getRequestData();

}