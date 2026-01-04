<?php

namespace Omnipay\Bocom\Request;

class DownloadNewPosStmtRequest extends AbstractH5Request
{

    public function isNeedEncrypt()
    {
        return false;
    }

    public function getUriPath()
    {
        return '/api/download/downloadNewPosStmt/v1';
    }

    public function validFields()
    {
        return [
            'stmt_date'   => true,
            'isv_no'      => false,
            'merch_code'  => false,
            'template_no' => false,
        ];
    }

}