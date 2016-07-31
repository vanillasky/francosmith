<?php
/**
 * Clib_Memcache
 * memcache 핸들러
 * @author extacy @ godosoft development team.
 */
class Clib_Memcache
{
	/**
	 * Memcache 서버에 연결 여부
	 * @var boolean
	 */
	private $_isConnected = false;

	/**
	 * 상점 ID
	 * @var integer
	 */
	private $_mallID;

	/**
	 * Memcache 인스턴스
	 * @var Memcache|null
	 */
	private $_memcache;

	/**
	 * Memcache 서버 목록
	 * @var array
	 */
	private $_memcacheServerInformation = array();

	/**
	 * Construct
	 * Memcache 인스턴스를 생성하고, Memcache 서버에 연결
	 * @param string $server [optional]
	 * @return void
	 */
	public function __construct($server = 'localhost:11211')
	{
		return false;
		// @todo : 설정에 따라 적용되도록 수정해야 함.
		if (class_exists('Memcache', false)) {
			$this->_memcache = new Memcache;
			$this->_connect($server);
			$this->_setMallID();
		}
		else {
			$this->_memcache = null;
		}
	}

	/**
	 * 상점 ID를 _mallID 프로퍼티에 설정
	 * @return void
	 */
	private function _setMallID()
	{
		$godo = CLib_Application::getConfig('godo');
		$this->_mallID = $godo['sno'];
	}

	/**
	 * 입력 값과 상점 ID를 이용한 키를 생성하여 리턴
	 * @param mixed $key
	 * @return string md5 키
	 */
	private function _getKey($key)
	{
		if (is_array($key) || is_object($key)) {
			$key = serialize($key);
		}

		$key = sprintf('%s_%s', $this->_mallID, $key);
		return md5($_key);
	}

	/**
	 * Memcache 서버에 연결하고, 결과를 _isConnected 프로퍼티에 설정
	 * @param string $server
	 * @return void
	 */
	private function _connect($server)
	{
		// memcache 서버는 localhost 에서만 사용한다.
		// host:port
		list($host, $port) = explode(':', $server);

		$this->_memcache->addServer($host, $port);

		if ($this->_memcache->getServerStatus($host, $port) > 0) {
			$this->_isConnected = true;
		}
		else {
			$this->_isConnected = false;
		}
	}

	/**
	 * Add an item to the server
	 * @param string $key The key that will be associated with the item
	 * @param mixed $var The variable to store. Strings and integers are stored as is, other types are stored serialized.
	 * @param integer $flag [optional] Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib).
	 * @param integer $expire [optional] Expiration time of the item.
	 * @return boolean
	 */
	public function add($key, $var, $flag = 0, $expire = 30)
	{
		$key = $this->_getKey($key);

		return $this->_isConnected ? $this->_memcache->add($key, $var, $flag, $expire) : false;
	}

	/**
	 * Retrieve item from the server
	 * @param string $key The key to fetch
	 * @return mixed|false the string associated with the key or FALSE on failure
	 */
	public function get($key)
	{
		$key = $this->_getKey($key);

		return $this->_isConnected ? $this->_memcache->get($key) : false;
	}

	/**
	 * Store data at the server
	 * @param string $key The key that will be associated with the item
	 * @param mixed $var The variable to store. Strings and integers are stored as is, other types are stored serialized
	 * @param integer $flag [optional] Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib)
	 * @param integer $expire [optional] Expiration time of the item
	 * @return boolean
	 */
	public function set($key, $var, $flag = 0, $expire = 30)
	{
		$key = $this->_getKey($key);

		// $flag = MEMCACHE_COMPRESSED;
		return $this->_isConnected ? $this->_memcache->set($key, $var, $flag = 0, $expire = 30) : false;
	}

	/**
	 * Delete item from the server
	 * @param string $key The key associated with the item to delete
	 * @param integer $timeout [optional] Execution time of the item
	 * @return boolean
	 */
	public function del($key, $timeout = 0)
	{
		$key = $this->_getKey($key);

		return $this->_isConnected ? $this->_memcache->delete($key, $timeout) : false;
	}

	/**
	 * Replace value of the existing item
	 * @param string $key The key that will be associated with the item
	 * @param mixed $var The variable to store. Strings and integers are stored as is, other types are stored serialized
	 * @param integer $flag [optional] Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib)
	 * @param object $expire [optional] Expiration time of the item
	 * @return boolean
	 */
	public function replace($key, $var, $flag = 0, $expire = 30)
	{
		$key = $this->_getKey($key);

		return $this->_isConnected ? $this->_memcache->replace($key, $var, $flag, $expire) : false;
	}

	/**
	 * Decrement item's value
	 * @param string $key Key of the item do decrement
	 * @param integer $value
	 * @return integer|false item's new value on success or FALSE on failure
	 */
	public function decrement($key, $value = 1)
	{
		$key = $this->_getKey($key);

		return $this->_isConnected ? $this->_memcache->decrement($key, $value) : false;
	}

	/**
	 * Increment item's value
	 * @param string $key Key of the item to increment
	 * @param integer $value Increment the item by value
	 * @return new item's value on success or FALSE on failure
	 */
	public function increment($key, $value = 1)
	{
		$key = $this->_getKey($key);

		return $this->_isConnected ? $this->_memcache->increment($key, $value) : false;
	}

	/**
	 * Flush all existing items at the server
	 * @return boolean
	 */
	public function flush()
	{
		return $this->_isConnected ? $this->_memcache->flush() : false;
	}

}
