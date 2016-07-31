<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */

/**
 * GODO_helper_cache
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_cache extends GODO_helper {

	/**
	 * 캐시 경로
	 * @var string
	 */
	private $cache_path;

	/**
	 * 캐시 경로를 설정 (없는 경우 생성)
     * @param mixed $sub_path 서브 디렉토리
	 * @return void
	 */
	public function __construct($sub_path = false) {

		$this->cache_path = G_CONST_DOCROOT . Core::DS . 'cache';

		if ($sub_path) {
			$this->cache_path .= Core::DS . $sub_path;
		}

		if (!is_dir($this->cache_path)) {
			@mkdir($this->cache_path, 0707);
            @chmod($this->cache_path, 0707);
		}

	}

	/**
	 * 캐시 이름을 리턴
	 * @param string $name
	 * @return string
	 */
	private function getCacheName($name) {
		return md5($name);
	}

	/**
	 * 캐시를 불러옴
	 * @param string $name	캐시 이름
	 * @param integer $expire [optional]	만료시간
	 * @return string	내용
	 */
	public function get($name, $expire=300) {	// 기본값 5분

		$_name = $this->getCacheName($name);
		$_file = $this->cache_path . Core::DS . $_name;

		$fh = Core::helper('File');

		return
			  $fh->mtime($_file) + $expire > G_CONST_NOW
			? $fh->get($_file)
			: false;

	}

	/**
	 * 캐시를 저장
	 * @param string $name	캐시이름
	 * @param string $contents [optional]	내용
	 * @return boolean
	 */
	public function set($name, $contents = '') {

		$_name = $this->getCacheName($name);
		$_file = $this->cache_path . Core::DS . $_name;

		$fh = Core::helper('File');

		return
			  $contents
			? $fh->set($_file, $contents, 0707)
			: false;

	}

}
?>
