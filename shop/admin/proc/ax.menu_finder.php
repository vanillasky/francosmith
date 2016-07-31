<?
/**
 * 관리자 메뉴 검색 클래스
 *
 * PHP versions 5
 *
 * @author extacy @ godosoft development team. <extacy@godo.co.kr>
 * @package eNamoo
 */

/**
 * 관리자 메뉴 검색 클래스
 * @author extacy @ godosoft development team. <extacy@godo.co.kr>
 * @package eNamoo
 */

class adminMenuFinder {

	/**
	 * 관리자 전체 메뉴 정보
	 * @var array
	 */
	private $menus;

	/**
	 * 캐시 저장 경로
	 * @var string
	 */
	private $cache;

	/**
	 * Construct. 관리자 전체 메뉴 정보를 설정한다
	 * @return void
	 */
	public function __construct() {
		$this->cache = dirname(__FILE__).'/../../cache/admin.menu.cache.php';
		$this->menus = $this->getMenus();
	}


	/**
	 * 특문을 공백으로 치환후, 공백울 경계로 나뉜 배열을 반환
	 * @param string $words
	 * @return array
	 */
	private function getRawWords($words) {

		$words = strip_tags($words);

		$_replaceFr = array(
			'\n','\r\n','-','+','_','~','!','@','#','$','%','^','&','*','(',')','{','}','[',']',';','\'',':','"',',','.','<','>','/','?','`',
		);

		$_replaceTo = ' ';

		$words = str_replace($_replaceFr,$_replaceTo,$words);
		$words = explode($_replaceTo,$words);
		$words = array_unique($words);

		return $words;

	}

	/**
	 * 문자열을 배열로 반환 (한글, 영문, 숫자 각 1자씩)
	 * @param string $keyword
	 * @return array
	 */
	private function getChars($keyword) {

		$result = array();

		for ($i=0,$m=strlen($keyword);$i<$m;$i++) {

			$char = $keyword[$i];

			if (ord($char) >= 127) {
				$i++;
				$char = $char . $keyword[$i];
			}

			$result[] = $char;
		}

		return $result;

	}

	/**
	 * 관리자 전체 메뉴 정보를 가져온다
	 * @return array
	 */
	private function getMenus() {

		if (($menus = $this->getMenuFromCache()) === false) {
			$menus = $this->getMenuFromINI();
			// 캐시에 저장
			$this->saveMenusCache($menus);
		}

		// 현재 사용중이지 않은 모바일 샵이 아닌 메뉴들은 삭제한다
		if ($this->getMobileShopVersion() == 2) {
			// v2
			$_mobile_shop_remove_regexp = '/^mobileShop\//';
		}
		else {
			// v1
			$_mobile_shop_remove_regexp = '/^mobileShop2\//';
		}

		for($i=0,$m=sizeof($menus);$i<$m;$i++) {
			$_url = $menus[$i]['url'];
			if (preg_match($_mobile_shop_remove_regexp,$_url)) {
				unset($menus[$i]);
			}
		}

		return $menus;

	}

	/**
	 * 캐시(파일)에 저장된 메뉴 정보를 가져온다
	 * @return array or false
	 */
	private function getMenuFromCache() {

		$result = array();

		if ((int)@filectime($this->cache) + 86400 > time()) {

			$fc = file_get_contents($this->cache);
			$result = unserialize($fc);
		}

		return (is_array($result) && sizeof($result) > 0)
			? $result
			: false;

	}


	/**
	 * 메뉴 정보를 캐시(파일)에 저장한다
	 * @param array $menus
	 * @return void
	 */
	private function saveMenusCache($menus) {

		if ($fh = @fopen($this->cache, 'w')) {

			$contents = serialize($menus);

			flock($fh, LOCK_EX);
			fwrite($fh, $contents);
			flock($fh, LOCK_UN);
			fclose($fh);
			@chmod($this->cache, 0707);

		}

	}

	/**
	 * 각 디렉토리의 _menu.ini 파일에서 메뉴 정보를 가져온다
	 * @return array
	 */
	private function getMenuFromINI() {

		@include(dirname(__FILE__).'/../../lib/library.php');
		@include(dirname(__FILE__).'/../../conf/menu.unable.php');

		$result = array();

		$path = dirname(__FILE__).'/../';	// admin 경로
		$dirs = scandir($path);

		foreach($dirs as $dir) {

			if ($dir == '.' || $dir == '..' || $dir == '.svn') continue;

			$_path = $path.$dir;

			if (is_dir($_path) && is_file($_path.'/_menu.ini')) {

				$ini = @file($_path.'/_menu.ini');

				foreach($ini as $menu) {
					$menu = trim($menu);
					if (!empty($menu) && !preg_match('/^\[.+\]$/',$menu)) {

						$tmp	= explode('= ', $menu);
						$url	= str_replace('"','', trim($tmp[1]));
						$title	= trim($tmp[0]);
						$target = trim($tmp[2]);

						if (preg_match('/^\.\.\//',$url))
							$url = preg_replace('/^\.\.\//','',$url);
						else
							$url = $dir.'/'.$url;

						// 사용 불가 메뉴 제외
						if (in_array($url, $menu_unable)) continue;

						$result[] = array(
							'title' => $title,
							'url' => $url,
							'target' => $target,
						);
					}
				}
			}
		}

		return $result;

	}

