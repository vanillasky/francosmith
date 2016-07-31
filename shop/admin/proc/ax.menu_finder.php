<?
/**
 * ������ �޴� �˻� Ŭ����
 *
 * PHP versions 5
 *
 * @author extacy @ godosoft development team. <extacy@godo.co.kr>
 * @package eNamoo
 */

/**
 * ������ �޴� �˻� Ŭ����
 * @author extacy @ godosoft development team. <extacy@godo.co.kr>
 * @package eNamoo
 */

class adminMenuFinder {

	/**
	 * ������ ��ü �޴� ����
	 * @var array
	 */
	private $menus;

	/**
	 * ĳ�� ���� ���
	 * @var string
	 */
	private $cache;

	/**
	 * Construct. ������ ��ü �޴� ������ �����Ѵ�
	 * @return void
	 */
	public function __construct() {
		$this->cache = dirname(__FILE__).'/../../cache/admin.menu.cache.php';
		$this->menus = $this->getMenus();
	}


	/**
	 * Ư���� �������� ġȯ��, ����� ���� ���� �迭�� ��ȯ
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
	 * ���ڿ��� �迭�� ��ȯ (�ѱ�, ����, ���� �� 1�ھ�)
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
	 * ������ ��ü �޴� ������ �����´�
	 * @return array
	 */
	private function getMenus() {

		if (($menus = $this->getMenuFromCache()) === false) {
			$menus = $this->getMenuFromINI();
			// ĳ�ÿ� ����
			$this->saveMenusCache($menus);
		}

		// ���� ��������� ���� ����� ���� �ƴ� �޴����� �����Ѵ�
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
	 * ĳ��(����)�� ����� �޴� ������ �����´�
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
	 * �޴� ������ ĳ��(����)�� �����Ѵ�
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
	 * �� ���丮�� _menu.ini ���Ͽ��� �޴� ������ �����´�
	 * @return array
	 */
	private function getMenuFromINI() {

		@include(dirname(__FILE__).'/../../lib/library.php');
		@include(dirname(__FILE__).'/../../conf/menu.unable.php');

		$result = array();

		$path = dirname(__FILE__).'/../';	// admin ���
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

						// ��� �Ұ� �޴� ����
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
	 * �ѱ�,����,���� �� ������ ���� ���ڿ� ��ȯ
	 * @param string $keyword
	 * @return string
	 */
	private function toSortString($keyword) {

		// ��->��->���� ������ ����

		$tmp = $this->getChars($keyword);

		$_str = '';
		// ��, ��, ��, ��, Ư
		foreach ($tmp as $char) {

			$code = ord($char);

			if ($code > 127) {
				$mod = 0;
			}
			elseif ($code == 32) {	// ����
				$mod = 0;
			}
			elseif ($code >= 97 && $code <= 122)	{ // �ҹ���
				$mod = 256;
			}
			elseif ($code >= 65 && $code <= 90)	{ // �빮��
				$mod = 512;
			}
			elseif ($code >= 48 && $code <= 57)	{ // ����
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
	 * ���� ���(����) ���� ����ϼ� ������ ����
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
	 * ������ �޴��� �˻�
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

		// ����
		if (sizeof($found) > 0) {
			array_multisort($sort, SORT_ASC, $found);
		}

		// �˻��� ��ġ �޴��� �� ����
		if (sizeof($exact) > 0) {
			array_unshift($found, $exact);
		}

		return $found;

	}

	/**
	 * �迭�� json ���ڿ��� ��ȯ
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
