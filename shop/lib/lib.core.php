<?php
function &load_class($obj_name,$class_name=null,$arg1=null,$arg2=null,$arg3=null)
{
	static $instances=array();
	if($class_name===null)
	{
		return $instances[$obj_name];
	}
	else if($instances[$obj_name])
	{
		return $instances[$obj_name];
	}
	else
	{
		if(!class_exists($class_name, false)) {
			$customClassMap = array(
				'Acecounter'=>SHOPROOT.'/lib/acecounter.class.php',
				'NaverCheckout'=>SHOPROOT.'/lib/naverCheckout.class.php',
				'Crypt_XXTEA'=>SHOPROOT.'/lib/xxtea.class.php',
				'Sms'=>SHOPROOT.'/lib/sms.class.php',
				'Goods'=>SHOPROOT.'/lib/goods.class.php',
				'aMail'=>SHOPROOT.'/lib/amail.class.php',
				'Bank'=>SHOPROOT.'/lib/bank.class.php',
				'Captcha'=>SHOPROOT.'/lib/captcha.class.php',
				'Cart'=>SHOPROOT.'/lib/cart.class.php'
			);
			if($customClassMap[$class_name]) {
				include($customClassMap[$class_name]);
			}
			else {
				include(SHOPROOT.'/lib/'.$class_name.'.class.php');
			}
		}

		if($obj_name=='READY') {
			return;
		}

		if($arg1===null) $instances[$obj_name]=new $class_name();
		else if($arg2===null) $instances[$obj_name]=new $class_name(&$arg1);
		else if($arg3===null) $instances[$obj_name]=new $class_name(&$arg1,&$arg2);
		else $instances[$obj_name]=new $class_name(&$arg1,&$arg2,&$arg3);
		return $instances[$obj_name];
	}
}


function stripslashes_all(&$var) {
	foreach($var as $k=>$v)
	{
		if(is_array(&$var[$k])) stripslashes_all(&$var[$k]);
		else $var[$k]=stripslashes(&$var[$k]);
	}
}

function getvalue_chg() {
	$temp = $_GET;
	$arg_list = func_get_args();
	$arg_num = count($arg_list);
	if($arg_num%2) return false;

	for($i=0;$i<($arg_num/2);$i++) {
		if(is_null($arg_list[$i*2+1]))
			unset($temp[$arg_list[$i*2]]);
		else
			$temp[$arg_list[$i*2]] = $arg_list[$i*2+1];
	}

	return http_build_query($temp);
}

function array_value_cheking($ar_fields,$ar_data) {
	$ar_result = array();
	foreach($ar_data as $field_name=>$value)
	{
		$ar_attr = $ar_fields[$field_name];

		if(strlen($value)==0 && $ar_attr['require']!=true) {
			continue;
		}

		if(strlen($value)==0 && $ar_attr['require']==true) {
			$ar_result[$field_name][] = 'require';
			continue;
		}

		switch($ar_attr['type'])
		{
			case 'int':
				if(!ctype_digit((string)$value)) $ar_result[$field_name][] = 'type';
				break;
			case 'float':
				if(!preg_match('/^-?[0-9]+(\.[0-9]+)?$/',$value)) $ar_result[$field_name][] = 'type';
				break;
			case 'digit':
				if(!ctype_digit((string)$value)) $ar_result[$field_name][] = 'type';
				break;
			case 'alnum':
				if(!ctype_alnum($value)) $ar_result[$field_name][] = 'type';
				break;
		}

		if($ar_attr['max_byte'] && $ar_attr['max_byte']<strlen($value))
		{
			$ar_result[$field_name][] = 'max_byte';
		}
		if($ar_attr['min_byte'] && $ar_attr['min_byte']<strlen($value))
		{
			$ar_result[$field_name][] = 'min_byte';
		}

		if($ar_attr['max_length'] && $ar_attr['max_length']<mb_strlen($value,'EUC-KR'))
		{
			$ar_result[$field_name][] = 'max_length';
		}
		if($ar_attr['min_length'] && $ar_attr['min_length']>mb_strlen($value,'EUC-KR'))
		{
			$ar_result[$field_name][] = 'min_length';
		}
		if($ar_attr['pattern'] && !preg_match($ar_attr['pattern'],$value))
		{
			$ar_result[$field_name][] = 'pattern';
		}

		if($ar_attr['array'] && !in_array($value,$ar_attr['array']))
		{
			$ar_result[$field_name][] = 'array';
		}

		if($ar_attr['callback']) {
			if(!call_user_func($ar_attr['callback'],$value)) {
				$ar_result[$field_name][] = 'callback';
			}
		}
	}
	return $ar_result;
}

