<?php
/**
 * Clib_Application
 * @author extacy @ godosoft development team.
 */
class Clib_Application
{

	/**
	 * 모바일 페이지 여부
	 * @var array
	 */
	private static $_mobile = false;

	/**
	 * 형식화된 클래스 이름을 리턴
	 * @param string $type 형식
	 * @param string $name 형식화할 이름 (ex: goods_list)
	 * @return string 형식화된 클래스 이름
	 */
	public static function getClassName($type, $name)
	{
		$tmp = explode('_', $name);

		if (sizeof($tmp) === 1) {
			$tmp = array_pad($tmp, 2, $tmp[0]);
		}

		$name = array_shift($tmp);

		if ($name != 'Clib') {
			$name = sprintf('Clib %s %s', $type, $name);
		}

		if (sizeof($tmp)) {
			$name .= ' ' . implode(' ', $tmp);
		}

		$name = str_replace(' ', '_', ucwords($name));

		return $name;
	}

	/**
	 * 클래스 인스턴스 생성
	 * @param string $name
	 * @return object
	 */
	private static function _getClass($name)
	{
		return new $name;
	}

	/**
	 * 싱글톤 인스턴스 생성
	 * @param string $name
	 * @return object
	 */
	private static function _getSingletonModelClass($name)
	{
		return Core::loader($name);
	}

	/**
	 * $name 에 해당하는 리소스 모델을 리턴
	 * @param string $name
	 * @return object
	 */
	public static function getResourceClass($name)
	{
		$name = self::getClassName('Resource', $name);

		if ( ! class_exists($name)) {
			$name = 'Clib_Resource_Common';
		}

		return self::_getSingletonModelClass($name);
	}

	/**
	 * $name 에 해당하는 모델을 리턴
	 * @param string $name
	 * @return object
	 */
	public static function getCollectionClass($name)
	{
		$name = self::getClassName('Collection', $name);
		return self::_getClass($name);
	}

	/**
	 * $name 에 해당하는 모델을 리턴
	 * @param string $name
	 * @return object
	 */
	public static function getModelClass($name)
	{
		$name = self::getClassName('Model', $name);
		return self::_getClass($name);
	}

	/**
	 * $name 에 해당하는 모델 클래스를 리턴
	 * @param string $name
	 * @return
	 */
	public static function getSingletonModelClass($name)
	{
		$name = self::getClassName('Model', $name);
		return self::_getSingletonModelClass($name);
	}

	/**
	 * 입력 arguments 에 해당하는 환경 설정값을 리턴
	 * @param multiple
	 * @return array|false 성공시에는 해당되는 설정값을, 실패시에는 false
	 */
	public static function getConfig()
	{
		$arg = func_get_args();

		try {
			$ret = call_user_func_array(array(
				'Core',
				'config'
			), $arg);
		}
		catch (Clib_Exception $e) {
			return false;
		}

		return $ret;
	}

	/**
	 * 입력 arguments 에 해당하는 환경 설정값
	 * @param multiple
	 * @return array|false 성공시에는 해당되는 설정값을, 실패시에는 false
	 */
	public static function getLoadConfig()
	{
		$arg = func_get_args();

		try {
			$ret = call_user_func_array(array(
				'Core',
				'load_config'
			), $arg);
		}
		catch (Clib_Exception $e) {
			return false;
		}

		return $ret;
	}

	/**
	 * 입력 arguments 에 해당하는 라이브러리를 인스턴스 합니다.
	 *
	 * @param multiple
	 * @return 인스턴스정보|false 성공시에는 해당되는인스턴스 정보을, 실패시에는 false
	 */
	public static function getLoadLib()
	{
		$arg = func_get_args();
		return self::_getSingletonModelClass($arg[0]);
	}

	/**
	 * 데이터베이스 인스턴스를 리턴
	 * @return GODO_DB
	 */
	public static function database()
	{
		return Core::loader('db');
	}

	/**
	 * Clib_Cookie 를 리턴
	 * @return Clib_Cookie
	 */
	public static function cookie()
	{
		return Core::loader('Clib_Cookie');
	}

	/**
	 * Clib_Storage 를 리턴
	 * @return Clib_Storage
	 */
	public static function storage()
	{
		return Core::loader('Clib_Storage');
	}

