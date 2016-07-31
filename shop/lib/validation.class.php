<?php
/**
 * 파라미터검증 Class
 *
 * @author gise, lee <birdmarine@godo.co.kr>
 * @version 1.0 2009/08/04 10:00:00
 * @copyright Copyright (c), Godosoft
 */

class Validation
{
	private static $_noAllowScript = 's\s*c\s*r\s*i\s*p\s*t|v\s*b\s*s\s*c\s*r\s*i\s*pt';	//허용불가 스크립트
	private static $_noAllowAttributeName = 'seekSegmentTime|FSCommand|on[a-zA-Z]{4,}|action';	//허용불가 속성
	private static $_noAllowAttributeValue = 'j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*pt:|v\s*b\s*s\s*c\s*r\s*i\s*p\s*t:|r\s*e\s*d\s*i\s*r\s*e\s*c\s*t|e\s*x\s*p\s*r\s*e\s*s\s*s\s*i\s*o\s*n|
	d\s*o\s*c\s*u\s*m\s*e\s*n\s*t\.c\s*o\s*o\s*k\s*i\s*e';	//허용불가 속성값
	private static $_noAllowTagName = 'meta';	//허용불가 태그
	public static $_pluginLoadTag = array(	//플러그인 정의
		'iframe' => 'src',
		'embed' => 'src',
	);

	public static $_allowPluginTag = 'iframe|embed';	//허용가능 디폴트 플러그인 태그
	public static $_allowPluginDomain = array( //허용가능 디폴트 플러그인 도메인
		'youtube.com',
		'facebook.com',
		'instagram.com',
		'naver.com',
		'daum.net',
		'tistory.com',
		'flicker.com',
	);

	private static $_addSingleSlashes;	//single quotStyle 에 따른 추가slashes
	private static $_addDoubleSlashes;	//double quotStyle 에 따른 추가slashes

	const FILTER_TAG_COMMENT = '<!--Not Allowed Tag Filtered-->';	//허용불가 스크립트 주석
	const FILTER_DOMAIN_COMMENT = '[안내]게시판의 안전한 이용을 위하여 부적절한 태그의 사용을 제한하였습니다.<!--Not Allowed domain Filtered-->';	//허용불가 도메인 주석

	const DEFAULT_KEY = '__DEFAULT__';
	/**
	 * 스크립트 필터
	 * @param String $str
	 * @return $string
	*/
	private static function _convertScript($str)
	{
		$doublePluginPattern = "/(<((".self::$_noAllowScript.").*?)\>)(.*?)(<\/((".self::$_noAllowScript.").*?)>)/ixs"; //닫기태그가 존재할 시 패턴체크
		$str = preg_replace_callback($doublePluginPattern , array(self,'__callbackConvertScript') , $str);

		$singlePluginPattern = "/(<(".self::$_noAllowScript.")+.*?>)/ixs"; //닫기태그가 없을 시 패턴체크
		$str = preg_replace_callback($singlePluginPattern , array(self,'__callbackConvertScript') , $str);

		return $str;
	}

	/**
	 * 스크립트 처리
	 * @param Array $str
	 * @return $string
	*/
	private static function __callbackConvertScript($matches)
	{
		$str = $matches[0];

		if($matches[5]) {	//닫기태그 존재할때
			$str = str_replace($matches[1],self::FILTER_TAG_COMMENT.'<noscript>',$str);
			$str = str_replace($matches[4],'[remove]',$str);
			$str = str_replace($matches[5],'</noscript>',$str);
		}
		else{
			$str = str_replace($matches[1],self::FILTER_TAG_COMMENT.'<noscript />',$str);
		}

		return $str;
	}

	/**
	 * 외부 플러그인 태그 필터
	 * @param String $str
	 * @return $string
	*/
	private static function _convertPlugin($str)
	{
		foreach(self::$_pluginLoadTag as $key=>$val) {
			$pluginTag[] = $key;
		}

		$doublePluginPattern = "/(<((".implode('|',$pluginTag).").*?)\>)(.*?)(<\/((".implode('|',$pluginTag).").*?)>)/ixs"; //닫기태그가 존재할 시 패턴체크
		$str = preg_replace_callback($doublePluginPattern , array(self,'__callbackConvertPlugin') , $str);

		$singlePluginPattern = "/(<(".implode('|',$pluginTag).").*?>)/ixs"; //닫기태그가 존재할 시 패턴체크
		$str = preg_replace_callback($singlePluginPattern , array(self,'__callbackConvertPlugin') , $str);

		return $str;
	}

