<?php

namespace Omnipay\Bocom\Contract;

interface ResponseContract
{

    public function isBizSuccessful();

    public function isTradeSuccessful();

}