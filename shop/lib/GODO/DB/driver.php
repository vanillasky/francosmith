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
 * GODO_DB_driver
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
class GODO_DB_driver {

	/**
	 * 오류 발생시 화면 출력 여부
	 * @var boolean
	 */
	public $silent = false;

	/**
	 * DB 연결의 고정 여부
	 * @var boolean
	 */
	private $fixed = false;

	/**
	 * 최대 실행 시간 (지나면 새로운 DB 연결을 생성)
	 * @var integer
	 */
	private $timeout = 30;


	/**
	 * 마지막 DB 연결 사용 시간
	 * @var integer
	 */
	protected $connectTime = 0;

	/**
	 * replication 사용 여부 (추가된 서버가 2개 이상일 경우 true 로 설정됨)
	 * @var boolean
	 */
	private $replication = false;

	/**
	 * GODO_DB_statement 인스턴스
	 * @var GODO_DB_statement
	 */
	private $statement = null;

	/**
	 * 현재 접속할(사용할) DB 번호
	 * @var integer
	 */
	private $seq = null;

	/**
	 * 사용할 DB 연결 링크
	 * @var resource
	 */
	protected $dbconn = null;

	/**
	 * 생성된 DB 연결
	 * @var resource
	 */
	protected $dbconns = array();

	/**
     * 스코프
	 * @var integer
	 */
	private $scope = 0;

	/**
	 * 트랜잭션 여부
	 * @var boolean
	 */
	protected $transaction = false;

	/**
	 * 인스턴스 복제 방지
	 * @return
	 */
	public function __clone() {}

	/**
	 * Construct
 	 * @return void
	 */
	public function __construct() {

		$this->setScope();

		$driver = strtolower($this->getName());
		$class_name = sprintf('GODO_DB_driver_%s_statement', $driver);

		if (class_exists($class_name, true)) {	// autoload
			$this->statement = new $class_name;
		}
		else {
			Core::raiseError('Prepared Stetement 를 사용할 수 없음');
		}

	}

	/**
	 * Destruct
 	 * @return void
	 */
	public function __destruct() {}


    /**
     * DB 드라이버 스코프를 설정
     * @return void
     */
	private function setScope() {

		static $scope = 0;

		$scope++;

		$this->scope = $scope;

	}

	/**
	 * 접속할 서버 정보를 설정하거나, 설정된 정보를 가져옴
 	 * @param string $mode [optional]
	 * @param array $data [optional]
	 * @return array database server infomation
	 */
	public function server( $mode = 'get', $data = null ) {

		static $servers = array();

		if ( $mode == 'set' && $data ) {

			if (!isset($servers[$this->scope]))
				$servers[$this->scope] = array();

			if (!in_array( $data, $servers[$this->scope] ))
				$servers[$this->scope][] = $data;

			if (sizeof($servers[$this->scope]) > 1) $this->replication = true;
		}
		else {
			return is_int($data) ? $servers[$this->scope][$data] : $servers[$this->scope];
		}

	}

	/**
	 * 모든 DB 연결을 닫음
	 * @return void
	 */
	public function disconnects() {
		$this->closeAll();
	}

	/**
	 * DB 연결을 고정하여 동작하도록 설정
	 * @param boolean $bool [optional]
	 * @return void
	 */
	public function setFixedMode( $bool = true ) { // true or false

		$this->fixed = (boolean)$bool;
	}

	/**
	 * DB 연결을 생성하거나, 생성된 연결을 리턴
	 * @param object $rw [optional]
	 * @return resource database connection
	 */
	protected function getDBconn( $rw = 'w' ) {

		$now = time();

		// 쓰기 모드 일때, db 를 고정하여, 이후 읽기 모드시 새로운 연결을 생성하지 않도록 함
		if ( $rw == 'w' || $this->fixed === true ) {
			$this->setFixedMode( true );
			$this->seq = 0;
		}
		else {
			$this->seq = mt_rand( 0, sizeof( $this->server( 'get' ) ) - 1 );
		}

		if ( !isset( $this->dbconns[$this->seq] ) || $this->timeout <= ( $now - $this->connectTime ) ) {
			$this->dbconns[$this->seq] = $this->connect( $this->server( 'get', $this->seq ) );
			$this->connectTime = $now;
		}

		$this->dbconn = $this->dbconns[$this->seq];

		return $this->dbconn;
	}

