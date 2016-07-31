<?php
/**
 * Clib_Object
 * Clib 이하에 만들어지는 모든 클래스의 최상위 클래스
 * 클래스라이브러리 오브젝트가 기본적으로 가져야 하는 함수를 정의한다.
 * @author khs @ godosoft development team.
 * @author extacy @ godosoft development team.
 */
class Clib_Object implements ArrayAccess, IteratorAggregate, Countable
{
	/**
	 * 담아둘 데이터
	 * @var array
	 */
	private $_data = array();

	/**
	 * 미 정의된 property
	 * @var array
	 */
	private $_store = array();

	/**
	 * 담아둘 데이터의 원본.
	 * 업데이트 혹은 처리 로직 중간에 데이터가 변경되었는지 확인하기 위해 사용한다
	 * @var array
	 */
	private $_originalData = array();

	/**
	 * DB 등의 Resource 와 동기화(load or save) 여부
	 * @var boolean
	 */
	private $_hasLoaded = false;

	/**
	 * DB 등의 Resource 와 동기화 이후, 데이터의 변형이 있었는지 여부
	 * @var boolean
	 */
	private $_hasChanged = false;

	/**
	 * 클래스를 시리얼라이즈 시킨다.
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this);
	}

	/**
	 * 클래스이름을 리턴한다.
	 * @return string 클래스 이름
	 */
	public function getClassName()
	{
		return get_class($this);
	}

	/**
	 * 환경 설정 파일을 불러들인다
	 * @param string $filename
	 * @return void
	 */
	public function includeConfig($filename)
	{
		include "../../conf/" . $filename;
	}

	/**
	 * 복제(clone) 동작 메서드
	 * @return object
	 */
	public function __clone()
	{
		return $this;
	}

	/**
	 * 모델의 데이터에 접근을 위한 메서드
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	public function __call($name, $value)
	{
		/*
		 * 아래와 같이 활용 가능
		 * 컬럼명은 db 컬럼명이며 네이밍룰에 따라 대소문자를 구분하여 사용한다
		 * 단, 컬럼명에 따라 추가 처리가 필요한 경우, 해당 모델에서 메서드 override 하여 사용한다
		 *
		 * $model->get컬럼명()   : 컬럼명에 해당하는 값 or 오버라이드시 연산된 값을 리턴
		 * $model->set컬럼명(값) : 컬럼명에 값을 설정
		 * $model->has컬럼명()   : 컬럼명에 해당 되는 값이 설정되어 있는지 체크
		 * $model->del컬럼명()   : 컬럼명을 삭제
		 */
		if (preg_match('/^(get|set|has|del)([a-zA-Z_0-9]+)$/', $name, $matches)) {

			$operate = $matches[1];
			$columnName = $this->_camelCaseToLowerCaseWithUnderscore($matches[2]);

			switch ($operate) {
				case 'get' :
					return $this[$columnName];
					break;
				case 'set' :
					$value = $value[0];
					$this[$columnName] = $value;
					break;
				case 'has' :
					return isset($this[$columnName]);
					break;
				case 'del' :
					unset($this[$columnName]);
					break;
			}

			return $this;
		}
		else {
			// 그 외.
			printf('Undefined Method "%s" in %s', $name, get_class($this));
		}

