<?
include "../_header.popup.php";

echo "<div style='padding-left:10px;'>자동 주문 취소 중입니다...<br>수초에서 수분이 소요 될 수 있습니다.<br><b>자동으로 창이 닫힐때까지 이창을 닫지 말아주십시오!!</b></div>";

$interval = (int)$cfg['autoCancel'];
$unit = (string)$cfg['autoCancelUnit'];

if ($interval > 0) {

	$modifier = sprintf('%d %s',$interval * -1, $unit == 'h' ? 'hours' : 'days' );

	$date = Core::helper('Date')->format( strtotime($modifier, G_CONST_NOW) );

	$query = "
		SELECT
			a.ordno, oi.sno, oi.ea
		FROM ".GD_ORDER." as a
		INNER JOIN ".GD_ORDER_ITEM." as oi
		on a.ordno = oi.ordno
		LEFT JOIN ".GD_ORDER_CANCEL." as b
		ON a.ordno = b.ordno AND b.memo = '자동주문취소'

		WHERE
			a.orddt <= '".$date."'
		AND a.step='0'
		AND a.step2='0'
		AND a.settlekind='a'
		AND b.memo is null
	";

	$res = $db->query($query);

	$arr = array(
		'name'=>'관리자',
		'code'=>'9',
		'memo'=>'자동주문취소',
		'bankcode'=>'',
		'bankaccount'=>'',
		'bankuser'=>''
	);

	$queue = array();

	while($tmp = $db->fetch($res)){
		if (!isset($queue[$tmp['ordno']])) $queue[$tmp['ordno']] = $arr;
		$queue[ $tmp['ordno'] ]['sno'][] = $tmp['sno'];
		$queue[ $tmp['ordno'] ]['ea'][] = $tmp['ea'];
	}

	foreach ($queue as $ordno => $arr) {

		### 주문취소
		chkCancel($ordno,$arr);
		### 재고조정
		if ($cfg['autoCancelRecoverStock'] != 'n') {
			setStock($ordno);
		}
		set_prn_settleprice($ordno);
	}
}
popupReload();
?>
