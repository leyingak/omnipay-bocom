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

}