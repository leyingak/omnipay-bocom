<?php

namespace Omnipay\Bocom\Util;

class ArrUtil
{

    public static function toJsonString($items)
    {
        return json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function toObject($jsonString)
    {
        return json_decode($jsonString);
    }

    public static function toArray($jsonString)
    {
        return json_decode($jsonString, true);
    }

}