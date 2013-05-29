<?php

namespace Patchwork\Helper;

class Tools
{
    public static function password($length = 8)
    {
        $pass = '';

        while (strlen($pass) < $length) {
            $chr = rand(48, 122);

            if (($chr <= 57) || (($chr >= 65) && ($chr <= 90)) || ($chr >= 97)) {
                $pass .= chr($chr);
            }
        }

        return $pass;
    }


    
    public static function vulgarize($s)
    {
        return trim(preg_replace('~(-+)~', '-', preg_replace('~([^a-z0-9-]*)~', '', preg_replace('~((\s|\.|\')+)~', '-', html_entity_decode(preg_replace('~&(a|o)elig;~', '$1e', preg_replace('~&([a-z])(uml|acute|grave|circ|tilde|ring|cedil|slash);~', '$1', strtolower(htmlentities($s, ENT_COMPAT, 'utf-8')))), ENT_COMPAT, 'utf-8')))), '-');
    }



    public static function strfdate($date, $format)
    {
        return mb_convert_case(strftime($format, strtotime($date)), MB_CASE_TITLE);
    }


    
    public static function isLeap($year)
    {
        return ((bool) date('L', strtotime($year.'-01-01')));
    }


    
    public static function dayCount($month, $year = 0)
    {
        if (! $year) {
            $year = date('Y');
        }

        return ((int) date('t', strtotime($year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-01')));
    }



    public static function twitter($url, $text = '')
    {
        return 'javascript:void(0);" onclick="window.open(\'http://twitter.com/share?url='.rawurlencode($url).'&amp;text='.rawurlencode($text).'\', \'\', \'directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,width=640,height=435\');';
    }



    public static function facebook($url)
    {
        return 'javascript:void(0);" onclick="window.open(\'http://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url).'\', \'\', \'directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,width=640,height=350\');';
    }
}