	/**
	 * Clib_Request 를 리턴
	 * @return Clib_Request
	 */
	public static function request()
	{
		return Core::loader('Clib_Request');
	}

	/**
	 * Clib_Request 를 리턴
	 * @return Clib_Response
	 */
	public static function response()
	{
		return Core::loader('Clib_Response');
	}

	/**
	 * Clib_Session 를 리턴
	 * @return Clib_Session
	 */
	public static function session()
	{
		return Core::loader('Clib_Session');
	}

	/**
	 * Clib_memcache 를 리턴
	 * @return Clib_memcache
	 */
	public static function memcache()
	{
		return Core::loader('Clib_memcache');
	}

	/**
	 * Clib_Session 를 리턴
	 * @return Clib_Session
	 */
	public static function xxtea()
	{
		return Core::loader('xxtea');
	}

	/**
	 * 컨트롤러를 생성하고 지정된 action (=메서드)의 실행 결과를 리턴
	 * @example some_controller 의 action 메서드를 실행하는 예제.
	 * @example Clib_Application::execute('some_controller/action');
	 * @param string $keyword
	 * @return mixed
	 */
	public static function execute($keyword)
	{
		$tmp = explode('/', $keyword);

		// parse controller name
		$_tmp = explode('_', $tmp[0]);
		if (sizeof($_tmp) === 1) {
			$_tmp = array_pad($_tmp, 2, $_tmp[0]);
		}

		$applicationName = implode(' ', $_tmp);
		$applicationName = str_replace(' ', '_', ucwords(sprintf('Clib Controller %s', $applicationName)));

		// parse action name
		if (isset($tmp[1])) {
			$actionName = $tmp[1];
		}
		else {
			$actionName = 'main';
			// default action
		}

		// create instance
		$oApp = new $applicationName();

		if ($oApp instanceof Clib_Controller_Abstract) {
			try {

				$args = func_get_args();
				array_shift($args);

				if (sizeof($args) > 0) {
					return call_user_func_array(array(
						$oApp,
						$actionName
					), $args);
				}
				else {
					return $oApp->$actionName();
				}
			}
			catch (Clib_Exception $ex) {
				Clib_Exception::displayMessage($ex);
			}
		}
		else {
			// error..

		}
	}

	/**
	 * Clib_ApiSpec 리턴
	 * @return Clib_ApiSpec
	 */
	public static function apiSpec($api_name)
	{
		return Core::loader('Clib_ApiSpec', $api_name);
	}

	public static function form($name)
	{
		$name = self::getClassName('Form', $name);
		return self::_getClass($name);
	}

	/**
	 * functionable 한 내부 api 를 리턴
	 * @param string $name
	 * @return object
	 */
	public static function iapi($name)
	{
		$name = self::getClassName('IApi', $name);
		return self::_getSingletonModelClass($name);
	}

	/**
	 * 입력 object 의 마지막 클래스명을 리턴
	 * @param object $object
	 * @return string
	 */
	public static function getClassLastName($object)
	{
		if (is_object($object) && method_exists($object, 'getClassName')) {
			return implode('_', array_slice(explode('_', $object->getClassName()), 2));
		}
		else {
			return '';
		}

	}

	public static function getAlias($modelName)
	{
		static $aliases = array();

		if ( ! $aliases[$modelName]) {
			$tmp = explode('_', $modelName);

			$alias = '';

			foreach ($tmp as $v) {
				$alias .= $v[0];
			}

			$idx = 0;

			$_alias = strtoupper($alias);
			$alias = $_alias . $idx;

			while (in_array($alias, $aliases)) {
				$alias = $_alias . $idx;
				$idx++;
			}

			$aliases[$modelName] = $alias;
		}

		return $aliases[$modelName];

	}

	/**
	 * @param string $name 헬퍼 클래스명
	 * @return Clib_Helper_Abstract
	 */
	public static function getHelperClass($name)
	{
		$name = self::getClassName('Helper', $name);
		return self::_getClass($name);
	}

	public static function setMobile($bool = true)
	{
		self::$_mobile = $bool;
	}

	public static function isMobile()
	{
		return self::$_mobile;
	}


}
