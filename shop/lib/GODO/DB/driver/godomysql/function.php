<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_driver_godomysql_function
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_driver_godomysql_function
								extends	GODO_DB_function
{

	/**
	 *
	 * @param string $value
	 * @return
	 */
	public function left($val1, $val2) {
		$val1 = $this->getValue($val1);
		return sprintf('LEFT(%s, %d)', $val1,$val2);
	}

	/**
	 *
	 * @param string $value
	 * @return
	 */
	public function sum($value) {
		$value = $this->getValue($value);
		return sprintf('SUM(%s)', $value);
	}

	/**
	 *
	 * @param string $value
	 * @return string
	 */
	public function count($value) {
		$value = $this->getValue($value);
		return sprintf('COUNT(%s)', $value);
	}

	/**
	 *
	 * @param string $value
	 * @return string
	 */
	public function min($value) {
		$value = $this->getValue($value);
		return sprintf('MIN(%s)', $value);
	}

	/**
	 *
	 * @param string $value
	 * @return string
	 */
	public function max($value) {
		$value = $this->getValue($value);
		return sprintf('MAX(%s)', $value);
	}

	/**
	 *
	 * @param string $val1
	 * @param string $val2
	 * @return string
	 */
	public function concat($val1, $val2) {
		$val1 = $this->getValue($val1);
		$val2 = $this->getValue($val2);
		return sprintf('CONCAT(%s, %s)', $val1,$val2);
	}

	/**
	 *
	 * @param string $value
	 * @return string
	 */
	public function password($value) {
		$value = $this->getValue($value);
		return sprintf('PASSWORD(%s)', $value);
	}

	/**
	 *
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	public function date_format($date, $format) {
		$date = $this->getValue($date);
		$format = $this->quote($format);
		return sprintf('DATE_FORMAT(%s, %s)', $date, $format);
	}

}
?>
