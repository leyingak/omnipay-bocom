<?php

namespace Omnipay\Bocom\Response;

use Omnipay\Bocom\Domain\BaseDto;

/**
 * @method getRspBizContent()
 * @method getSign()
 */
class BocomResponse extends BaseDto
{

    protected function schema()
    {
        return [
            'rsp_biz_content' => ['class' => BocomDataResponse::class],
            'sign'            => 'string',
        ];
    }

}