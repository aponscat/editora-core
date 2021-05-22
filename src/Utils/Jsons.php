<?php

namespace Omatech\Editora\Utils;

class Jsons
{
    public static function mapSerialize($var)
    {
        if ($var===null) {
            return null;
        }
        $res=[];
        if (is_array($var)) {
            foreach ($var as $obj) {
                $res+=$obj->jsonSerialize();
            }
        } else {
            $res+=$var->jsonSerialize();
        }
        return $res;
    }
}
