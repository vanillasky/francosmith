<?php /**
 * Clib_Session
 * Application 내부에서 SESSION 에 접근
 * @author extacy @ godosoft development team.
 */
class Clib_Session
{
	/**
	 * SESSION 설정된 값을 리턴
	 * @param string $name 값을 가져올 키
	 * @param mixed $default [optional] 설정되지 않은 경우 리턴할 기본값
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		$var = $default;

		switch (true) {
			case isset($_SESSION[$name]) :
				$var = $_SESSION[$name];
				break;
		}

		return $var;
	}

	/**
	 * 세션에 저장된 회원 정보를 리턴
	 * @return array
	 */
	private function _getMember()
	{
		return $this->get('sess');
	}

	/**
	 * 로그인된 회원 ID 리턴
	 * @return string
	 */
	public function getMemberId()
	{
		$sess = $this->_getMember();
		return (string)$sess['m_id'];
	}

	/**
	 * 로그인된 회원 번호 리턴
	 * @return integer
	 */
	public function getMemberNo()
	{
		$sess = $this->_getMember();
		return (int)$sess['m_no'];
	}

	/**
	 * 로그인된 회원 레벨 리턴
	 * @return integer
	 */
	public function getMemberLevel()
	{
		$sess = $this->_getMember();
		return (int)$sess['level'];
	}

	public function isAdmin()
	{
		return $this->getMemberLevel() >= 80;
	}

	public function isAdult()
	{
		$sess = $this->_getMember();

		if ($sess['adult'] || $this->get('adult')) {
			return true;
		}

		return false;
	}

	public function canAccessAdult()
	{
		return $this->isAdmin() || $this->isAdult();
	}

	public function isLogged()
	{
		return $this->getMemberNo() > 0 ? true : false;
	}

}
