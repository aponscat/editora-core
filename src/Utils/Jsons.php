<?php

namespace Omatech\Editora\Utils;

class Jsons
{
    public static function mapSerialize($var)
    {
        if ($var===null) {
            return null;
        }

        if (is_array($var)) {
            $res=[];
            foreach ($var as $key=>$obj) {
                $res[$key]=$obj->jsonSerialize();
            }
            return $res;
        }
        return $var->jsonSerialize();
    }
}