	/**
	 * 모든 DB 연결을 끊음
	 * @return void
	 */
	public function closeAll() {

		for ( $i = 0, $m = sizeof( $this->dbconns ); $i < $m; $i++ ) {
			$dbconn = $this->dbconns[$i];
			if ( $this->disconnect( $dbconn ))
				unset( $this->dbconns[$i] );
		}
	}

	/**
	 * 쿼리가 읽기 / 쓰기 속성인지 체크
 	 * @param string $sql
	 * @return string r : 읽기, w : 쓰기
	 */
	private function rwCheck( $sql ) {

		$sql = trim( $sql );
		switch ( strtoupper( substr( $sql, 0, 4 ) ) ) {
			case 'SELE' :	// select
			case 'EXPL' :	// explain
			case 'SHOW' :	// show
			case 'DESC' :	// describe
				return 'r';
				break;
			default :
				return 'w';
				break;
		}
	}

	/**
	 * 질의할 SQL, DB 연결 후 GODO_DB_statement 인스턴스 리턴
 	 * @param string $sql
	 * @return GODO_DB_statement
	 */
	public function prepare( $sql ) {

		$rw = $this->rwCheck($sql);

		$stmt = clone $this->statement;
		$stmt->readOnly = $rw == 'r' ? true : false;
		$stmt->PREPARED_STATEMENT = $sql;
		$stmt->values = array();
		$stmt->setDbconn( $this->getDBconn($rw) );

		return $stmt;
	}

	/**
	 * query 메서드의 별칭
	 * @return mixed
	 */
	public function execute() {
		$arguments = func_get_args();
		$result = call_user_func_array(array($this, 'query'), $arguments);
		return $result;
	}

	/**
	 * SQL 문을 실행하여 결과를 리턴
	 * 추가 파라미터 입력시, 그 값을 bind 하여 질의
	 * @param string $sql
	 * @return mixed
	 * @todo 캐시 사용시 질의 결과를 저장 or 리턴 하는 코드 추가
	 */
	public function query( $sql ) {

		// prevent execute empty SQL
		if (empty($sql)) return false;

		// check arguments
		if (func_num_args() > 1) {
			$args = func_get_args(); unset($args[0]);
		}
		else {
			$args = null;
		}

		$_cache = false;

		// cache trigger ( /*sql_file_cache*/ )
		if (strpos($sql,'/*sql_file_cache*/') === 0) {
			$_cache = true;
		}

		if ($_cache) {
			if ($this->cache()->hasCache($sql) && ($rs = $this->cache()->getCache($sql)) !== false) {
				return $rs;
			}
		}

		$stmt = $this->prepare($sql);
		$stmt->execute($args);
		$rs   = $stmt->getResultResource();

		if ($rs === false && $this->silent !== true && G_CONST_DEVELOPER_MODE) {

			$err   = array();
			if ($who = Core::whoisCallMe()) {
				$err[] = vsprintf('%s @ %d line', $who);	// file, line
			}

			$err[] = sprintf( '<strong>%d : %s</strong>', $this->errorCode(), $this->errorInfo() );
			$err[] = $this->sqlBeautifier( $sql , false);

			Core::raiseError($err);

		}
		else if ($_cache) {
			$this->cache()->setCache($sql, $stmt);
		}

		return $rs;
	}

	protected function cache() {
		static $_cache = null;

		if (is_null($_cache)) {
			$_cache = new GODO_DB_cache();
		}

		return $_cache;
	}

	/**
	 * 포맷팅된 sql 문을 리턴
	 * @todo sql 포맷팅 코드 작성
	 * @param string  $sql sql query
	 * @param boolean $html [optional] true : 예쁘게 색을 씌운 html, false : 텍스트
	 * @return string
	 */
	protected function sqlBeautifier( $sql, $html = true ) {

		return trim($sql);
	}

	/**
	 * 트랜잭션 여부를 리턴
	 * @return boolean
	 */
	public function hasTransaction()
	{
		return $this->transaction;
	}

}

?>
