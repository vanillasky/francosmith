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
 * GODO_DB_utility
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_utility {

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
	 * 인스턴스 복제
	 * @return GODO_DB_builder
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * 총 레코드 수를 리턴
	 * @param GODO_DB_builder $builder
	 * @return
	 */
	abstract protected function getTotalCount(GODO_DB_builder $builder);

}
?>