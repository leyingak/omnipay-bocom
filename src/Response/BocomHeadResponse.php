<?php

namespace Omnipay\Bocom\Response;

use Omnipay\Bocom\Domain\BaseDto;

/**
 * @method getTranscode()
 * @method getTermTransTime()
 * @method getResponseCode()
 * @method getResponseMsg()
 * @method getRemark()
 */
class BocomHeadResponse extends BaseDto
{

    protected function schema()
    {
        return [
            'transcode'       => 'string',
            'term_trans_time' => 'string',
            'response_code'   => 'string',
            'response_msg'    => 'string',
            'remark'          => 'string',
        ];
    }

}