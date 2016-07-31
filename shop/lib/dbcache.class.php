<?php

/**
 * GODO_DB_cache 참고하여 수정
 *
 * SQL 질의 결과를 캐시하여, 같은 질의시 캐시된 내용을 리턴함
 * 2014년 10월 30일 패치일 기점으로 사용개시
 */
class dbcache
{
	/**
	 * DB캐시 환경정보
	 */
	private $_cacheConfig = array(
		'cacheUseType' => 'none',
	);

	/**
	 * DB캐시 사용여부
	 */
	private $isAvailable = false;

	/**
	 * cache 폴더
	 * @var string
	 */
	private $cachePath = null;

	/**
	 * DB캐시가 저장되는 default 경로
	 * @var string
	 */
	private $defaultPath = null;

	/**
	 * DB캐시가 저장되는 location 경로
	 * @var string
	 */
	private $locationPath = null;

	/**
	 * 캐시 만료 시간 (초)
	 * 30초로 고정
	 * @var integer
	 */
	private $cache_expire = 30;

	/**
	 *
	 */
	private $cacheNamePrefix = "__dbcache__";

	/**
	 * 생성자 (set cache save directory)
  	 * @return void
	 */
	public function __construct()
	{
		// DB캐시 환경정보 정의
		if (file_exists(dirname(__FILE__).'/../conf/cache.db.cfg.php')) {
			include dirname(__FILE__).'/../conf/cache.db.cfg.php';
			$this->_cacheConfig = $cacheConfig['db'];
		}

		// DB캐시 사용여부 정의
		if ($this->_cacheConfig['cacheUseType'] == 'default') {
			$this->isAvailable = true;
		}
		else {
			return;
		}

		// cache 폴더 정의
		$this->cachePath = G_CONST_DOCROOT . Core::DS . 'cache' . Core::DS;
		// cache 폴더 없으면 false
		if (!is_dir($this->cachePath)) {
			return;
		}

		// DB캐시 default 디렉토리 정의
		$this->defaultPath = $this->cachePath . 'DB' . Core::DS;

		// DB캐시 디렉토리 생성
		// step1. DB폴더 생성
		if ($this->defaultPath != '' && !is_dir($this->defaultPath)) {
			@mkdir($this->defaultPath, 0707);
			chmod($this->defaultPath, 0707);
		}

	}

	public function setLocation($loc) {

		$loc = trim($loc, '\\/');

		$this->locationPath = $this->defaultPath . $loc . Core::DS;

		// step2. 하위폴더 생성
		if (!is_dir($this->locationPath)) {
			@mkdir($this->locationPath, 0707);
			chmod($this->locationPath, 0707);
		}

		return $this;
	}

	/*
	 * DB캐시 환경정보 리턴 (환경정보 설정시 사용)
	 * @return array
	 */
	public function loadConfig()
	{
		return $this->_cacheConfig;
	}

	/**
	 * SQL 문의 해시를 리턴
 	 * @param string $sql
	 * @return string
	 */
	private function getCacheName( $sql )
	{
		return $this->cacheNamePrefix.md5( trim( $sql ) );
	}

	/**
	 * 캐시되어 있는 질의 결과를 리턴
	 * @param string $sql
 	 * @param string $lifeTime : 캐시만료시간 ($this->cache_expire 가 기본값)
	 * @return array or bool
	 */
	public function getCache( $sql , $lifeTime = null)
	{
		// 1.DB캐시 사용여부 검증
		if ($this->isAvailable !== true) {
			return false;
		}

		// 2. DB캐시 디렉토리 검증
		if ($this->locationPath == '') return false;
		else if (!is_dir($this->locationPath)) return false;
		else if (strpos($this->locationPath, $this->defaultPath) === false) return false;

		// 3. 캐시이름정의
		$cache_name = $this->getCacheName( $sql );

		// 4. 캐시만료시간 정의
		if($lifeTime === null) {
			$cache_expire = G_CONST_NOW - $this->cache_expire;
		} else {
			$cache_expire = G_CONST_NOW - $lifeTime;
		}

		// 5. 캐시파일이 있고, 캐시만료시간을 경과하지않았을때 캐시파일리턴
		if ( is_file( $this->locationPath . $cache_name ) && filemtime( $this->locationPath . $cache_name ) > $cache_expire ) {

			$result = '';

			if ( $fh = @fopen( $this->locationPath . $cache_name, 'r' ) ) {

				flock( $fh, LOCK_SH );
				if ( filesize( $this->locationPath . $cache_name ) > 0)
					$result = fread( $fh, filesize( $this->locationPath . $cache_name ) );
				flock( $fh, LOCK_UN );
				fclose( $fh );
			}

			return ( $result != '' ) ? unserialize( $result ) : false;
		}

		return false;
	}

	/**
	 * 질의 결과를 저장
 	 * @param string $sql
	 * @param array $result
	 * @return void
	 */
	public function setCache( $sql, $result = array() )
	{
		// 1.DB캐시 사용여부 검증
		if ($this->isAvailable !== true) {
			return;
		}

		// 2. DB캐시 디렉토리 검증
		if ($this->locationPath == '') return false;
		else if (!is_dir($this->locationPath)) return false;
		else if (strpos($this->locationPath, $this->defaultPath) === false) return false;

		// 3. 캐시이름정의
		$cache_name = $this->getCacheName( $sql );

		// 4. 캐시저장
		if (is_array($result) && count($result) > 0 ) {

			$contents = serialize( $result );

			if ( $fh = @fopen( $this->locationPath . $cache_name, 'w' ) ) {

				flock( $fh, LOCK_EX );
				fwrite( $fh, $contents );
				flock( $fh, LOCK_UN );
				fclose( $fh );
				@chmod( $this->locationPath . $cache_name, 0707 );
			}
		}
		else {
			@unlink( $this->locationPath . $cache_name );
		}
	}

	/**
	 * DB캐시갱신 (캐시파일 수정시간을 현 시점 30분전으로 변경)
	 * @param string $loc : 갱신할 캐시 폴더
	 * return void
	 */
	public function clearCache($loc = null)
	{
		// 1.DB캐시 사용여부 검증
		if ($this->isAvailable !== true) {
			return;
		}

		// 2. DB캐시 디렉토리 검증
		if ($this->defaultPath == '') return;
		else if (!is_dir($this->defaultPath)) return;

		// 지정된위치의 캐시만 갱신
		if ($loc !== null && $loc != '') {
			$this->locationPath = $this->defaultPath . $loc;
			// 3. 위치지정했을때, DB캐시 위치지정 디렉토리 검증
			if ($this->locationPath == '') return;
			else if (!is_dir($this->locationPath)) return;

			foreach(glob($this->locationPath.'/*') as $cacheFile) {
				// 캐시경로에 "$this->cachePath"가 포함되어있고, 캐시이름에 prefix가 포함되어있는지 체크 후, 파일삭제
				if ((strpos($cacheFile, $this->cachePath) !== false) && (strpos($cacheFile, $this->cacheNamePrefix) !== false)) {
					@unlink( $cacheFile );
				}
			}
		// 전체캐시갱신
		} else {
			foreach(glob($this->defaultPath.'*') as $cacheFolder) {

				foreach(glob($cacheFolder.'/*') as $cacheFile) {
					// 캐시경로에 "$this->cachePath"가 포함되어있고, 캐시이름에 prefix가 포함되어있는지 체크 후, 파일삭제
					if ((strpos($cacheFile, $this->cachePath) !== false) && (strpos($cacheFile, $this->cacheNamePrefix) !== false)) {
						@unlink( $cacheFile );
					}
				}
			}
		}
	}
}
?>
