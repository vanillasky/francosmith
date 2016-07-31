<?
if (class_exists('todayshop_cache', false)) return;
class todayshop_cache {

	var $process = array();
	var $dir = '';
	var $path = '';
	var $regen;
	var $expire;
	var $now;

	var $use = true;
	var $debug = false;
	var $php_error = array();

	function _error_handle($errno, $errstr, $errfile, $errline) {

		if ($this->debug === true) {
			$_error['errno'] = $errno;
			$_error['errstr'] = $errstr;
			$_error['errfile'] = $errfile;
			$_error['errline'] = $errline;

			$this->php_error[] = $_error;
		}

		return true;
	}

	function __construct($expire=300) {	// 초(=5분)

		$this->process['start'] = $this->_microtime();

		// 에러 제어;
		set_error_handler(array(&$this, '_error_handle'));

		$this->regen = true;
		$this->dir = $_SERVER['DOCUMENT_ROOT'].'/shop/data/todayshop/';
		$this->expire = $expire;
		$this->now = time();
		$this->fromcache = false;

		if ($expire < 0) return;

		/**
			세션에 기록된 사용자 등급을 가져와야 하므로 세션이 시작되지 않은 경우 시작시키며,
			save_path 가 변경된 경우 여기서도 바꿔 줘야 정상 동작합니다.
		 */
		$_session_started = session_id() ? true : false;

		if ($_session_started === false) {
			ini_set("session.save_path",$_SERVER['DOCUMENT_ROOT']."/session_tmp");
			ini_set("session.gc_maxlifetime", "18000");
			if(isset($_GET['sess_id']) && !empty($_GET['sess_id'])) {
				session_id($_GET['sess_id']);
			}
			session_start();
		}

		// 캐시
		if (($_cache = $this->_cache())) {
			$this->fromcache = true;

			$this->_print($_cache);	// 출력
		}
	}

	function __destruct() {

		$this->process['end'] = $this->_microtime();
		$this->process['exec'] = $this->process['end'] - $this->process['start'];

		if ($this->debug === true){
			echo '<xmp>';
			print_r($this->php_error);
			echo '</xmp>';

			if ($this->fromcache === true)
			echo "from    : CACHE <br>";
			echo "excuted : ".$this->process['exec']." sec <br>";
		}
	}

	function _header() {
		header("Content-Type: text/html; charset=EUC-KR");
	}

	function _print($_cache) {
		$this->_header();
		echo $_cache;
		exit;
	}

	function _cache() {

		if ($this->use !== true) return '';

		$_base = $_SERVER['REQUEST_URI'];
		$_ext	= $_SESSION['sess']['level'];

		$_tmp = explode('.',basename($_base));
		array_pop($_tmp);
		$_tmp = implode("",$_tmp);

		$_type = preg_replace('/[^a-z]/','',$_tmp);

		// 투데이샵 프론트에서만 사용
		if (dirname($_base) != '/shop/todayshop') return '';

		$html = '';

		$_tgsno = isset($_REQUEST['tgsno']) ? $_REQUEST['tgsno'] : '';

		// 상품번호_uri_레벨
		$_cache_name  = $_type;
		$_cache_name .= ($_tgsno) ? '_'.$_tgsno : '';
		$_cache_name .= '_'.md5($_base);
		$_cache_name .= ($_ext) ? '_'.$_ext : '';
		$_cache_name .= '.htm';

		$this->path = $this->dir.$_cache_name;

		if (is_file($this->path)) {

			$_limit = (filectime($this->path) + $this->expire) - $this->now;
			if ($_limit > 0) {	// 만료시간이 남으면 사용 가능
				$this->regen = false;
				$html = $this->getCache($this->path);
			}

		}

		return $html;
	}

	function &getInstance() {
		static $instance = null;
		if (null === $instance) $instance = new todayshop_cache(-1);
		return $instance;
	}

	/**
		remove, truncate 메서드는 직접 호출 가능하도록 싱글톤 패턴을 사용.
	 */
	function remove($tgsno='',$type='') {

		$class = &todayshop_cache::getInstance();
		if(is_object($class)) $class->_remove($tgsno,$type);
	}

	function _remove($tgsno='',$type='') {

		if ($this->use !== true) return;
		if ($type == '' || $tgsno == '') return;
		if ($type == '*') $type = '[a-z_]*';
		if ($tgsno == '*') $tgsno = '[0-9]*';

		$dh  = opendir($this->dir);
		while (false !== ($filename = readdir($dh))) {
			if ( preg_match('/^'.$type.'(_'.$tgsno.')?(_)[a-z0-9A-Z]{32}[0-9_]*\.htm/',$filename) ) {
				@unlink( $this->dir.$filename );
			}
		}
		closedir($dh);

	}

	function truncate() {
		$class = &todayshop_cache::getInstance();
		if(is_object($class)) $class->_truncate();
	}

	function _truncate() {

		if ($this->use !== true) return;

		$dh  = opendir($this->dir);
		while (false !== ($filename = readdir($dh))) {

			if ( preg_match('/^[a-z]+(_(-)?[0-9]*)?(_)[a-z0-9A-Z]{32}[0-9_]*\.htm/',$filename) ) {
				@unlink( $this->dir.$filename );
			}

		}
		closedir($dh);
	}

	function getCache($_path) {

		if ($this->use !== true) return false;

		$contents = '';

		if ($fh = @fopen($_path, 'r')) {

			flock($fh, LOCK_SH);
			if (filesize($_path) > 0) $contents = fread($fh, filesize($_path));
			flock($fh, LOCK_UN);
			fclose($fh);

		}

		return ($contents != '') ? $contents : false;
	}

	function setCache(& $_html) {

		if ($this->use !== true) exit($_html);

		if ($this->regen === true) {

			if (strlen($_html) > 0) {

				if ($fh = @fopen($this->path, 'w')) {

					flock($fh, LOCK_EX);
					fwrite($fh, $_html);
					flock($fh, LOCK_UN);
					fclose($fh); @chmod($this->path, 0777);
				}
			}
			else {
				@unlink($this->path);
			}
		}

		echo $_html;
	}

	function _microtime() {	// (from php.net)

		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

}
?>
