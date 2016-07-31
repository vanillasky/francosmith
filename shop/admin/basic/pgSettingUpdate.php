<?
require "../../lib/library.php";
require "../../lib/json.class.php";
include "../../conf/config.php";
include "../../conf/pg.".$cfg['settlePg'].".php";

//godo�ַ������
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
	echo "<script>alert('���������� ���ΰ�ħ�Ͽ����ϴ�.');parent.location.reload();</script>";
}
else{
	echo "<script>alert('�������� ���ΰ�ħ�� �����Ͽ����ϴ�. \\n���� ��û�� �Ϸ�� ���¶�� �����ͷ� �����Ͽ� �ּ���. \\n(".$responseResult['error_msg'].") ');</script>";
}
?>