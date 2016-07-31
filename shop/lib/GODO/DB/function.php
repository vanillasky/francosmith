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
 * GODO_DB_function Abstraction
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_function {

	/**
	 * DB 인스턴스
 	 * @var GODO_DB
	 */
	protected $db;

	/**
	 * Construct
	 * @param GODO_DB
 	 * @return void
	 */
	public function __construct(&$db) {
		$this->db = $db;
	}

	/**
	 * 문자열을 quote 처리 함
	 * @param string $value
	 * @return string
	 */
	protected function quote($value) {
		return $this->db->quote($value);
	}

	/**
     * 회피문자(_) 가 붙지 않은 문자열을 quote 처리하여 리턴 (ex: test123 -> 'test123')
     * 회피문자(_) 가 붙은 경우, 이를 제거하고 리턴 (ex: _ordno-> ordno)
	 * @param string $value
	 * @return string
	 */
	protected function getValue($value) {

		if (preg_match('/^_(.+)$/',$value,$matches)) $value = $matches[1];
		else $value = $this->quote($value);

		return $value;

	}

	/**
	 * description.
	 * @param string $val1
	 * @param string $val2
	 * @return string
	 */
	abstract public function left($val1, $val2);


	/**
	 * description.
	 * @param string $value
	 * @return
	 */
	abstract public function sum($value);

	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function count($value);

	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function min($value);

	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function max($value);


	/**
	 * description.
	 * @param string $val1
	 * @param string $val2
	 * @return string
	 */
	abstract public function concat($val1, $val2);


	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function password($value);

	/**
	 * description.
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	abstract public function date_format($date, $format);

}

?>
