<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */

/**
 * GODO_helper_crypt
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_crypt extends GODO_helper {

	/**
	 * 암호화
	 * @param string $str
	 * @param string $key [optional]
	 * @return string
	 */
	public function crypt($str, $key='') {

		if ($str === '') return;

		$key = $key ? $key : $this->_getkey();
		$key = ($m = ceil(strlen($str) / strlen($key))) > 1 ? str_repeat($key, $m) : $key;
		$enc = ~(string)$str ^ (string)$key;

		return $enc;
	}

	/**
	 * 복호화
	 * @param string $str
	 * @param string $key [optional]
	 * @return string
	 */
	public function decrypt($str, $key='') {

		if ($str === '') return;

		$key = $key ? $key : $this->_getkey();
		$key = ($m = ceil(strlen($str) / strlen($key))) > 1 ? str_repeat($key, $m) : $key;
		$dec = ~(string)$str ^ (string)$key;

		return $dec;

	}

	/**
	 * 암호화 키를 생성함
	 * @return string
	 */
	private function _getkey() {

		static $enc_key = null;

		if ($enc_key === null) {

			$tmp = ip2long($_SERVER['SERVER_ADDR']);

			$key = $_SERVER['DOCUMENT_ROOT'];

			$sha = sha1($key);
			$md5 = md5($key);
			$key = $sha.$md5;

			$s_point = $tmp % strlen($key);

			$r_key = substr($key, $s_point).substr($key,0,$s_point);
			$enc_key = $r_key . str_pad(base_convert($s_point, 10, 16),2,'0',STR_PAD_LEFT);
		}

		return $enc_key;
	}

}
?>