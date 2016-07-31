<?
/*********************************************************
* 파일명     :  pAPI.class.php
* 프로그램명 :	pAPI 클래스
* 작성자     :  dn
* 생성일     :  2011.09.29
**********************************************************/	
if(!defined('P_DOMAIN')) define('P_DOMAIN','pad.godo.co.kr');
if(!defined('P_PATH')) define('P_PATH','/shopAPI/');

if(!defined('MAIN_MENU_LIST')) define('MAIN_MENU_LIST', P_PATH.'GPMainMenuList.php');
if(!defined('MAIN_MENU_ITEM')) define('MAIN_MENU_ITEM', P_PATH.'GPMainMenuItem.php');
if(!defined('MAIN_MENU_MODIFY')) define('MAIN_MENU_MODIFY', P_PATH.'GPMainMenuModify.php');
if(!defined('MAIN_MENU_ADD')) define('MAIN_MENU_ADD', P_PATH.'GPMainMenuAdd.php');
if(!defined('MAIN_MENU_DELETE')) define('MAIN_MENU_DELETE', P_PATH.'GPMainMenuDelete.php');
if(!defined('MAIN_MENU_DELETE_ICON')) define('MAIN_MENU_DELETE_ICON', P_PATH.'GPMainMenuDeleteIcon.php');
if(!defined('MY_MENU_LIST')) define('MY_MENU_LIST', P_PATH.'GPMyMenuList.php');
if(!defined('MY_MENU_MODIFY')) define('MY_MENU_MODIFY', P_PATH.'GPMyMenuModify.php');
if(!defined('MY_MENU_ADD')) define('MY_MENU_ADD', P_PATH.'GPMyMenuAdd.php');
if(!defined('MY_MENU_DELETE')) define('MY_MENU_DELETE', P_PATH.'GPMyMenuDelete.php');
if(!defined('BASIC_SCREEN_INFO')) define('BASIC_SCREEN_INFO', P_PATH.'GPBasicScreenInfo.php');
if(!defined('BASIC_SCREEN_ADD')) define('BASIC_SCREEN_ADD', P_PATH.'GPBasicScreenAdd.php');
if(!defined('START_SCREEN_INFO')) define('START_SCREEN_INFO', P_PATH.'GPStartScreenInfo.php');
if(!defined('START_SCREEN_ADD')) define('START_SCREEN_ADD', P_PATH.'GPStartScreenAdd.php');
if(!defined('TEMPLATE_LIST')) define('TEMPLATE_LIST', P_PATH.'GPTemplateList.php');
if(!defined('MY_TEMPLATE_LIST')) define('MY_TEMPLATE_LIST', P_PATH.'GPMyTemplateList.php');
if(!defined('MY_TEMPLATE_DELETE')) define('MY_TEMPLATE_DELETE', P_PATH.'GPMyTemplateDelete.php');
if(!defined('MAIN_TEMPLATE_ADD')) define('MAIN_TEMPLATE_ADD', P_PATH.'GPMainTemplateAdd.php');
if(!defined('MENU_TEMPLATE_ADD')) define('MENU_TEMPLATE_ADD', P_PATH.'GPMenuTemplateAdd.php');
if(!defined('DETAIL_TEMPLATE_ADD')) define('DETAIL_TEMPLATE_ADD', P_PATH.'GPDetailTemplateAdd.php');
if(!defined('USE_TEMPLATE')) define('USE_TEMPLATE', P_PATH.'GPUseTemplate.php');
if(!defined('SET_SHOP_INFO')) define('SET_SHOP_INFO', P_PATH.'GPSetShopInfo.php');
if(!defined('CONTENTS_UPLOAD')) define('CONTENTS_UPLOAD', P_PATH.'GPContentsUpload.php');
if(!defined('GROUP_NM')) define('GROUP_NM', P_PATH.'GPGetGroupNm.php');
if(!defined('GROUP_SID')) define('GROUP_SID', P_PATH.'GPGetGroupSid.php');
if(!defined('NOTICE_PUSH')) define('NOTICE_PUSH', P_PATH.'GPNoticePush.php');

class pAPI {
	
	var $xxtea;

	function pAPI()	{
		$this->xxtea = Core::loader('xxtea');
	}

	function returnData($arr=Array()) {
		return $this->json_encode_ue($arr);
	}

