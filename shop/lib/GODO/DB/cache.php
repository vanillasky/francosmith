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
 * GODO_DB_cache
 *
 * SQL 질의 결과를 캐시하여, 같은 질의시 캐시된 내용을 리턴함
 * 현재, 사용하지 않음
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_cache {

	/**
	 * 캐시 사용 여부
	 * @var boolean
	 */
	private $use = true;

	/**
	 * 캐시를 저장할 경로
	 * @var string
	 */
	private $path;

	/**
	 * 캐시 만료 시간 (초)
	 * @var integer
	 */
	private $cache_expire = 300;

	/**
	 * 생성자 (set cache save directory)
 	 * @return void
	 */
	public function __construct() {
		$this->path = G_CONST_DOCROOT . Core::DS . 'cache' . Core::DS . 'DB' . Core::DS;
		if (!is_dir($this->path)) {
			@mkdir($this->path, 0707);
		}
	}

	/**
	 * 캐시 사용 여부를 리턴
 	 * @return boolean
	 */
	public function isAvailable() {

		return $this->use;
	}

	/**
	 * SQL 문의 해시를 리턴
 	 * @param string $sql
	 * @return string
	 */
	private function getCacheName( $sql ) {

		return md5( trim( $sql ) );
	}

	private function getCacheFilePath($sql)
	{
		$cache_name = $this->getCacheName( $sql );
		return $this->path . $cache_name;
	}


	/**
	 * 캐시되어 있는 질의 결과를 리턴
 	 * @param string $sql
	 * @return array
	 */
	public function getCache( $sql ) {

		if ( $this->hasCache($sql) ) {

			$result = '';

			if ( $fh = @fopen( $this->getCacheFilePath($sql), 'r' ) ) {

				flock( $fh, LOCK_SH );
				if ( filesize( $this->getCacheFilePath($sql) ) > 0)
					$result = fread( $fh, filesize( $this->getCacheFilePath($sql) ) );
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
	 * @param mixed GODO_DB_statement or string [optional]
	 * @return void
	 */
	public function setCache( $sql, $result = '' ) {

		$cache_name = $this->getCacheName( $sql );

		if ($result instanceof GODO_DB_statement) {
			$result = $result->fetchAll();
		}
		else {
			return false;
		}

			$contents = serialize( $result );

		if ( $fh = @fopen( $this->getCacheFilePath($sql), 'w' ) ) {

			flock( $fh, LOCK_EX );
			fwrite( $fh, $contents );
			flock( $fh, LOCK_UN );
			fclose( $fh );
			@chmod( $this->getCacheFilePath($sql), 0777 );
		}

	}

	public function hasCache($sql)
	{
		$cache_name = $this->getCacheName( $sql );

		if ( is_file( $this->getCacheFilePath($sql) ) ) {
			if ((filemtime($this->getCacheFilePath($sql)) + $this->cache_expire) >= G_CONST_NOW) {
				return true;
			}
			else {
				@unlink( $this->getCacheFilePath($sql) );
			}
		}

		return false;
	}

	public function setCacheExpire($expire = 300)
	{
		$this->cache_expire = $expire;
	}

}
?>