if(!function_exists('http_build_query')) {
    function http_build_query( $formdata, $numeric_prefix = null, $key = null ) {
        $res = array();
        foreach ((array)$formdata as $k=>$v) {
            $tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
            if ($key) {
                $tmp_key = $key.'['.$tmp_key.']';
            }
            if ( is_array($v) || is_object($v) ) {
                $res[] = http_build_query($v, null, $tmp_key);
            } else {
                $res[] = $tmp_key."=".urlencode($v);
            }
        }
        return implode("&", $res);
    }
}

function array_diff_unset(&$ar_data,$keys) {
	foreach($ar_data as $k=>$v)
	{
		if(!in_array($k,$keys))
		{
			unset($ar_data[$k]);
		}
	}
}


/*
	특정 배열을 FORM의 POST값으로 모두 전달 할때 유용한 함수입니다

	예)
		$forward_array = array(
			'name'=>'godo_worker',
			'zipcode'=>array('111','222'),
			'order_item'=>array(
				array('goodsnm'=>'goods_item','price'=>1000),
				array('goodsnm'=>'bad_item','price'=>1500)
			)
		);
		$serialized_result = array_formpost_serialize($forward_array);

		foreach($serialized_result as $each_result) {
			echo $each_result['name']." || ",$each_result['value']."\n";
		}

	결과)
		name || godo_worker
		zipcode[0] || 111
		zipcode[1] || 222
		order_item[0][goodsnm] || goods_item
		order_item[0][price] || 1000
		order_item[1][goodsnm] || bad_item
		order_item[1][price] || 1500
*/
function array_formpost_serialize(&$forward_array) {
	$result = http_build_query($forward_array);
	$ar_value=explode('&',$result);
	$ar_result=array();
	foreach($ar_value as $v) {
		$tmp = explode('=',$v);
		$ar_result[] = array(
			'name'=>urldecode($tmp[0]),
			'value'=>urldecode($tmp[1])
		);
	}
	return $ar_result;
}

if(!function_exists('scandir')) {
    function scandir($directory,$sorting_order = 0) {
		 if(!is_dir($directory)) return FALSE;
		$d = dir($directory);
		$arResult = array();
		while (false !== ($entry = $d->read())) {
			$arResult[]=$entry;
		}
		if($sorting_order) {
			rsort($arResult);
		}
		else {
			sort($arResult);
		}
		return $arResult;
    }
}

function scandir_all_file($path,$pattern=false) {
	$arResult=array();
	if(!is_dir($path)) {
		return $arResult;
	}
	$scaned = scandir($path);
	foreach($scaned as $v) {
		if($v[0]=='.') continue;
		$each_path = $path.'/'.$v;
		if(is_dir($each_path)) {
			$tmp = scandir_all_file($each_path,$pattern);
			foreach($tmp as $t) $arResult[]=$t;
		}
		elseif(is_file($each_path)) {
			if($pattern && !preg_match($pattern,$each_path)) {
				continue;
			}
			$arResult[] =$each_path;
		}
	}
	return $arResult;
}

function h(&$str) {
	return htmlspecialchars($str);
}

function iconv_recursive($in_charset,$out_charset,$mixed) {
	if(is_array($mixed)) {
		foreach($mixed as $k=>$eachValue) {
			$mixed[$k] = iconv_recursive($in_charset,$out_charset,$eachValue);
		}
		return $mixed;
	}
	elseif(is_object($mixed)) {
		foreach($mixed as $k=>$eachValue) {
			$mixed->$k = iconv_recursive($in_charset,$out_charset,$eachValue);
		}
		return $mixed;
	}
	elseif(is_string($mixed)) {
		return iconv($in_charset,$out_charset,$mixed);
	}
	else {
		return $mixed;
	}
}

// 쇼핑몰 IP 접속제한 처리
$IPAccessRestriction	= Core::loader('IPAccessRestriction');
$IPAccessRestriction->setUserAccessIP();
?>