	/*
	 * json_encode 한글 개선
	 */
	function urlencode_ex($arg) {
		if (is_array($arg)) {
			$n_arg = array();
			foreach($arg as $key => $val) {
				$n_arg[urlencode($key)] = $this->urlencode_ex($val);
			}
			return $n_arg;
		}
		else return urlencode($arg);
	}

	function json_encode_ue($arg) {
		return urldecode(json_encode($this->urlencode_ex($arg)));
	}

	/*
	 * json_decode 한글 개선
	 */
	function urldecode_ex($arg) {
		if (is_array($arg)) {
			$n_arg = array();
			foreach($arg as $key => $val) {
				$n_arg[urldecode($key)] = $this->urldecode_ex($val);
			}
			return $n_arg;
		}
		else {
			return urldecode($arg);
		}
	}

	function json_decode_ue($arg) {
		
		$n_arg = json_decode($arg, true);
		return $this->urldecode_ex($n_arg);
		
		/*
		include '../../lib/json.class.php';
		$json = new Services_JSON(1);

		return $json->decode($arg);
		*/

	}

	/*
	 * mcrypt 암호화 복호화
	 * 인증키 사용 위함
	*/
	/*
	function cryptare($text, $key, $alg, $crypt) { 
		$encrypted_data="";
		
		if(!$crypt) {
			$text = base64_decode($text);
		}

		switch($alg) { 
			case "3des": 
				$td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
				break; 
			case "cast-128": 
				$td = mcrypt_module_open('cast-128', '', 'ecb', ''); 
				break;
			case "gost": 
				$td = mcrypt_module_open('gost', '', 'ecb', ''); 
				break;
			case "rijndael-128": 
				$td = mcrypt_module_open('rijndael-128', '', 'ecb', ''); 
				break;
			case "twofish": 
				$td = mcrypt_module_open('twofish', '', 'ecb', ''); 
				break;
			case "arcfour": 
				$td = mcrypt_module_open('arcfour', '', 'ecb', ''); 
				break;
			case "cast-256": 
				$td = mcrypt_module_open('cast-256', '', 'ecb', ''); 
				break;
			case "loki97": 
				$td = mcrypt_module_open('loki97', '', 'ecb', ''); 
				break;
			case "rijndael-192": 
				$td = mcrypt_module_open('rijndael-192', '', 'ecb', ''); 
				break;
			case "saferplus": 
				$td = mcrypt_module_open('saferplus', '', 'ecb', ''); 
				break;
			case "wake": 
				$td = mcrypt_module_open('wake', '', 'ecb', ''); 
				break;
			case "blowfish-compat": 
				$td = mcrypt_module_open('blowfish-compat', '', 'ecb', ''); 
				break;
			case "des": 
				$td = mcrypt_module_open('des', '', 'ecb', ''); 
				break;
			case "rijndael-256": 
				$td = mcrypt_module_open('rijndael-256', '', 'ecb', ''); 
				break;
			case "xtea": 
				$td = mcrypt_module_open('xtea', '', 'ecb', ''); 
				break;
			case "enigma": 
				$td = mcrypt_module_open('enigma', '', 'ecb', ''); 
				break;
			case "rc2": 
				$td = mcrypt_module_open('rc2', '', 'ecb', ''); 
				break;
			default: 
				$td = mcrypt_module_open('blowfish', '', 'ecb', ''); 
				break;
		} 
		
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
		
		$key = substr($key, 0, mcrypt_enc_get_key_size($td)); 
		
		mcrypt_generic_init($td, $key, $iv); 
		
		if($crypt) { 
			$encrypted_data = base64_encode(mcrypt_generic($td, $text)); 
		} 
		else { 
			$encrypted_data = mdecrypt_generic($td, $text); 
		} 
		
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		
		return $encrypted_data; 
	}
	*/
	
	/*
	 * xxtea 암호화 복호화
	 * 인증키 사용 위함
	*/

