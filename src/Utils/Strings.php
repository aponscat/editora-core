<?php

namespace Omatech\Editora\Utils;

class Strings
{
    public static function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }


    public static function substringBefore($haystack, $needle)
    {
        if ($i=stripos($haystack, $needle)) {
            return(substr($haystack, 0, $i));
        } else {
            return $haystack;
        }
    }

    public static function substringAfter($haystack, $needle)
    {
        if ($i=strripos($haystack, $needle)) {
            return(substr($haystack, $i+1));
        } else {
            return $haystack;
        }
    }
}