	/**
	 * 외부 플러그인 태그 처리
	 * @param Array $matches
	 * @return $string
	*/
	private static function __callbackConvertPlugin($matches)
	{
		$str = $matches[0];

		if($matches[5]) {	//닫기태그 존재할때
			$tagName = $matches[3];
		}
		else{
			$tagName = $matches[2];
		}

		if(!in_array(strtolower($tagName), explode('|', self::$_allowPluginTag))) {	//허용불가 태그면  무조건 치환
			if($matches[5]){	//닫힘태그존재할때
				$str = str_replace('</','</no',$str);
			}
			$str = str_replace('<'.$tagName,self::FILTER_DOMAIN_COMMENT.'<no'.$tagName,$str);
			return $str;
		}

		$loadTag = self::$_pluginLoadTag[strtolower($tagName)];
		$attributeUnitPattern = '/('.$loadTag.')=
			(
				 '.self::$_addSingleSlashes.'\'((.*?)[^\\\])*?'.self::$_addSingleSlashes.'\'|
				'.self::$_addDoubleSlashes.'"((.*?)[^\\\])*?'.self::$_addDoubleSlashes.'"|
				 `((.*?)[^\\\])*?`|
				 ([^\s<>])+
			)\s*
		/ixs';

		preg_match($attributeUnitPattern,$str ,$matches2 );
		$dataUrl = stripslashes(str_replace(array('"','\'') , array('',''),stripslashes($matches2[2])));
		$dataUrl = str_replace(' ','',$dataUrl);

		$isSearch = false;	//허용가능 도메인 검색여부
		array_push(self::$_allowPluginDomain, 'godo.co.kr');
		foreach(self::$_allowPluginDomain as $key=>$url) {
			$domainPattern = "/(\/\/)?(http(s)?(:)?\/\/)?([^\/]*)/i";
			preg_match($domainPattern,$dataUrl ,$matches3 );
			$domain = $matches3[0];

			if(!empty($url) && !empty($domain)){
				if(strpos($domain, $url)!==false){
					$isSearch = true;
				}
			}
		}

		if($isSearch===false){
			if($matches[5]){	//닫힘태그존재할때
				$str = str_replace('</','</no',$str);
			}
			$str = str_replace('<'.$tagName,self::FILTER_DOMAIN_COMMENT.'<no'.$tagName,$str);
		}

		return $str;
	}

	/**
	 * 태그 치환
	 * @param String $str
	 * @return $string
	*/
	private static function _convertTag($str)
	{
		$doublePluginPattern = "/(<((".self::$_noAllowTagName.").*?)\>)(.*?)(<\/((".self::$_noAllowTagName.").*?)>)/ixs"; //닫기태그가 존재할 시 패턴체크
		$str = preg_replace_callback($doublePluginPattern , array(self,'__callbackConvertTag') , $str);

		$singlePluginPattern = "/(<(".self::$_noAllowTagName.").*?>)/ixs"; //닫기태그가 존재할 시 패턴체크
		$str = preg_replace_callback($singlePluginPattern , array(self,'__callbackConvertTag') , $str);

		// @qnibus 2015-06 앞주석 태그의 갯수와 뒷주석 태그의 갯수를 비교해 일치하지 않으면 앞에 주석 제거
		preg_match('/<!-{2,}/is', $str, $matches1);
		preg_match('/-{2,}>/is', $str, $matches2);
		if (sizeof($matches1[0]) != sizeof($matches2[0])) {
			$str = preg_replace('/<!-{2,}/is', '', $str);
		}

		return $str;
	}

	/**
	 * 태그 처리
	 * @param String $str
	 * @return $string
	*/
	private static function __callbackConvertTag($matches)
	{
		$str = $matches[0];

		if($matches[5]) {	//닫기태그 존재할때
			$tagName = $matches[3];
			$str = str_replace('</','</no',$str);
		}
		else{
			$tagName = $matches[2];
		}

		$str = str_replace('<'.$tagName,self::FILTER_TAG_COMMENT.'<no'.$tagName,$str);

		return $str;
	}