	function xcryptare($text, $cryptKey, $crypt) { 
		
		if(!$crypt) {
			$text = base64_decode($text);
		}

		$this->xxtea->setKey($cryptKey);

		if($crypt) {
			$ret_text = base64_encode($this->xxtea->encrypt($text));
		}
		else {
			$ret_text = $this->xxtea->decrypt($text);
		}

		return $ret_text;
		/*
		$encrypted_data="";
		
		

		switch($alg) { 
			case "3des": 
				$td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
				break; 
			case "cast-128": 
				$td = mcrypt_module_open('cast-128', '', 'ecb', ''); 
				break;
			case "gost": 
				$td = mcrypt_module_open('gost', '', 'ecb', ''); 
				break;
			case "rijndael-128": 
				$td = mcrypt_module_open('rijndael-128', '', 'ecb', ''); 
				break;
			case "twofish": 
				$td = mcrypt_module_open('twofish', '', 'ecb', ''); 
				break;
			case "arcfour": 
				$td = mcrypt_module_open('arcfour', '', 'ecb', ''); 
				break;
			case "cast-256": 
				$td = mcrypt_module_open('cast-256', '', 'ecb', ''); 
				break;
			case "loki97": 
				$td = mcrypt_module_open('loki97', '', 'ecb', ''); 
				break;
			case "rijndael-192": 
				$td = mcrypt_module_open('rijndael-192', '', 'ecb', ''); 
				break;
			case "saferplus": 
				$td = mcrypt_module_open('saferplus', '', 'ecb', ''); 
				break;
			case "wake": 
				$td = mcrypt_module_open('wake', '', 'ecb', ''); 
				break;
			case "blowfish-compat": 
				$td = mcrypt_module_open('blowfish-compat', '', 'ecb', ''); 
				break;
			case "des": 
				$td = mcrypt_module_open('des', '', 'ecb', ''); 
				break;
			case "rijndael-256": 
				$td = mcrypt_module_open('rijndael-256', '', 'ecb', ''); 
				break;
			case "xtea": 
				$td = mcrypt_module_open('xtea', '', 'ecb', ''); 
				break;
			case "enigma": 
				$td = mcrypt_module_open('enigma', '', 'ecb', ''); 
				break;
			case "rc2": 
				$td = mcrypt_module_open('rc2', '', 'ecb', ''); 
				break;
			default: 
				$td = mcrypt_module_open('blowfish', '', 'ecb', ''); 
				break;
		} 
		
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
		
		$key = substr($key, 0, mcrypt_enc_get_key_size($td)); 
		
		mcrypt_generic_init($td, $key, $iv); 
		
		if($crypt) { 
			$encrypted_data = base64_encode(mcrypt_generic($td, $text)); 
		} 
		else { 
			$encrypted_data = mdecrypt_generic($td, $text); 
		} 
		
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		
		return $encrypted_data; 
		*/
	}

	/*
	 * 인증키 Check
	*/
	function keyCheck($key, $return='') {

		### 고도 설정 화일 
		$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
		$file	= file($file);
		$godo	= decode($file[1],1);
		
		//$decrypt_authentic = base64_decode($key);//$this->cryptare($key, $godo[sno], '3des', 0);
		$decrypt_authentic = $this->xcryptare($key, $godo['sno'], 0);
		
		$db = &$GLOBALS['db'];
		$arr_key = explode('|', $decrypt_authentic);
		
		if(trim($arr_key[0]) == 'id' && trim($arr_key[1]) == 'pwd') {
			return 'OK';
		}

		$chk_query = $db->_query_print('SELECT name, m_no FROM '.GD_MEMBER.' WHERE m_id=[s] AND password in (password([s]),old_password([s]),[s],[s]) AND level > [i]', $arr_key[0], base64_decode($arr_key[1]), base64_decode($arr_key[1]), md5(base64_decode($arr_key[1])), base64_decode($arr_key[1]), 80); 

		$res_chk = $db->_select($chk_query);
		$row_chk = $res_chk[0];

		if($row_chk['m_no']) {
			if($return == 'm_no') {
				return $row_chk['m_no'];
			}
			else {
				return $row_chk['name'];
			}
		}
		else {
			return '';
		}
	}

