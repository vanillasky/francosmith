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
 * GODO_helper_performance
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_performance extends GODO_helper {

	/**
	 * 성능 로그
	 * @var array
	 */
	private $log = array();

	/**
	 * 성능 측정 시작 시간 (마이크로 타임)
	 * @var float
	 */
	private static $start;

	/**
	 * 메모리 사용량
	 * @var integer
	 */
	private static $memory;

	/**
	 * 측정 시작
	 * @return void
	 */
	public function start() {
		$this->start = $this->_time();
		$this->memory = memory_get_usage();
	}

	/**
	 * 성능 로그 를 리턴 (html)
	 * @return string
	 */
	public function __toString() {

		$_total['lap'] = 0;
		$_total['mem'] = 0;

		foreach ($this->log as $log) {
			$_total['lap'] += $log['lap'];
			$_total['mem'] += $log['mem'];
		}

		ob_start();
		print_r($_total);
		print_r($this->log);

		return ob_get_clean();

	}

	/**
	 * 마이크로 타임을 리턴
	 * @return float
	 */
	private function _time() {

		list($ms, $s) = explode(' ', microtime());
		return (float)$ms + (float)$s;

	}

	/**
	 * 실행 시간을 리턴
	 * @return float 측정 시간
	 */
	private function _lap() {

		static $from = null;

		if ($from === null) $from = $this->start;

		$to = $this->_time();

		$lap = $to - $from;

		$from = $to;

		return $lap;

	}

	/**
	 * 메모리 사용량을 리턴
	 * @return integer
	 */
	private function _mem() {

		static $from = null;

		if ($from === null) $from = $this->memory;

		$to = memory_get_usage();

		$mem = $to - $from;

		$from = $to;

		return $mem;

	}

	/**
	 * 호출 시점의, 성능 (메모리, 시간) 을 리턴
	 * @param string $scope [optional]
	 * @return array
	 */
	public function checkpoint($scope='') {

		$_log = array(
			'lap' => $this->_lap(),
			'mem' => $this->_mem()
		);

		if ($scope) {
			$this->log[$scope][] = $_log;
		}
		else {
			$this->log[] = $_log;
		}

	}

}
?>
