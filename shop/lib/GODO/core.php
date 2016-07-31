<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 */

/**
 * Core
 * @author extacy @ godosoft development team.
 * @package GODO
 */
final class Core {

	/**
	 * DIRECTORY_SEPARATOR 상수의 별칭
	 */
	const DS = DIRECTORY_SEPARATOR;

	/**
	 * Core 인스턴스
	 * @var Core
	 */
	static private $instance = null;

	/**
	 * 스크립트 실행 로그 (DB 로그 포함)
	 * @var array
	 */
	private $script_log = array();

	/**
	 *
	 * @return void
	 */
	private function __construct() {
	}

	/**
	 * Core 객체를 리턴
	 * @return Core
	 */
	static public function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new Core;
		}

		return self::$instance;
	}

	/**
	 * 오류 메시지를 출력 스크립트 종료
	 * @param string $msg 출력할 오류 메시지
	 * @param object $exit [optional] false 일 경우 멈추지 않음
	 * @return void
	 */
	static public function raiseError($msg, $exit = true) {

		global $background_exec;

		$msg = is_array($msg) ? implode(PHP_EOL, $msg) : $msg;

		echo '<div style="width:100%;font-family:Bitstream Vera Sans Mono,Courier New,Tahoma; font-size:9pt;background:#F7F7F9;color:#202020;padding:10px;margin:10px;border:1px dotted red;">';
		echo nl2br($msg);
		echo '</div>';

		if (is_object($background_exec)) {
			$background_exec->do_logging($msg);

		}

		//if ($exit) exit;

	}

	/**
	 * GODO 환경 설정 값을 리턴
	 * @param string $sector 불러올 설정값의 키
	 * @return mixed 설정 내용
	 */
	static public function config($sector) {

		global $_CFG;

		if (isset($_CFG[$sector])) {
			$_cfg = $_CFG[$sector];
		}
		else {
			$_this	= self::getInstance();
			$_conf = $_this->loader('config');
			$_cfg = $_conf->load($sector);
		}

		if (func_num_args() > 1) {
			$args = func_get_args();
			unset($args[0]);

			foreach($args as $arg) {
				if (isset($_cfg[$arg])) $_cfg = $_cfg[$arg];
			}

		}

		return $_cfg;

	}

	/**
	 * 배열로 넘어온 인자를 가지고 config 를 배열로 넘겨준다.
	 * 배열명이 없을경우 include 만한다.
	 * @param array (파일명 ,배열명)
	 * @return array
	 */
	static public function load_config()
	{
		$patharray = func_get_args();

		$path = $patharray[0];
		$filePath = SHOPROOT . '/conf/'.$path;

		if(is_file($filePath.'.php')) {
			@include($filePath.'.php');

			//불필요한 변수를 unset한다.
			unset($patharray,$path,$filePath);

			//config 파일 에 있는 값을 가져온다.
			$arrname = array_keys(get_defined_vars());
			if (count($arrname)> 1) {
				for ($i=0;$i<count($arrname);$i++) {
					$ret[$arrname[$i]] = ${$arrname[$i]};
				}
			} else {
				$ret = ${$arrname[0]};
			}

			return $ret;
		} else {
			include($filePath);
		}
	}


	/**
	 * 구 클래스와 같은 기능을 하는 클래스명으로 전환
	 * @param string $class_name 클래스명
	 * @return string 변경된 클래스명
	 */
	private static function compatibleClassName($class_name) {
		switch ($class_name) {
			case 'db':
				$class_name = 'GODO_DB';
				break;
			case 'qfile.class':
				$class_name = 'qfile';
				break;
			case 'json.class':
				$class_name = 'Services_JSON';
				break;
		}

		return $class_name;

	}

	/**
	 * 클래스 로더
	 * @param string $class_name [optional]
	 * @return object 생성된 인스턴스 or false (실패시)
	 */
	static public function loader($class_name = '') {

		static $instances = array();

		if (empty($class_name)) return false;

		$_this = self::getInstance();

		$class_name = $_this->compatibleClassName($class_name);

		if(!isset($instances[$class_name])) {

			if (!class_exists($class_name, true)) {
				$_this->raiseError($class_name.' not support.');
			}

			$args = func_get_args();
			array_shift($args);


			if (($args_size = sizeof($args)) > 0) {

				$_args = array($class_name);

				for ($i=0;$i<$args_size;$i++) {
					${'arg'.$i} = $args[$i];
					$_args[] = '$arg'.$i;
				}

				array_unshift($args,$class_name);

				$_eval_script = vsprintf('$instances[$class_name] = new %s('.implode(', ',array_pad(array(), $args_size, '%s')).');' ,$_args);

			}
			else {
				$_eval_script = sprintf('$instances[$class_name] = new %s;',$class_name);
			}

			@eval($_eval_script);

		}

		return $instances[$class_name];

	}

	/**
	 * 헬퍼 인스턴스를 생성하여 리턴
	 * @param string $helper [optional]
	 * @return mixed object or false (실패시)
	 */
	static public function helper($helper='') {

		$_this = self::getInstance();

		$arguments = func_get_args();
		$arguments[0] = sprintf('GODO_Helper_%s', strtolower($helper));

		return call_user_func_array(array($_this, 'loader'), $arguments);

	}

    /**
     * $start 와 $end 의 차이를 리턴
     * @param float $start
     * @param float $end
     * @return float
     */
	function getLapTime($start, $end) {

		static $bc_math = null;

		if ($bc_math === null)
			$bc_math = function_exists('bcadd');

		$start = explode(' ', $start);
		$end   = explode(' ', $end);

		if ($bc_math) {
			$start = bcadd($start[1], $start[0], 8);
			$end   = bcadd($end[1], $end[0], 8);

			return bcsub($end, $start, 8);
		}
		else {
			$start = (float)$start[1] + (float)$start[0];
			$end   = (float)$end[1] + (float)$end[0];

			return $end - $start;
		}

	}

	/**
	 * 로그를 쌓거나 출력함
	 * @param array $log [optional]
	 * @param string $type [optional]
	 * @return
	 */
	static public function log($log = array(), $type='php') {

		if (! G_CONST_DEVELOPER_MODE) return;

		$_this = self::getInstance();

		if (func_num_args() === 0) {

			// zlib
			$zlib = function_exists('gzcompress');

			// sql analyzer
			$sqlAnalyzer =  str_replace(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']), '', str_replace('\\','/',dirname(__FILE__))).'/sql_analyzer.php';

			// view.
			$lap = $_this->getLapTime(G_CONST_SCRIPT_START, microtime());
			$mem = memory_get_usage(true);

			echo '<hr>';

			$db_log = '';
			$db_lap = 0;

			if (isset($_this->script_log['db'])) {
				ob_start();

				echo '
				<style>
				.gdo_performance_log {border-collapse:collapse;font-family:Bitstream Vera Sans Mono, Tahoma;font-size:8pt;margin-bottom:100px;}
				.gdo_performance_log tr.bad {background-color:RGB(255,235,235);}
				.gdo_performance_log tr.good {background-color:RGB(235,255,235);}
				.gdo_performance_log td dl {margin:0;}
				.gdo_performance_log td dl dt {margin:0 0 10px 0;color:#aaa;}
				.gdo_performance_log td dl dd {margin:0;}
				</style>
				<table width="100%" border="1" bordercolor="#cccccc" class="gdo_performance_log">
				<col align="center"><col style="padding-left:5"><col style="padding-left:5"><col align=center>
				<tr bgcolor="#f7f7f7" height="30">
					<th width="40">no</th>
					<th>query</th>
					<th width="80">time</th>
					<th width="80">rows</th>
					<th width="80">-</th>
				</tr>
				';

				$_data = $_this->script_log['db'];

				// 쿼리 실행 속도 계산 및 속도순 정렬
				$_sort = array();
				$keys = array_keys($_data);
				for ($i=0, $m=sizeof($keys); $i < $m; $i++) {
					$key = $keys[$i];

					$_data[$key]['lap'] = $_this->getLapTime($_data[$key]['lap'][0],$_data[$key]['lap'][1]);
					$_sort[$key] = $_data[$key]['lap'];

				}
				//array_multisort( $_sort, SORT_DESC, $_data );

				foreach ( $_data as $k=>$v ) {

					$css = '';

					if ( $v['lap'] > 0.5 ) {
						$css = 'bad';
					}
					elseif ( $v['lap'] < 0.001 ) {
						$css = 'good';
					}

					$db_lap = (float) $db_lap + (float) $v['lap'];
					$_lap = round( $v['lap'] * 100000 ) / 100000;
					$row = number_format($v['row']);

					$sql = $v['sql'];

					$file = isset( $v['file'] ) ? $v['file'] : $_SERVER['PHP_SELF'];
					$file .= isset( $v['line'] ) ? sprintf( ' @ %d line', $v['line'] ) : '';

					$_root = str_replace( DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT'] );
					$file = str_replace( DIRECTORY_SEPARATOR, '/', $file );
					$file = str_replace( $_root, '', $file );

					$_sql = $zlib ? base64_encode(gzcompress($sql, 9)) : $sql;

					printf('<tr class="%s">
						<td>%d</td>
						<td style="padding:10px;">
						<dl>
							<dt>%s</dt>
							<dd>
							%s
							</dd>
						</dl>
						</td>
						<td>%f</td>
						<td>%d</td>
						<td align="center"><a href="%s" target="sql_analyzer">Analyze</a></td>
					</tr>', $css, ++$idx, $file, $sql, $_lap, $row, $sqlAnalyzer.'?q='.urlencode($_sql));

				}

				printf('<tr bgcolor="#f7f7f7" height="30">
					<td>total</td>
					<td></td>
					<td>%f</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				</table><br/>', $db_lap);

				$db_log = ob_get_contents();
				ob_end_clean();

			}

			printf('
			<style>
				ul.godo-page-performance-log {}
				ul.godo-page-performance-log li {font-family:Bitstream Vera Sans Mono, Tahoma;font-size:9pt;}
			</style>
			<h3>performance log</h3>
			<ul class="godo-page-performance-log">
				<li>ALL : %f Sec</li>
				<li>PHP : %f Sec</li>
				<li>SQL : %f Sec</li>
				<li>MEM : %s KB</li>
			</ul>
			', $lap, $lap - $db_lap, $db_lap, number_format($mem / 1024));
			echo $db_log;

			//$sql = Core::loader('GODO_DB')->getPerformance();	// 0 : log table, 1 : lap time.

		}
		else {

			// logging.
			$_this->script_log[$type][] = $log;
		}

	}

	/**
	 * 특정 위치에서 삽입하면 해당 부분이 어디에서 호출되었는지 출력
	 * @return
	 */
	static public function whoisCallMe() {

		$trace = debug_backtrace();

		$result = array();

		for ($i=2, $m=sizeof($trace);$i<$m;$i++) {
			$_trace = $trace[$i];

			if (isset($_trace['file']) && strpos($_trace['function'], 'call_user_func') === false) {

				/*if (strpos($_trace['file'], "eval()'d code") !== false) {
					$key = 4;
				}*/

				$result['file'] = $_trace['file'];
				$result['line'] = $_trace['line'];

				break(1);
			}

		}

		return !empty($result) ? $result : false;

	}

}
?>
