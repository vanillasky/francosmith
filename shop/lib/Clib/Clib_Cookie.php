<?php
/**
 * Clib_Cookie
 * COOKIE 핸들러
 * @author extacy @ godosoft development team.
 */
class Clib_Cookie
{
	/**
	 * 쿠키를 굽는다
	 * @param string $name
	 * @param mixed $value
	 * @param integer $expire [optional]
	 * @param string $path [optional]
	 * @param string $domain [optional]
	 * @param boolean $secure [optional]
	 * @param boolean $httponly [optional]
	 * @return
	 */
	public function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
	{
		if ( ! headers_sent()) {
			$expire = $expire > 0 ? $expire + time() : 0;
			setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		}
		else {
			// 쿠키를 구울 수 없음.
		}
	}

	/**
	 * 쿠키에 설정된 값을 리턴
	 * @param string $name 가져올 쿠키의 키
	 * @return mixed|null
	 */
	public function get($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}

	/**
	 * 쿠키를 삭제한다(쿠키를 만료처리 한다)
	 * @param string $name 삭제할 쿠키의 키
	 * @return void
	 */
	public function del($name)
	{
		$this->set($name, false, - 86400);
	}

}
