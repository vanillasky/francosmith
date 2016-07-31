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
 * GODO_DB_interface_driver
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
interface GODO_DB_interface_driver {

	/**
	 * 드라이버 이름을 리턴
	 * @return
	 */
	public function getName();

	/**
	 * DB 연결
	 * @param array $server
	 * @return void
	 */
	public function connect( $server );

	/**
	 * DB 연결을 끊음
	 * @param object $dbconn
	 * @return void
	 */
	public function disconnect( $dbconn );

	/**
	 * 에러 코드를 리턴
	 * @return integer
	 */
	public function errorCode();

	/**
	 * 에러 메시지를 리턴
	 * @return string
	 */
	public function errorInfo();

	/**
	 * 트랜잭션 시작
	 */
	public function begin();

	/**
	 * 트랜잭션 롤백
	 */
	public function rollback();

	/**
	 * 트랜잭션 커밋
	 */
	public function commit();

	/**
	 * 문자열을 이스케이프
	 * @param string $string
	 * @return string
	 */
	public function escape( $string );

	/**
	 * 마지막 삽입된 pk 를 리턴
	 * @return integer
	 */
	public function lastID();

	/**
	 * 테이블 구조를 리턴
	 * @param string $table_name
	 * @return array
	 */
	public function desc($table_name);

}

?>