	function connectPadAPI($mode, $arr) {
		
		$post_value = $arr;
		$mode = strToLower($mode);
		switch($mode) {
			case 'getmainmenu' :
				$url = 'http://'.P_DOMAIN.MAIN_MENU_LIST;
				break;
			case 'getmainmenuitem' :
				$url = 'http://'.P_DOMAIN.MAIN_MENU_ITEM;
				break;
			case 'mainmenumodify' :
				$url = 'http://'.P_DOMAIN.MAIN_MENU_MODIFY;
				break;
			case 'mainmenuadd' :
				$url = 'http://'.P_DOMAIN.MAIN_MENU_ADD;
				break;
			case 'mainmenudelete' :
				$url = 'http://'.P_DOMAIN.MAIN_MENU_DELETE;
				break;
			case 'mainmenudeleteicon' :
				$url = 'http://'.P_DOMAIN.MAIN_MENU_DELETE_ICON;
				break;
			case 'getmymenu' : 
				$url = 'http://'.P_DOMAIN.MY_MENU_LIST;
				break;
			case 'mymenuadd' : 
				$url = 'http://'.P_DOMAIN.MY_MENU_ADD;
				break;
			case 'mymenumodify' : 
				$url = 'http://'.P_DOMAIN.MY_MENU_MODIFY;
				break;
			case 'mymenudelete' : 
				$url = 'http://'.P_DOMAIN.MY_MENU_DELETE;
				break;
			case 'getbasicscreen' : 
				$url = 'http://'.P_DOMAIN.BASIC_SCREEN_INFO;
				break;
			case 'basicscreenadd' : 
				$url = 'http://'.P_DOMAIN.BASIC_SCREEN_ADD;
				break;
			case 'getstartscreen' : 
				$url = 'http://'.P_DOMAIN.START_SCREEN_INFO;
				break;
			case 'startscreenadd' : 
				$url = 'http://'.P_DOMAIN.START_SCREEN_ADD;
				break;
			case 'gettemplate' :
				$url = 'http://'.P_DOMAIN.TEMPLATE_LIST;
				break;
			case 'getmytemplate' :
				$url = 'http://'.P_DOMAIN.MY_TEMPLATE_LIST;
				break;
			case 'mytemplatedelete' :
				$url = 'http://'.P_DOMAIN.MY_TEMPLATE_DELETE;
				break;
			case 'maintemplateadd' :
				$url = 'http://'.P_DOMAIN.MAIN_TEMPLATE_ADD;
				break;
			case 'menutemplateadd' :
				$url = 'http://'.P_DOMAIN.MENU_TEMPLATE_ADD;
				break;
			case 'detailtemplateadd' :
				$url = 'http://'.P_DOMAIN.DETAIL_TEMPLATE_ADD;
				break;
			case 'getusetemplate' :
				$url = 'http://'.P_DOMAIN.USE_TEMPLATE;
				break;
			case 'setshopinfo' :
				$url = 'http://'.P_DOMAIN.SET_SHOP_INFO;
				break;
			case 'contentsupload':
				$url = 'http://'.P_DOMAIN.CONTENTS_UPLOAD;
				break;
			case 'getgroupnm':
				$url = 'http://'.P_DOMAIN.GROUP_NM;
				break;
			case 'getgroupsid':
				$url = 'http://'.P_DOMAIN.GROUP_SID;
				break;
			case 'noticepush' :
				$url = 'http://'.P_DOMAIN.NOTICE_PUSH;
				break;
		}
		
		$ret = $this->curlFunc($url, $post_value);

		return $ret;

	}

