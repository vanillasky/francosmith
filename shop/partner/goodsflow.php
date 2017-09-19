<?php
include "../lib/library.php";
include dirname(__FILE__).'/../lib/goodsflow_v2.class.php';

header("Content-type:application/json; charset=utf-8; ");

$_gf_delivery_company_map = array(
	'KOREX' => '4',
	'CJGLS' => '15',
	'SAGAWA' => '17',
	'YELLOW' => '8',
	'KGB' => '5',
	'DONGBU' => '21',
	'EPOST' => '9',
	'REGISTPOST' => '18',
	'HANJIN' => '12',
	'HYUNDAI' => '13',
	'KGBLS' => '1',
	'HANARO' => '20',
	'INNOGIS' => '32',
	'DAESIN' => '33',
	'ILYANG' => '22',
	'KDEXP' => '39',
	'CHUNIL' => '19'
);

function __result($result, $msg, $failed=null) {
	if ($result === true) {
		$rs['success'] = true;
		$rs['message'] = '';
	}
	else {
		if (!$msg) {
			$msg = '�������� ��û�� ó���� �� ����';
		}
		$rs['success'] = false;
		$rs['message'] = $msg;
	}

	goodsflow_v2::resultlog('RESPONSE=' . ($t = gd_json_encode($rs)), '�����ȣ ���� ���');
	$rs['message'] = iconv('EUC-KR', 'UTF-8', $rs['message']);
	$response = gd_json_encode($rs);
	exit($response);
}

$postdata = file_get_contents('php://input');
goodsflow_v2::resultlog('POST DATA='.$postdata, '�����ȣ ����');
$postdata = gd_json_decode($postdata, true);
$result = $postdata;
if (empty($result)) __result(false, '�������� ��û�� ó���� �� ����', false);

$queues = array();

foreach ($result['data'] as $dtnPrint) {
	foreach ($dtnPrint as $_tmp){
		// UniqueCd
		$UniqueCd = $_tmp['uniqueCd'];
		// �ù�� �ڵ�
		$deliveryno = $_gf_delivery_company_map[strtoupper($_tmp['deliverCode'])];
		// ���� ��ȣ
		$deliverycode = $_tmp['sheetNo'];

		$queues[] = array(
				'UniqueCd' => $UniqueCd,
				'deliveryno' => $deliveryno,
				'deliverycode' => $deliverycode
		);
	}
}

for ($i=0,$m=sizeof($queues);$i<$m;$i++) {

	$queue = $queues[$i];

	// �½��÷� uniquecd ����
	if (($gf = $db->fetch("SELECT * FROM gd_goodsflow WHERE UniqueCD = '".$queue['UniqueCd']."' ",1)) == false) continue;

	switch ($gf['type']) {

		case 'package':
		case 'casebyorder':

			$query = "
			UPDATE ".GD_GOODSFLOW." AS GF

			INNER JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OD
			ON GF.sno = OD.goodsflow_sno

			INNER JOIN ".GD_ORDER." AS O
			ON OD.ordno = O.ordno

			SET
				O.deliveryno = '".$queue['deliveryno']."',
				O.deliverycode = '".$queue['deliverycode']."',
				GF.status = 'print_invoice'

			WHERE GF.UniqueCd = '".$queue['UniqueCd']."'
			";
			break;

		case 'partial':
		case 'casebygoods':
			// ٽ �ֹ��� �ù���ڵ�, �����ȣ�� ���� ������Ʈ �Ѵ�.
			$query = "
			UPDATE ".GD_GOODSFLOW." AS GF

			INNER JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OD
			ON GF.sno = OD.goodsflow_sno

			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OD.ordno = OI.ordno AND OD.item_sno = OI.sno

			INNER JOIN ".GD_ORDER." AS O
			ON OD.ordno = O.ordno

			SET
				OI.dvno = '".$queue['deliveryno']."',
				OI.dvcode = '".$queue['deliverycode']."',
				GF.status = 'print_invoice',
				O.deliveryno = IF (O.deliveryno != '".$queue['deliveryno']."' , '".$queue['deliveryno']."', O.deliveryno),
				O.deliverycode =  IF (O.deliverycode != '".$queue['deliverycode']."' , '".$queue['deliverycode']."', O.deliverycode)

			WHERE GF.UniqueCd = '".$queue['UniqueCd']."'
			";
			break;
		default:
			$query = '';

	}

	if ($query && $db->query($query)) unset($queues[$i]);

} // for

__result( (empty($queues) ? true : false),'',$queues);
?>