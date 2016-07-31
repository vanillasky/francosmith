<?
/*********************************************************
* 파일명     :  pCategoryName.php
* 프로그램명 :	카테고리 명 API
* 작성자     :  dn
* 생성일     :  2011.11.30
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### 인증키 Check (실제로는 아이디와 비번 임) 시작 ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### 인증키 Check 끝 ###

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		if(strstr($key, 'arr_')) {
			${str_replace('arr_', '', $key)} = explode('|', $val);
		}
		else  {
			${$key} = $val;
		}
	}
}

if(!empty($category) && is_array($category)) {
	foreach($category as $val_category) {
		$res_category['category'] = $val_category;
		$res_category['catnm'] = currPosition($val_category, 1);

		$res_data[] = $res_category;
	}
}
echo ($json->encode($res_data));

?>