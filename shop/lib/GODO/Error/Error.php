<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Error Handler
 */

/**
 * Error
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Error Handler
 */
final class Error {

	/**
	 * 설정된 에러 정보
	 * @var array
	 */
	private $errorCode = array();

	/**
	 * 에러 정보를 설정
	 * @return void
	 */
	public function __construct() {

		/**
		 * @see http://www.php.net/manual/en/errorfunc.constants.php
		 */
		$this->errorCode = array(
			E_ERROR				=> 'Error',				// 1
			E_WARNING			=> 'Warning',			// 2
			E_PARSE				=> 'Parse',				// 4
			E_NOTICE			=> 'Notice',			// 8
			E_CORE_ERROR		=> 'Core Error',		// 16
			E_CORE_WARNING		=> 'Core Warning',		// 32
			E_COMPILE_ERROR		=> 'Compile Error',		// 64
			E_COMPILE_WARNING	=> 'Compile Warning',	// 128
		);

		// 2048
		if (version_compare(PHP_VERSION, '5.0.0' ,'>='))
			$this->errorCode[E_STRICT] = 'Strict';

		// 4096
		if (version_compare(PHP_VERSION, '5.2.0' ,'>='))
			$this->errorCode[E_RECOVERABLE_ERROR] = 'Recoverable Error';

		// 8192
		if (version_compare(PHP_VERSION, '5.3.0' ,'>='))
			$this->errorCode[E_DEPRECATED] = 'Deprecated';

		// 핸들링할 에러만 남기고 삭제
		$er = Core::config('global', 'report_error_level');
		$ks = array_keys($this->errorCode);

		for ($i=0,$m=sizeof($ks);$i<$m;$i++) {
			if (!($er & $ks[$i])) {
				unset($this->errorCode[$ks[$i]]);
			}
		}

	}

	/**
	 * 에러 핸들러 (errorCode 에 지정된 에러만 출력)
	 *
	 * @param integer $errno	에러 코드
	 * @param string  $errstr	에러 메시지
	 * @param string  $errfile	에러가 발생할 파일
	 * @param integer $errline	에러가 발생한 파일의 라인
	 * @return true
	 */
	function handler($errno, $errstr, $errfile='', $errline=0) {

		if (isset($this->errorCode[$errno])) {

			$_param = array(
				$this->errorCode[$errno],
				$errstr,
				$errfile,
				$errline
			);

			$msg = vsprintf('<strong>%s</strong> (%s) @ %s / %d line', $_param);

			echo '<div style="display:block;width:100%;font-family:Bitstream Vera Sans Mono,Courier New,Tahoma; font-size:9pt;background:#F7F7F9;color:#202020;padding:10px;margin:10px;border:1px dotted red;">';
			echo $msg;
			echo '</div>';

		}

		return true;

	}

}
?>