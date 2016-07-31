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
 * GODO_DB_interface_statement
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
interface GODO_DB_interface_statement {

	/**
	 * 질의 결과의 리소스를 리턴
 	 * @return mixed resource or false
	 */
	public function getResultResource();

	/**
	 * 질의
 	 * @param string $sql
	 * @return mixed 쿼리 결과
	 */
	public function query( $sql );

	/**
	 * 질의 결과의 현재 포인터가 위치한 행을 fetch
	 * @param Integer $fetchstyle [optional]
	 * @return array
	 */
	public function fetch( $fetchstyle = 0 );

	/**
	 * 질의 결과의 모든 행을 fetch
	 * @param Integer $fetchstyle [optional]
	 * @return array
	 */
	public function fetchAll( $fetchstyle = 0 );

	/**
	 * 문자열을 escape
	 * @param string $string
	 * @return string
	 */
	public function escape( $string );

	/**
	 * 질의 결과의 레코드 수를 리턴
	 * @return integer
	 */
	public function rowCount();

	/**
	 * 질의 결과의 내부 포인터를 이동
	 * @param integer $field_offset 이동할 위치
	 * @return boolean
	 */
	public function dataSeek( $field_offset );

	/**
	 * 질의 결과를 배열로 리턴(혼용)
 	 * @return array
	 */
	public function fetchArray();

	/**
	 * 질의 결과를 배열로 리턴 (문자키)
 	 * @return array
	 */
	public function fetchAssoc();

	/**
	 * 질의 결과를 객체로 리턴
 	 * @return object
	 */
	public function fetchObject();

	/**
	 * 마지막 삽입된 pk 를 리턴
	 * @return integer
	 */
	public function lastID();

}

?>
