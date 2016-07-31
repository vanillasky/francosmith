<?php
include "../lib.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI_4');
$checkoutapi = $config->load('checkoutapi');

// operation
switch ((int)$_POST['case']) {
	case 2 :
		$api_name = 'PlaceProductOrder';
		break;
	case 3:
		$api_name = 'ShipProductOrder';
		break;
	default :
		exit('OPERATION_IS_NOT_FOUND');
		break;
}

$idxs = isset($_POST[$api_name.'_OrderID']) ? array_keys($_POST[$api_name.'_OrderID']) : '';
unset($_POST['mode'],$_POST['case'],$_POST[$api_name.'_OrderID'],$_POST['x'],$_POST['y']);

$queue = array();

// queue 생성시, 불필요한 값이 param 에 저장되더라도, api request 시 걸러지므로 무시해도 됨.
for ($i=0,$m=sizeof($idxs);$i<$m;$i++) {
	$idx = $idxs[$i];

	$param = array();

	foreach($_POST as $k => $v) {

		if (is_array($v) && isset($v[$idx]))
			$param[$k] = $v[$idx];
		else
			$param[$k] = $v;

	}

	if (isset($param['ProductOrderIDList'])) {
		$tmp = explode(',',$param['ProductOrderIDList']);
		unset($param['ProductOrderIDList']);
		for ($j=0,$m2=sizeof($tmp);$j<$m2;$j++) {
			$param['ProductOrderID'] = $tmp[$j];
			$queue[] = $param;
		}
	}
	else {
		$queue[] = $param;
	}

}

$_operation_name = array(
'PlaceProductOrder' => '배송준비중(발주확인)',
'ShipProductOrder' => '배송중(발송)'
);

$processMessage=':: 네이버체크아웃 주문 처리 결과'.PHP_EOL;

foreach($queue as $param) {

	if (($rs = $naverCheckoutAPI->request( $api_name , $param )) !== false) {
		$processMessage.= '주문번호 '.$param['ProductOrderID'].'에 대한 '.$_operation_name[$api_name].' 처리에 성공했습니다.'.PHP_EOL;
	}
	else {
		$processMessage.= '주문번호 '.$param['ProductOrderID'].'에 대한 '.$_operation_name[$api_name].' 처리에 실패했습니다.'.PHP_EOL;
		$processMessage.= $naverCheckoutAPI->error.PHP_EOL.PHP_EOL;
	}
}

echo $processMessage;
?>
