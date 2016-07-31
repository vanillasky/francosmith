<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Development
 */

/**
 * GODO_Development_syntax
 *
 * command line 에서 실행
 *
 * 사용 예제 :
 *
 * shell>php ./syntax.php 디렉토리
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Development
 */
class GODO_Development_syntax {

    /**
     * syntax 를 체크할 디렉토리
     * @var string
     */
	private $root_dir = './';

    /**
     * syntax 체크 결과
     * @var array
     */
	private $log = array();

    /**
     * syntax 체크시 제외할 확장자
     * @var array
     */
	private $exclude = array('.','..','.svn');


    /**
     * syntax 를 체크할 디렉토리를 설정
     * @param string $path
     * @return void
     */
	public function setRootDir($path) {

		$this->root_dir = $path;
	}


    /**
     * _scan 메서드 실행
     * @param string $dir
     * @return void
     */
	public function scan($dir='') {
		if ($dir == '') $dir = $this->root_dir;
		$this->_scan($dir);
	}


    /**
     * 인자로 넘어온 디렉토리 또는, 설정된 디렉토리내의 php 파일의 syntax 체크
     * @param string $path
     * @return void
     */
	private function _scan($path='') {

		if (is_dir($path)) {

			if (!preg_match('/\/$/',$path)) $path .= '/';

			$fl = scandir($path);
		}
		else if (is_file($path)) {
			$fl = array($path);
		}
		else {
			$fl = array();
		}

		foreach ($fl as $file) {

			if (in_array($file, $this->exclude)) continue;

			$_file = $path . $file;

			if (is_dir($_file)) {
				$this->_scan($_file);
			}
			else {
				$this->check($_file);
			}
		}
	}

    /**
     * PHP 파일인지 체크
     * @param string $path
     * @return boolean
     */
	private function isPHP($path) {
		$tmp = explode('.',$path);
		$ext = array_pop($tmp);

		return in_array($ext, array(
			'php','html','inc','php3'
		));
	}

    /**
     * php 파일의 syntax 를 체크하여 결과를 화면에 출력
     * @param $path
     * @return void
     */
	private function check($path) {

		if ($this->isPHP($path)) {

			$script = sprintf('php -l %s', $path);

			exec($script, $error, $code);

			if ($code !== 0) {
				echo $path.PHP_EOL;
			}

		}

	}

}



if (isset($argv[1]) && $argv[1] !== '')
	$root = $argv[1];
else
	exit('missing argument.');

set_time_limit(0);

$sc = new GODO_Development_syntax;
$sc->setRootDir($root);
$sc->scan();

?>