		return false;
	}

	public function __isset($name)
	{
		return isset($this->_store[$name]);
	}

	/**
	 * 정의되지 않은 Property 에 설정된 값을 리턴
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->$name)) {
			return $this->_store[$name];
		}
		else {
			// trigger_error or return null
			return null;
		}
	}

	/**
	 * 정의되지 않은 Property 에 값을 설정
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Object
	 */
	public function __set($name, $value)
	{
		$this->_store[$name] = $value;
		return $this;
	}

	/**
	 * 정의되지 않은 Property 를 삭제
	 * @param string $name
	 * @return void
	 */
	public function __unset($name)
	{
		unset($this->_store[$name]);
	}

	/**
	 * CamelCase 를 camel_case 형태로 변환하여 리턴
	 * @param string $name
	 * @return string
	 */
	private function _camelCaseToLowerCaseWithUnderscore($name)
	{
		$rename = '';

		for ($i = 0, $m = strlen($name); $i < $m; $i++) {

			$char = strtolower($name[$i]);
			$rename .= ($char != '_' && $i > 0 && $char != $name[$i]) ? '_' . $char : $char;
		}

		return $rename;

	}

	/**
	 * 데이터 로드 여부를 리턴
	 * @return boolean
	 */
	public function hasLoaded()
	{
		return $this->_hasLoaded;
	}

	/**
	 * 데이터 변경 여부를 리턴
	 * @return boolean
	 */
	public function hasChanged()
	{
		return $this->_hasChanged;
	}

	/**
	 * 데이터 변경 여부를 설정
	 * @param boolean $bool
	 * @return Clib_Object
	 */
	public function setChanged($bool)
	{
		$this->_hasChanged = (bool)$bool;
		return $this;
	}

	/**
	 * 데이터 로드 여부를 설정
	 * @param boolean $bool
	 * @return Clib_Object
	 */
	public function setLoaded($bool)
	{
		$this->_hasLoaded = (bool)$bool;
		return $this;
	}

	/**
	 * 원본 데이터를 설정
	 * @param string $key
	 * @param mixed $data
	 * @return Clib_Object
	 */
	public function setOriginalData($key = null, $value = null)
	{
		if (is_null($key)) {
			$this->_originalData = $this->_data;
		}
		else {
			$this->_originalData[$key] = $value;
		}

		return $this;
	}

	/**
	 * 데이터 초기화
	 * @return Clib_Object
	 */
	public function resetData()
	{
		$this->setData(array());
		$this->setOriginalData();

		$this->setChanged(false);
		$this->setLoaded(false);

		return $this;
	}

	/**
	 * 모델의 데이터를 설정
	 * @param string|array|object $key
	 * @param mixed $value
	 * @return Clib_Object
	 */
	public function setData($key, $value = null)
	{
		if (is_array($key)) {
			$this->_data = $key;
		}
		else if ($key instanceof self) {
			$this->_data = $key->toArray();
		}
		else if ( ! is_null($key)) {
			$this->_data[$key] = $value;
			// or $this[$key] = $value;
		}
		else {
			// nothing to do.
		}

		$this->setChanged(true);

		return $this;
	}

	/**
	 * 모델의 데이터를 추가
	 * @param string|array|object $key
	 * @param mixed $value
	 * @return Clib_Object
	 */
	public function addData($key, $value = null)
	{

		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->setData($k, $v);
			}
		}
		else if ($key instanceof self) {
			foreach ($key->toArray() as $k => $v) {
				$this->setData($k, $v);
			}
		}
		else if ( ! is_null($key)) {
			$this->setData($key, $value);
		}
		else {
			// nothing to do.
		}

		return $this;
	}

	/**
	 * 모델의 데이터를 리턴
	 * @return mixed 모델의 데이터
	 */
	public function getData($key = null)
	{
		if (is_null($key)) {

			$array = array();

			if ( ! empty($this->_data)) {
				foreach ($this->_data as $k => $v) {
					if ($v instanceof Clib_Model_Abstract) {
						$array[$k] = $v->getData();
					}
					else {
						$array[$k] = $v;
					}
				}
			}

			return $array;

		}
		else {
			return $this[$key];
		}

	}

	/**
	 * 입력 키가 존재 하는지 여부
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key)
	{
		return isset($this[$key]);
	}

	/**
	 * 변경된 데이터를 리턴
	 * @return array
	 */
	public function getChangedData()
	{
		$data = array();

		if ($this->hasLoaded()) {
			foreach ($this->_data as $k => $v) {
				if ((string)$v !== (string)$this->_originalData[$k]) {
					$data[$k] = $v;
				}
			}
		}
		else {
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * 현재 객체의 hash 를 얻는다
	 * @return string
	 */
	public function getHash()
	{
		return spl_object_hash($this);
	}

	/**
	 * Whether a offset exists
	 * @param string $offset An offset to check for
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Offset to retrieve
	 * @param string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}

	/**
	 * Offset to set
	 * @param string $offset The offset to assign the value to
	 * @param mixed $value The value to set
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	/**
	 * Offset to unset
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	/**
	 * data Property convert to json
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->_data);
	}

	/**
	 *
	 * @return
	 */
	public function toArray()
	{
		return (array)$this->_data;
	}

	/**
	 * Retrieve an external iterator
	 * @return
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_data);
	}

	/**
	 * Count elements of an object
	 * @return
	 */
	public function count()
	{
		return count($this->_data);
	}

}
