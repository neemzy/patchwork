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
} 
