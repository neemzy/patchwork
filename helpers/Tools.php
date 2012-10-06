<?php

class Tools
{
	// Password generation (uppercase and lowercase letters and digits)
	public function password($length = 8) {
		$pass = '';
		while (strlen($pass) < $length) {
			$chr = rand(48, 122);
			if (($chr <= 57) || (($chr >= 65) && ($chr <= 90)) || ($chr >= 97)) $pass .= chr($chr);
		}
		return $pass;
	}
	
	// URL-valid UTF-8 string conversion
	public function vulgarize($s) {
		return trim(preg_replace('~(-+)~', '-', preg_replace('~([^a-zA-Z0-9-]*)~', '', preg_replace('~(\s+)~', '-', str_replace('&amp;', 'et', html_entity_decode(preg_replace('~&(A|O|a|o)(E|e)lig;~', '$1e', preg_replace('~&([a-zA-Z])(uml|acute|grave|circ|tilde|ring|cedil|slash);~', '$1', htmlentities($s, ENT_COMPAT, 'utf-8')))))))), '-');
	}

	// Date conversion from any recognized format to the specified one
	public function strfdate($date, $format) {
		return mb_convert_case(strftime($format, strtotime($date)), MB_CASE_TITLE);
	}
	
	// Leap year determination
	public function isLeap($year) {
		return ((bool) date('L', strtotime($year.'-01-01')));
	}
	
	// Number of days in a month determination
	public function dayCount($month, $year = 0) {
		if (!$year) $year = date('Y');
		return ((int) date('t', strtotime($year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-01')));
	}

	// UTF-8 HTML mail sending
	public function htmail($from, $to, $subject, $message, $headers = '') {
		return mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, 'MIME-Version: 1.0'.PHP_EOL.'Content-type: text/html; charset=utf-8'.PHP_EOL.'From: '.$from.(($headers != '') ? PHP_EOL.$headers : ''));
	}

    // Facebook link generator
    public function fb_link($url, $text, $summary, $image)
    {
		return 'javascript:void(0);" onclick="window.open(\'http://www.facebook.com/sharer/sharer.php?s=100&amp;p%5Btitle%5D='.rawurlencode($text).'&amp;p%5Bsummary%5D='.rawurlencode($summary).'&amp;p%5Burl%5D='.rawurlencode($url).'&amp;p%5Bimages%5D%5B0%5D='.rawurlencode($image).'\', \'fb_share\', \'directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no,width=640,height=350\');';
    }

    // Twitter link generator
	public function tw_link($url, $text) {
		return 'http://twitter.com/share?url='.rawurlencode($url).'&amp;text='.rawurlencode($text);
	}

    // Google+ link generator
    public function gp_link($url, $text) {
        return 'https://plus.google.com/share?url='.rawurlencode($url);
    }
} 
