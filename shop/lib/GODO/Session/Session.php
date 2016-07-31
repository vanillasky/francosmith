<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Session
 */

/**
 * Sessions
 * @see scheme.sql
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Session
 */
final class Sessions {

	/**
	 * DB 인스턴스 (DB 세션을 사용할 경우 설정됨)
	 * @var GODO_DB
	 */
	private $DB = null;

	/**
	 * 세션 환경 설정
	 * @var array
	 */
	private $config = null;

	/**
	 * 세션 변경 여부를 판단키 위한 crc32
	 * @var integer
	 */
	private $checksum = null;

	/**
	 * 세션 사용 환경을 설정
	 * @return
	 */
	public function __construct() {

		$this->config = Core::config( 'session' );

		// db 세션
		if ( $this->config['use_db'] ) {

			$this->DB = Core::loader( 'GODO_DB' );

			session_set_save_handler(
				array( $this, "open" ),
				array( $this, "close" ),
				array( $this, "read" ),
				array( $this, "write" ),
				array( $this, "destroy" ),
				array( $this, "gc" )
			);

		}
		// file 세션
		else {
			ini_set( "session.save_path", $_SERVER['DOCUMENT_ROOT'] . Core::DS . $this->config['savepath'] );
		}

		// session id 지정
		if ( isset( $_GET['sess_id'] ) && preg_match('/^[a-zA-Z0-9]+$/', $_GET['sess_id'] ))
			$this->reset_sessionID( $_GET['sess_id'] );

		ini_set( "session.gc_maxlifetime", $this->config['lifetime'] );

		session_start();

	}

	/**
	 * 세션을 기록하고 종료
	 * @return void
	 */
	public function __destruct() {

		session_write_close();

	}

	/**
	 * 세션 id 를 변경
	 	 * @param string $ssid 변경할 세션 id
	 * @return void
	 */
	private function reset_sessionID( $ssid ) {

		session_id( $ssid );

	}

	/**
	 * 현재 세션 데이터의 crc32(md5 보다 빠름) 체크섬을 구함
	 * @param string $data
	 * @return integer
	 */
	private function checksum( $data ) {

		$checksum = sprintf( '%u', crc32( $data ) );
		return $checksum;

	}

	/**
	 * 세션 데이터를 인코딩함
	 * @param string $data 세션 데이터
	 * @return string 인코딩된 세션 데이터
	 */
	private function encode( $data ) {

		return $data;

	}

	/**
	 * 세션 데이터를 디코딩함
	 * @param string $data 세션 데이터
	 * @return string 디코딩된 세션 데이터
	 */
	private function decode( $data ) {

		return $data;

	}

	/**
	 * 세션 id 를 구함
	 * @param string $id
	 * @return string 세션 id
	 */
	private function getID( $id ) {

		return md5( $id );
	}

	/**
	 * 세션을 엶
	 * @param string $savepath
	 * @param string $sessionname
	 * @return true
	 */
	public function open( $savepath, $sessionname ) {

		return true;

	}

	/**
	 * 세션을 닫음
	 * @return true
	 */
	public function close() {

		return true;

	}

	/**
	 * 세션 데이터를 읽음
	 * @param string $id 세션 id
	 * @return string 세션 데이터
	 */
	public function read( $id ) {

		static $stmt = null;	// prepared statement

		if ($stmt === null) {
			$query = "SELECT data FROM gd_session WHERE id = ? AND expire >= ?";
			$stmt  = $this->DB->prepare( $query );
		}

		$stmt->execute( array(
			$this->getID( $id ), G_CONST_NOW
		) );

		if ( $result = $stmt->fetchArray() ) {
			$this->checksum = $this->checksum( $result[0] );
			return $this->decode( $result[0] );
		}

	}

	/**
	 * 세션 데이터 저장
	 * @param string $id 세션 id
	 * @param string $data 세션 데이터
	 * @return boolean
	 */
	public function write( $id, $data ) {

		static $u_stmt = null;	// prepared statement
		static $i_stmt = null;	// prepared statement

		$data = $this->encode( $data );

		if ( $this->checksum === $this->checksum( $data ) ) {

			if ($u_stmt === null) {

				$query = "
				UPDATE gd_session SET expire = ? WHERE id = ? AND expire > ?
				";
				$u_stmt  = $this->DB->prepare( $query );
			}

			$param = array(
				G_CONST_NOW + $this->config['lifetime'], $this->getID( $id ), G_CONST_NOW
			);

			$stmt = $u_stmt;

		}
		else {

			// @toto : mysql 전용 쿼리 이므로 수정할 필요가 있음
			if ($i_stmt === null) {

				$query = "
				INSERT INTO gd_session
					SET id = ?, data = ?, expire = ?

				ON DUPLICATE KEY UPDATE
					data = VALUES(data), expire = VALUES(expire)
				";
				$i_stmt  = $this->DB->prepare( $query );
			}

			$param = array(
				$this->getID( $id ),
				$data,
				G_CONST_NOW + $this->config['lifetime']
			);

			$stmt = $i_stmt;

		}

		return $stmt->execute( $param ) ? true : false;

	}

	/**
	 * 세션을 파괴함
	 * @param string $id 세션 id
	 * @return boolean
	 */
	public function destroy( $id ) {

		static $stmt = null;

		if ($stmt === null) {
			$query = "DELETE FROM gd_session WHERE id = ?";
			$stmt = $this->DB->prepare( $query );
		}

		return $stmt->execute( array( $this->getID( $id ) ) ) ? true : false;

	}

	/**
	 * 가비지 콜렉터 (만료된 세션을 모두 삭제함)
	 * @param string $maxlifetime
	 * @return boolean
	 */
	public function gc( $maxlifetime ) {

		static $stmt = null;

		if ($stmt === null) {
			$query = "DELETE FROM gd_session WHERE expire  < ?";
			$stmt = $this->DB->prepare($query);
		}

		return $stmt->execute( G_CONST_NOW ) ? true : false;

	}
}
?>