	function curlFunc($url, $post_value) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_value);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);

		$contents = curl_exec($ch);

		return $contents;
	}
	
	function convertEncodeObj($cvt_data, $in_charset, $out_charset) {
		
	}

	function convertEncodeArr($cvt_data, $in_charset, $out_charset) {
		$tmp_data;
		if(!empty($cvt_data) && is_array($cvt_data)) {
			
			foreach($cvt_data as $key => $val) {

				if(is_array($val)) {
					
					$tmp_data[iconv($in_charset, $out_charset, $key)] = $this->convertEncodeArr($val, $in_charset, $out_charset);
				}
				else {
					$tmp_data[iconv($in_charset, $out_charset, $key)] = iconv($in_charset, $out_charset, $val);
				}
			}
		}
		else {
			if($cvt_data) {
				$tmp_data = iconv($in_charset, $out_charset, $cvt_data);
			}
		}

		return $tmp_data;
	}

	/*
	 * 메인 메뉴(샵터치용 카테고리) 리스트 가져오기
	*/
	function getMainMenu($sno, $parent=0) {

		$arr['sno'] = $sno;
		$arr['parent'] = $parent;
		$ret = $this->connectPadAPI('getmainmenu', $arr);
		return $ret;
	}

	/*
	 * 메인 메뉴(샵터치용 카테고리) 아이템 가져오기
	*/
	function getMainMenuItem($sno, $category) {

		$arr['sno'] = $sno;
		$arr['category'] = $category;

		$ret = $this->connectPadAPI('getmainmenuitem', $arr);
		return $ret;
	}

	/*
	 * 메인 메뉴(샵터치용 카테고리) 수정하기
	*/
	function mainMenuModify($sno, $arr) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mainmenumodify', $arr);
		return $ret;
	}

	/*
	 * 메인 메뉴(샵터치용 카테고리) 추가하기
	*/
	function mainMenuAdd($sno, $arr) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mainmenuadd', $arr);
		return $ret;
	}

	/*
	 * 메인 메뉴(샵터치용 카테고리) 삭제하기
	*/
	function mainMenuDelete($sno, $arr) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mainmenudelete', $arr);
		return $ret;
	}

	/*
	 * 메인 메뉴(샵터치용 카테고리) 아이콘 삭제하기
	*/
	function mainMenuDeleteIcon($sno, $arr) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mainmenudeleteicon', $arr);
		return $ret;
	}

	/*
	 * 마이 메뉴(샵터치용) 리스트 가져오기
	*/
	function getMyMenu($sno) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getmymenu', $arr);
		return $ret;
	}

	/*
	 * 마이 메뉴(샵터치용) 수정하기
	*/
	function myMenuModify($sno, $arr) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mymenumodify', $arr);
		return $ret;
	}

	/*
	 * 마이 메뉴(샵터치용) 추가하기
	*/
	function myMenuAdd($sno, $arr) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mymenuadd', $arr);
		return $ret;
	}

	/*
	 * 마이 메뉴(샵터치용) 삭제하기
	*/
	function myMenuDelete($sno, $arr) {
		
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mymenudelete', $arr);
		return $ret;
	}

	/*
	 * 기본화면 설정정보 가져오기
	*/
	function getBasicScreen($sno) {

		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getbasicscreen', $arr);
		return $ret;
	}

	/*
	 * 기본화면 설정하기
	*/
	function basicScreenAdd($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('basicscreenadd', $arr);
		return $ret;
	}

	/*
	 * 시작화면 정보 가져오기
	*/
	function getStartScreen($sno) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getstartscreen', $arr);
		return $ret;
	}

	/*
	 * 시작화면 정보 설정
	*/
	function startScreenAdd($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('startscreenadd', $arr);
		return $ret;
	}

	/*
	 * 템플릿 리스트 가져오기
	*/
	function getTemplate($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('gettemplate', $arr);
		return $ret;
	}

	/*
	 * 나의 템플릿 리스트 가져오기
	*/
	function getMyTemplate($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getmytemplate', $arr);
		return $ret;
	}

	/*
	 * 나의 템플릿 삭제
	*/
	function myTemplateDelete($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('mytemplatedelete', $arr);
		return $ret;
	}

	/*
	 * 메인화면 템플릿 설정
	*/
	function mainTemplateAdd($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('maintemplateadd', $arr);
		return $ret;
	}

	/*
	 * 메뉴화면 템플릿 설정
	*/
	function menuTemplateAdd($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('menutemplateadd', $arr);
		return $ret;
	}

	/*
	 * 상세화면 템플릿 설정
	*/
	function detailTemplateAdd($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('detailtemplateadd', $arr);
		return $ret;
	}

	/*
	 * 사용중인 템플릿 가져오기
	*/
	function getUseTemplate($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getusetemplate', $arr);
		return $ret;
	}

	/*
	 * 샵정보 세팅
	*/
	function setShopInfo($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('setshopinfo', $arr);
		return $ret;
	}

	/*
	 * 컨텐츠 업로드(상품이미지)
	*/
	function contentsUpload($sno, $arr) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('contentsupload', $arr);
		return $ret;
	}

	/*
	 * 만료일자 Check
	*/
	function chkExpireDate($os='') {
		GLOBAL $db;
		
		$category = 'shoptouch';

		$status_apple_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'apple_status');
		$apple_status_res = $db->_select($status_apple_query);
		$apple_status = $apple_status_res[0]['value'];

		$status_android_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'android_status');
		$android_status_res = $db->_select($status_android_query);
		$android_status = $android_status_res[0]['value'];
		
		$chk_apple_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'apple_expire_dt');
		$apple_res = $db->_select($chk_apple_query);
		$expire_dt_apple = $apple_res[0]['value'];

		$chk_android_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'android_expire_dt');
		$android_res = $db->_select($chk_android_query);
		$expire_dt_android = $android_res[0]['value'];

		
		$expire_dt = '';

		if($os == 'apple') {
			$expire_dt = $expire_dt_apple;
			$status = $apple_status;
		}
		else if($os == 'android') {
			$expire_dt = $expire_dt_android;
			$status = $android_status;
		}
		else {
			
			if($expire_dt_apple > $expire_dt_android) {
				$expire_dt = $expire_dt_apple;
			}
			else {
				$expire_dt = $expire_dt_android;
			}

			if($apple_status == 'open' || $android_status=='open') {
				$status = 'open';
			}
			else {
				$status = 'close';
			}
		}

		$now_dt = date('Y-m-d H:i:s');
		
		if($expire_dt >= $now_dt && $status == 'open') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * 만료일자 가져오기
	*/
	function getExpireDate($os='') {
		GLOBAL $db;
		
		$category = 'shoptouch';

		$chk_apple_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'apple_expire_dt');
		$apple_res = $db->_select($chk_apple_query);
		$expire_dt_apple = $apple_res[0]['value'];

		$chk_android_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'android_expire_dt');
		$android_res = $db->_select($chk_android_query);
		$expire_dt_android = $android_res[0]['value'];

		
		$expire_dt = '';

		if($os == 'apple') {
			$expire_dt = $expire_dt_apple;
		}
		else if($os == 'android') {
			$expire_dt = $expire_dt_android;
		}
		else {
			
			if($expire_dt_apple > $expire_dt_android) {
				$expire_dt = $expire_dt_apple;
			}
			else {
				$expire_dt = $expire_dt_android;
			}
		}

		return $expire_dt;
	}

	/*
	 * 그룹네임 가져오기
	*/
	function getGroupNm($sno) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getgroupnm', $arr);
		return $ret;
	}

	/*
	 * 그룹Sid 가져오기
	*/
	function getGroupSid($sno) {
		$arr['sno'] = $sno;
		$ret = $this->connectPadAPI('getgroupsid', $arr);
		return $ret;
	}

	/*
	 * notice_push
	*/
	function noticePush($arr) {
		
		if($this->chkExpireDate) {
			$db = &$GLOBALS['db'];
			
			$chk_noti_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'notice_'.$arr['msg_type']);
			$res_noti = $db->_select($chk_noti_query);
			$chk_noti = $res_noti[0]['value'];

			if($chk_noti) {
				### 고도 설정 화일 
				$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
				$file	= file($file);
				$godo	= decode($file[1],1);
				
				$arr['sno'] = $godo['sno'];
				$ret = $this->connectPadAPI('noticepush', $arr);
				return $ret;
			}
		}

	}

	/*
	 * notice_push_order
	*/
	function noticePushOrder($ordno) {
		
		if($this->chkExpireDate) {

			$db = &$GLOBALS['db'];
			
			$chk_noti_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'notice_order');
			$res_noti = $db->_select($chk_noti_query);
			$chk_noti = $res_noti[0]['value'];
			
			if($chk_noti) {
				$arr = Array();

				### 고도 설정 화일 
				$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
				$file	= file($file);
				$godo	= decode($file[1],1);
				
				$arr['sno'] = $godo['sno'];
				
				$ord_query = $db->_query_print('SELECT nameOrder FROM '.GD_ORDER.' WHERE ordno=[s]', $ordno); 
				$res_ord = $db->_select($ord_query);
				$row_ord = $res_ord[0];
				
				include '../conf/config.php';

				$arr['title'] = $cfg['shopName'];
				$arr['msg'] = '['.$row_ord['nameOrder'].']님의 주문이 접수 되었습니다.';
				$arr['msg_type'] = 'order';
				
				
				$ret = $this->connectPadAPI('noticepush', $arr);
				return $ret;
			}
		}
	}
}
?>
