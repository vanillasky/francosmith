<?
/**
 * Robot 라이브러리
 */
class Lib_Robot
{
	/**
	 * 특정 로봇 접근여부
	 *
	 * @author pr
	 * @return bool
	 *
	 */
	function isRobotAccess()
	{
		$isRobot = false;
		$needle = array(
			'libwww-perl',
			'curl',
			'wget',
			'python',
			'Googlebot',
			'Yahoo! Slurp',
			'msnbot',
			'bingbot',
			'Yeti',
			'YandexBot',
			'Baiduspider',
			'NaverBot',
			'Daumoa',
			'MJ12bot',
		);
		foreach ($needle as $what) {
			if (($pos = strpos($_SERVER['HTTP_USER_AGENT'], $what)) !== false) {
				$isRobot = true;
				break;
			}
		}
		return $isRobot;
	}
}
?>