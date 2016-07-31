<?php
class GODO_Autoload
{

	private static $_instance = null;
	private $_preDefinedClasses = array();

	private function __construct()
	{
		$this->_setPreDefinedClasses();
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new GODO_Autoload();
		}

		return self::$_instance;
	}

	public static function register()
	{
		spl_autoload_register(array(
			self::getInstance(),
			'autoload'
		));
	}

	public static function unregister()
	{
		//spl_autoload_unregister()
	}

	public function autoload($className)
	{

		$_inc = true;

		if ( ! class_exists($className, false)) {

			if (isset($this->_preDefinedClasses[$className]) && is_file($this->_preDefinedClasses[$className])) {
				include_once ($this->_preDefinedClasses[$className]);
			}
			else if (is_file(G_CONST_DOCROOT . '/lib/' . $className . '.class.php')) {
				include_once (G_CONST_DOCROOT . '/lib/' . $className . '.class.php');
			}
			else {

				$temp = explode('_', $className);
				$path = G_CONST_DOCROOT . '/lib/' . implode('/', $temp) . '.php';

				if (is_file($path)) {
					include_once ($path);
				}
				else {

					array_pop($temp);
					// 끝 원소 제거
					$path = G_CONST_DOCROOT . '/lib/' . implode('/', $temp) . '/' . $className . '.php';

					if (is_file($path)) {
						include_once ($path);
					}
					else {
						$_inc = false;
					}

				}

			}

		}

		return $_inc;

	}

	private function _setPreDefinedClasses()
	{
		$this->_preDefinedClasses = array(

			// 편의를 위해 미리 정의
			'Clib_Application' => G_CONST_DOCROOT . '/lib/Clib/Clib_Application.php',
			'GODO_DB' => G_CONST_DOCROOT . '/lib/GODO/DB/DB.php',
			'Error' => G_CONST_DOCROOT . '/lib/GODO/Error/Error.php',
			'Sessions' => G_CONST_DOCROOT . '/lib/GODO/Session/Session.php',
			'Security' => G_CONST_DOCROOT . '/lib/GODO/Security/Security.php',
			'Zipcode' => G_CONST_DOCROOT . '/lib/GODO/Zipcode/Zipcode.php',

			// 이전 클래스
			'Services_JSON' => G_CONST_DOCROOT . '/lib/json.class.php',
			'Acecounter' => G_CONST_DOCROOT . '/lib/acecounter.class.php',
			'NaverCheckout' => G_CONST_DOCROOT . '/lib/naverCheckout.class.php',
			'Crypt_XXTEA' => G_CONST_DOCROOT . '/lib/xxtea.class.php',
			'Sms' => G_CONST_DOCROOT . '/lib/sms.class.php',
			'Goods' => G_CONST_DOCROOT . '/lib/goods.class.php',
			'aMail' => G_CONST_DOCROOT . '/lib/amail.class.php',
			'Bank' => G_CONST_DOCROOT . '/lib/bank.class.php',
			'Captcha' => G_CONST_DOCROOT . '/lib/captcha.class.php',
			'Cart' => G_CONST_DOCROOT . '/lib/cart.class.php',
			'Page' => G_CONST_DOCROOT . '/lib/page.class.php',
			'upload_file' => G_CONST_DOCROOT . '/lib/upload.lib.php',
			'Criteo' => G_CONST_DOCROOT . '/lib/criteo.class.php',
			'AuctionIpay' => G_CONST_DOCROOT . '/lib/auctionIpay.class.php',
			'NateClipping' => G_CONST_DOCROOT . '/lib/nateClipping.class.php',
			'LoadClass' => G_CONST_DOCROOT . '/lib/load.class.php',

			// 소셜로그인
			'SocialMemberService' => G_CONST_DOCROOT . '/lib/SocialMember/SocialMemberServiceLoader.php',
			'SocialMember' => G_CONST_DOCROOT . '/lib/SocialMember/SocialMemberServiceLoader.php',
			'FacebookMember' => G_CONST_DOCROOT . '/lib/SocialMember/SocialMemberServiceLoader.php',
		);

	}

}
