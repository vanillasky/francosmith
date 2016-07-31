<?php
/**
 * Clib_Storage
 * GLOBAL 처리해야하는 값이나, 타 모델에 전달하기 위한 데이터를 핸들링
 * @author extacy @ godosoft development team.
 */
class Clib_Storage
{
	/**
	 * 저장될 데이터
	 * @var array
	 */
	private static $_storage = array();

	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * 특정 키에 저장된 값을 리턴
	 * @param string $key 가져올 값의 키
	 * @return mixed 키에 저장된 값
	 */
	public function get($key = null)
	{
		if (is_null($key)) {
			return $this->_storage;
		}
		else {
			return $this->_storage[$key];
		}

	}

	/**
	 * 특정 키에 값을 설정
	 * @param string $key 저장할 값의 키
	 * @param mixed $value 키에 저장할 값
	 * @return boolean 설정 성공시 true, 이미 설정된 경우 false
	 */
	public function set($key, $value)
	{
		if ($this->has($key)) {
			return false;
		}
		else {
			$this->_storage[$key] = $value;
			return true;
		}
	}

	/**
	 * 특정 키가 설정되어 있는지 여부를 리턴
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return isset($this->_storage[$key]);
	}

	/**
	 * 특정 키를 삭제
	 * @param string $key
	 * @return void
	 */
	public function del($key)
	{
		unset($this->_storage[$key]);
	}

	/**
	 * 설정된 값을 모두 GLOBAL 처리함
	 * @return void
	 */
	public function toGlobal()
	{
		if (is_array($this->_storage) && ! empty($this->_storage)) {
			foreach ($this->_storage as $key => $value) {
				$GLOBALS[$key] = $value;
			}
		}
	}

}
