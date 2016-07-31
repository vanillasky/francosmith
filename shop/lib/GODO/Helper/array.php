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
 * GODO_helper_array
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_array extends GODO_helper {

	/**
	 * 무작위로 섞은 배열을 리턴 (숫자, 문자키 혼용 가능)
	 * @param array $array
	 * @return array
	 */
	public function shuffle($array) {

		$_keys = array_keys($array);
		shuffle($_keys);

		$ret = array();

		foreach ($_keys as $k) {
			$ret[$k] = $array[$k];
		}

		return $ret;

	}

}
?>