	/**
	 * 엘리먼트 처리
	 * @param String $str
	 * @return $string
	*/
	private static function _convertAttribute($str)
	{
		$tagPattern = '/
		<([a-zA-Z]+).*?
			(
				(\w*)=
					(
						 '.self::$_addSingleSlashes.'\'((.*?)[^\\\])*?'.self::$_addSingleSlashes.'\'|
						'.self::$_addDoubleSlashes.'"((.*?)[^\\\])*?'.self::$_addDoubleSlashes.'"|
						`((.*?)[^\\\])*?`|
						 [^\s<>]+
					)\s*
			)*?
			((\s\w*\s)?).*?
		>
		/ixs';

		$str = preg_replace_callback($tagPattern,array(self,'__callbackConvertDecimalCharacter'),$str);
		$str = preg_replace_callback($tagPattern,array(self,'__callbackConvertAttributeName'),$str);
		$str = preg_replace_callback($tagPattern,array(self,'__callbackConvertAttributeValue'),$str);

		return $str;
	}

	/**
	 * 엘리먼트 어트리뷰트 네임 치환 콜백함수
	 * @param Array $matches
	 * @return $string
	*/
	private static function __callbackConvertAttributeName($matches)
	{
		$attrPattern = '/
		(
			('.self::$_noAllowAttributeName.')=
				 (
				    '.self::$_addSingleSlashes.'\'((.*?)[^\\\])*?'.self::$_addSingleSlashes.'\'|
				    '.self::$_addDoubleSlashes.'"((.*?)[^\\\])*?'.self::$_addDoubleSlashes.'"|
					`((.*?)[^\\\])*?`|
					 [^\s<>]+
			 )\s*
		)
		/ixs';

		$str = ($matches[0]);

		preg_match_all($attrPattern,$str,$matches2,PREG_SET_ORDER);

		$_newTag = $str;
		foreach($matches2 as $key=>$_attr) {
			$_newTag = str_replace($_attr[0],'',$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		return $str;
	}

	/**
	 * 엘리먼트 어트리뷰트 밸류 치환 콜백함수
	 * @param Array $matches
	 * @return $string
	*/
	private static function __callbackConvertAttributeValue($matches)
	{
		$str = $matches[0];

		$commentPattern = array(
			'#\/\*.*\*\/#Uis',
			'/\x00/',
			'/\x0a/',
			'/<!--.*?-->/',
		);

		$str = preg_replace($commentPattern, array('','',''), $str);//주석 또는 공백코드 제거
		$attrValuePattern = '/
		(
			(\w*)=
				 (
				    '.self::$_addSingleSlashes.'\'.*?('.self::$_noAllowAttributeValue.')+.*?[^\\\]'.self::$_addSingleSlashes.'\'|
				    '.self::$_addDoubleSlashes.'".*?('.self::$_noAllowAttributeValue.')+.*?[^\\\]'.self::$_addDoubleSlashes.'"|
					`.*?('.self::$_noAllowAttributeValue.')+.*?[^\\\]`|
					('.self::$_noAllowAttributeValue.')+[^\s<>]+
				 )\s*
		)
		/ixs';

		preg_match_all($attrValuePattern,$str,$matches2,PREG_SET_ORDER);

		$_newTag = $str;
		foreach($matches2 as $key=>$_attr) {
			$_newTag =str_replace($_attr[0],'',$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		return $str;
	}

 	/**
	 * 태그 <,> 치환
	 * @param String $str
	 * @return $string
	*/
	private static function _cleanTag($str)
	{
		$str = str_replace(array('<','>') , array('&lt;','&gt;') , $str);
		return $str;
	}

 	/**
	 * 쿼터 스타일 설정
	 * @param String $quotStyle ent_compat : 홑따옴표에만 역슬래시 처리된 경우  , ent_quotes : 모든따옴표에 역슬래시 처리된경우, ent_noquotes : 역슬래시 처리 안되있는 경우
	 * @return $string
	*/
	private static function _setQuotStyle($quotStyle) {
		if($quotStyle == 'ent_compat') {
			self::$_addSingleSlashes = '\\\\';
			self::$_addDoubleSlashes = '';
		}
		else if($quotStyle == 'ent_quotes'){
			self::$_addSingleSlashes = '\\\\';
			self::$_addDoubleSlashes = '\\\\';
		}
		else if($quotStyle == 'ent_noquotes'){
			self::$_addSingleSlashes = '';
			self::$_addDoubleSlashes = '';
		}
		else{
			self::$_addSingleSlashes = '';
		}
	}

	/**
	 * 허용불가 플러그인 태그설정
	 * @param String $noAllowPluginTag '|' 구분자로 문자열조합
	 * @return
	*/
	private static function _setNoAllowPluginTag($noAllowPluginTag) {
		if(isset($noAllowPluginTag)) {
			if(empty($noAllowPluginTag)) {
				self::$_allowPluginTag = 'null';
			}
			else{
				self::$_allowPluginTag = $noAllowPluginTag;
			}
		}
	}

	/**
	 * 허용불가 플러그인 도메인설정
	 * @param String $allowPluginDomain '|' 구분자로 문자열조합
	 * @return
	*/
	private static function _setAllowPluginDomain($allowPluginDomain) {
		if(isset($allowPluginDomain)) {
			self::$_allowPluginDomain = explode('|',$allowPluginDomain);
		}
	}

	/**
	 * Decimal 또는 Hexadecimal HTML character 치환
	 * @param Array $macthes
	 * @return String
	*/
	private function __callbackConvertDecimalCharacter($matches) {
		$haxPattern = "/&#x([0-9a-f]+);?/i";
		$charPattern = "/&#([0-9]+);?/i";
		$str = $matches[0];

		preg_match_all($haxPattern,$str,$matches2,PREG_SET_ORDER);
		$_newTag = $str;
		foreach($matches2 as $key=>$_attr) {
			$_newTag = str_replace($_attr[0],chr(hexdec($_attr[1])),$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		preg_match_all($charPattern,$str,$matches3,PREG_SET_ORDER);

		$_newTag = $str;
		foreach($matches3 as $key=>$_attr) {
			$_newTag = str_replace($_attr[0],chr($_attr[1]),$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		return $str;
	}

 	/**
	 * xss 보안필터 단일처리
	 * @param String $str  : 처리할 문자열
	 * @param String $mode : 모드 [text : html태그를 사용안할 경우 , html : html태그를 사용할 경우]
	 * @param String $quotStyle [ent_compat : 홑따옴표에만 역슬래시 처리된 경우  , ent_quotes : 모든따옴표에 역슬래시 처리된경우, ent_noquotes : 역슬래시 처리 안되있는 경우]
	 * @param String $noAllowPluginTag : 허용불가 플러그인 태그  '|' 구분자로 문자열조합
	 * @param String $noAllowPluginTag : 허용불가 도메인 '|' 구분자로 문자열조합
	 * @return $string
	*/
	public static function xssClean($str, $mode = 'html' , $quotStyle , $noAllowPluginTag = null, $allowPluginDomain = null, $disable = '')
	{
		if($mode == 'disable' || $disable == 'disable'){
			return $str;
		}

		if($mode == 'text') {
			$str = self::_cleanTag($str);
		}
		else{	//html모드
			self::_setQuotStyle($quotStyle);
			self::_setNoAllowPluginTag($noAllowPluginTag);
			self::_setAllowPluginDomain($allowPluginDomain);
			$str = self::_convertScript($str);
			$str = self::_convertAttribute($str);
			$str = self::_convertPlugin($str);
			$str = self::_convertTag($str);
		}

		return $str;
	}

	/**
	 * xss 보안필터 복수처리
	 * @param String $str  : 처리할 문자열
	 * @param Array $extraData : 복수로 처리할 인자값
	 * @return Array
	*/
	public static function xssCleanArray($str, $extraData)
	{
		if(!is_array($str)) {
			return $str;
		}

		if(array_key_exists(self::DEFAULT_KEY, $extraData)) {	//DEFAULT 처리
			foreach($str as $key=>$val) {
				if(!array_key_exists($key, $extraData)) {
					$arg = array();
					if(!is_array($extraData[self::DEFAULT_KEY])) {
						$arg[0] = $extraData[self::DEFAULT_KEY];
						$arg[1] = null;
						$arg[2] = null;
						$arg[3] = null;
						$arg[4] = null;
					}
					else{
						$arg[0] = $extraData[self::DEFAULT_KEY][0];
						$arg[1] = $extraData[self::DEFAULT_KEY][1];
						$arg[2] = $extraData[self::DEFAULT_KEY][2];
						$arg[3] = $extraData[self::DEFAULT_KEY][3];
						$arg[4] = $extraData[self::DEFAULT_KEY][3];
					}

					$str[$key] = self::xssClean($str[$key],$arg[0], $arg[1],$arg[2], $arg[3], $arg[4] );
				}
			}
		}

		foreach($extraData as $key=>$val) {
			if($key == self::DEFAULT_KEY) {
				continue;
			}

			$arg = array();
			if(!is_array($val)) {
				$arg[0] = $val;
				$arg[1] = null;
				$arg[2] = null;
				$arg[3] = null;
				$arg[4] = null;
			}
			else{
				$arg[0] = $val[0];
				$arg[1] = $val[1];
				$arg[2] = $val[2];
				$arg[3] = $val[3];
				$arg[4] = $val[4];
			}

			$str[$key] = self::xssClean($str[$key],$arg[0], $arg[1], $arg[2], $arg[3], $arg[4] );
		}

		return $str;
	}

	/**
	 * 문자열 검증 이메일형식
	 * @param String $value
	 * @return bool
	*/
	function check_email($value)
	{
		$pattern = "(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*$)";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 전화번호
	 * @param String $value
	 * @return bool
	*/
	function check_phone($value)
	{
		if(is_array($value))$value = implode('-',$value);
		$pattern = "^[0-9]{2,4}\-[0-9]{3,4}\-[0-9]{4}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 date형식
	 * @param String $value
	 * @return bool
	*/
	function check_date($value)
	{
		$pattern = "^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 time형식
	 * @param String $value
	 * @return bool
	*/
	function check_time($value)
	{
		$pattern = "^[0-9]{2}:[0-9]{2}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 datetime형식
	 * @param String $value
	 * @return bool
	*/
	function check_datetime($value)
	{
		$pattern = "^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 도메인형식
	 * @param String $value
	 * @return bool
	*/
	function check_domain($value)
	{
		$pattern = "^[.a-zA-Z0-9-]+\.[a-zA-Z]+$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 url형식
	 * @param String $value
	 * @return bool
	*/
	function check_url($value)
	{
		$pattern = "^[.a-zA-Z0-9-]+\.[a-zA-Z]+[^:space:]+$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * 문자열 검증 최소길이
	 * @param String $value
	 * @return bool
	*/
	function check_min($min,$value)
	{
		if($min > strlen($value))return false;
		return true;
	}

	/**
	 * 문자열 검증 최대길이
	 * @param String $value
	 * @return bool
	*/
	function check_max($max,$value)
	{
		if($max < strlen($value))return false;
		return true;
	}

	/**
	 * 문자열 검증 숫자형식
	 * @param String $value
	 * @return bool
	*/
	function check_digit($value)
	{
		if(is_numeric($value))return true;
		return false;
	}


	/**
	 * 문자열 검증 패턴
	 * @param String $pattern,$value
	 * @return bool
	*/
	function check_pattern($pattern,$value)
	{
		if(ereg($pattern,$value)){
			return true;
		}
		return false;
	}

	function check_require($value)
	{
		$value = trim($value);
		if( empty($value) )return false;
		return true;
	}

	/*
	* 예외 카테고리 체크
	* working by
	* @param int, array [goodsno - 상품번호], [cfgArr - 예외카테고리가 지정된 array형 정보]
	* @return boolean true - 예외카테고리포함, false - 예외카테고리미포함
	* @date 2015-01-29
	*/
	function check_exception_category($goodsno, $exception_category)
	{
		global $db;

		if(!$goodsno || !is_array($exception_category)) return false;

		//상품카테고리 구하기
		$res = $db->query("SELECT category FROM " . GD_GOODS_LINK . " WHERE goodsno='".$goodsno."' and category");
		while($data = $db->fetch($res)){
			for($i=3; $i<=strlen($data['category']); $i=$i+3)
			{
				$category = substr($data['category'], 0, $i);
				if(in_array($category, $exception_category) ) return true;
			}
		}
		return false;
	}

	/*
	* 예외 상품 체크
	* working by
	* @param int
	* @return boolean true - 예외상품포함, false - 예외상품미포함
	* @date 2015-01-29
	*/
	function check_exception_goods($goodsno, $exception_goods)
	{
		if(!$goodsno || !is_array($exception_goods)) return false;

		if($exception_goods && in_array($goodsno, $exception_goods)){
			return true;
		}

		return false;
	}
}
?>