<?php
/*
 * Clib_Filter
 *
 * @desc 각종 필터링 관련 함수를 제공한다. 제공되는 함수는 전부 static 으로 제공한다.
 */
final class Clib_Filter
{
	/*
	 * setFilter($variable)
	 * @desc 주어진 변수값을 필터링한다.
	 * @desc filter_var() 함수 사용 검토 필요
	 */
	public static function setFilter($val)
	{
		$filterResult = "";

		if (is_int($val)) {
			return intval($val);
		}
		else if (is_bool($val)) {
			return $val;
		}
		else if (is_numeric($val)) {
			return $val;
		}
		else if (is_string($val)) {
			return $val;
		}
		else if (is_object($val)) {
			return $val;
		}
		else if (is_array($val)) {
			return $val;
		}
		else {
			return "";
		}
	}

}
