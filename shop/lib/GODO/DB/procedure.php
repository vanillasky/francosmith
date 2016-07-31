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
 * GODO_DB_procedure
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_procedure {

	/**
	 * GODO_DB
	 * @var object
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
	 * 데이터 procedure 를 실행하고 결과를 리턴
	 * @return mixed
	 */
	public function procedure() {

		$args = func_get_args();
		return call_user_func_array(array($this, 'execute'), $args);

	}

	/**
	 * 데이터 procedure 를 실행하고 결과를 리턴
	 * @return mixed
	 */
	abstract protected function execute();

}
?>