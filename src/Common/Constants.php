<?php

namespace Omnipay\Bocom\Common;

class Constants
{


    const ENV_PRODUCTION = 'production';
    const ENV_SANDBOX = 'sandbox';

    const RESPONSE_STATE_SUCCESS = 'S';
    const RESPONSE_STATE_PENDING = 'P';
    const RESPONSE_STATE_FAILED = 'F';

    const TRADE_TYPE_CONSUME = '01';
    const TRADE_TYPE_REFUND = '02';

    const BOCOM_NOTIFY_SUCCESS = 'SUCCESS';

    const TRADE_RESPONSE_CODE_SUCCESS = 'CIPP0004PY0000';

    const REQUEST_BODY_KEY = 'requestBody';

    const REQUEST_BODY_MCH_ID_KEY = 'mch_id';
    const REQUEST_BODY_MCHT_ID_KEY = 'mcht_id';

}