	/**
	 * 한글,영문,숫자 순 정렬을 위해 문자열 변환
	 * @param string $keyword
	 * @return string
	 */
	private function toSortString($keyword) {

		// 한->영->숫자 순으로 정렬

		$tmp = $this->getChars($keyword);

		$_str = '';
		// 한, 소, 대, 숫, 특
		foreach ($tmp as $char) {

			$code = ord($char);

			if ($code > 127) {
				$mod = 0;
			}
			elseif ($code == 32) {	// 공백
				$mod = 0;
			}
			elseif ($code >= 97 && $code <= 122)	{ // 소문자
				$mod = 256;
			}
			elseif ($code >= 65 && $code <= 90)	{ // 대문자
				$mod = 512;
			}
			elseif ($code >= 48 && $code <= 57)	{ // 숫자
				$mod = 768;
			}
			else {
				$mod = 1024;
			}

			$code = $code + $mod;

			$_str .= base_convert($code, 10, 36);
		}

		return $_str;

	}


	/**
	 * 현재 사용(설정) 중인 모바일샵 버전을 리턴
	 * @return integer mobile shop version
	 */
	private function getMobileShopVersion() {

		$_ms_htaccess = dirname(__FILE__).'/../../../m/.htaccess';

		if ( is_file($_ms_htaccess) ) {

			$aFileContent = file($_ms_htaccess);

			for ($i=0,$m=sizeof($aFileContent); $i<$m; $i++) {
				if (preg_match("/RewriteRule/i", $aFileContent[$i])) {
					break;
				}
			}

			return $i < $m ? 2 : 1;

		} else {
			return 1;
		}

	}


	/**
	 * 관리자 메뉴를 검색
	 * @param string $keyword
	 * @return array
	 */
	public function find($keyword) {

		$keyword = strtolower($keyword);

		$keywords = $this->getRawWords($keyword);

		$found = array();
		$exact = array();

		foreach($this->menus as $menu) {

			$_match = true;
			$_title = strtolower($menu['title']);

			foreach ($keywords as $_keyword) {
				if (strpos($_title,$_keyword) === false) $_match = false;
			}

			if ($_match) {

				$_tmp = array(
					'title' => $menu['title'],
					'url' => '../'.$menu['url'],
					//'target' => $target,
				);

				if (!in_array($_tmp, $found)) {

					if ($menu['title'] == $keyword) {
						$exact[] = $_tmp;
					}
					else {
						$sort[] = $this->toSortString($menu['title']);
						$found[] = $_tmp;
					}
				}
			}

		}

		// 정렬
		if (sizeof($found) > 0) {
			array_multisort($sort, SORT_ASC, $found);
		}

		// 검색어 일치 메뉴를 맨 위로
		if (sizeof($exact) > 0) {
			array_unshift($found, $exact);
		}

		return $found;

	}

	/**
	 * 배열을 json 문자열로 변환
	 * @param array $array [optional]
	 * @return string
	 */
	public function toJSON($array=false) {

		if (is_null($array)) return 'null';
		if ($array === false) return 'false';
		if ($array === true) return 'true';
		if (is_scalar($array)) {

			if (is_float($array)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($array)));
			}

			if (is_string($array)) {

				$array = preg_replace('{(</)(script)}i', "$1'+'$2", $array);
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $array) . '"';
			}

			else return $array;
		}

		$isList = true;
		for ($i = 0, reset($array); $i < count($array); $i++, next($array)) {
			if (key($array) !== $i) {
				$isList = false;
				break;
			}
		}

		$result = array();
		if ($isList) {
			foreach ($array as $v) $result[] = $this->toJSON($v);
			return '[ ' . join(", \n", $result) . ' ]';
		}
		else
		{
			foreach ($array as $k => $v) $result[] = $this->toJSON($k).': '.$this->toJSON($v);
			return '{ ' . join(", \n", $result) . ' }';
		}
	}

}

header('Content-Type: text/html; charset=euc-kr');
$keyword = isset($_POST['keyword']) ? iconv('UTF-8','EUC-KR',trim($_POST['keyword'])) : '';

$finder = new adminMenuFinder;
echo $finder->toJSON( $finder->find($keyword) );
?>
