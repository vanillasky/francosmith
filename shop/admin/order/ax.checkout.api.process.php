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

// queue ������, ���ʿ��� ���� param �� ����Ǵ���, api request �� �ɷ����Ƿ� �����ص� ��.
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
'PlaceProductOrder' => '����غ���(����Ȯ��)',
'ShipProductOrder' => '�����(�߼�)'
);

$processMessage=':: ���̹�üũ�ƿ� �ֹ� ó�� ���'.PHP_EOL;

foreach($queue as $param) {

	if (($rs = $naverCheckoutAPI->request( $api_name , $param )) !== false) {
		$processMessage.= '�ֹ���ȣ '.$param['ProductOrderID'].'�� ���� '.$_operation_name[$api_name].' ó���� �����߽��ϴ�.'.PHP_EOL;
	}
	else {
		$processMessage.= '�ֹ���ȣ '.$param['ProductOrderID'].'�� ���� '.$_operation_name[$api_name].' ó���� �����߽��ϴ�.'.PHP_EOL;
		$processMessage.= $naverCheckoutAPI->error.PHP_EOL.PHP_EOL;
	}
}

echo $processMessage;
?>
