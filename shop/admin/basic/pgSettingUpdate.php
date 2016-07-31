<?
require "../../lib/library.php";
require "../../lib/json.class.php";
include "../../conf/config.php";
include "../../conf/pg.".$cfg['settlePg'].".php";

//godo솔루션정보
$godo = $config->load('godo');

$pg_name = $cfg['settlePg'];

switch($pg_name) {
	case 'agspay':
		$pg_name = 'allthegate';
		break;
	case 'allatbasic':
		$pg_name = 'allat';
		break;
	case 'dacom':
		$pg_name = 'lgdacom';
		break;
	case 'inipay':
		$pg_name = 'inicis';
		break;
	default:

}

$data = array(
'basic_sno' => $godo['sno'],
'pg_name' => $pg_name,
'pg_id' => $pg['id'],
);
 
$json = new Services_JSON();
//$actionUrl = 'https://pgapi.godo.co.kr/solution/test_solution_renew_setting.php';
$actionUrl = 'http://pgapi.godo.co.kr/solution/solution_renew_setting.php';
$responseData = readpost($actionUrl, $data);
$responseResult =  get_object_vars($json->decode(stripslashes($responseData)));

if($responseResult['result'] == 'ok') {
	echo "<script>alert('결제수단을 새로고침하였습니다.');parent.location.reload();</script>";
}
else{
	echo "<script>alert('결제수단 새로고침을 실패하였습니다. \\n서비스 신청이 완료된 상태라면 고객센터로 문의하여 주세요. \\n(".$responseResult['error_msg'].") ');</script>";
}
?>