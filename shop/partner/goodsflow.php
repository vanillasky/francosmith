<?php
include "../lib/library.php";
require_once('../lib/parsexmlstruc.class.php');

header("Content-Type: text/html; charset=utf-8");

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

function __result($result, $msg='',$failed=null) {

	$xml = '';
	$xml .= '<?xml version="1.0" encoding="utf-8"?>'."\r\n";
	$xml .= '<response>'."\r\n";
	$xml .= '<result>'.$result.'</result>'."\r\n";

	if ($msg)
		$xml .= '<errorMsg>'.iconv('euc-kr','utf-8',$msg).'</errorMsg>'."\r\n";
	else 
		$xml .= '<errorMsg></errorMsg>'."\r\n";
	$xml .= '</response>';


	exit($xml);
}


$postdata = isset($_POST['orderListXml']) ? stripslashes($_POST['orderListXml']) : '';

$xml = new StrucXMLParser();
$xml ->parse($postdata);
$res = $xml->parseOut();

$result = (array)$res['ORDERLIST'][0]['child']['ORDER'];
if (empty($result)) exit;

$queues = array();

foreach ($result as $dtnPrint) {

	$_tmp = $dtnPrint['child'];

	// UniqueCd
	$UniqueCd = $_tmp['TRANSUNIQUECD'][0]['data'];

	// 택배사 코드
	$deliveryno = $_gf_delivery_company_map[strtoupper($_tmp['DELIVERCODE'][0]['data'])];

	// 송장 번호
	$deliverycode = $_tmp['SHEETNO'][0]['data'];

	$queues[] = array(
		'UniqueCd' => $_tmp['TRANSUNIQUECD'][0]['data'],
		'deliveryno' => $_gf_delivery_company_map[strtoupper($_tmp['DELIVERCODE'][0]['data'])],
		'deliverycode' => $_tmp['SHEETNO'][0]['data']
		);
}

for ($i=0,$m=sizeof($queues);$i<$m;$i++) {

	$queue = $queues[$i];

	// 굿스플로 uniquecd 정보
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
			// 母 주문의 택배사코드, 송장번호를 같이 업데이트 한다.
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


__result( (empty($queues) ? 'TRUE' : 'FALSE'),'',$queues);
?>