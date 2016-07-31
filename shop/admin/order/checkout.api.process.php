<?php
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI_4');

if (!isset($_POST['OrderID']) || !isset($_POST['ProductOrderIDList'])) {
	echo '<script>parent.closeLayer();</script>';
	exit;
}

$api_name = (string)$_POST['mode'];
$idxs = array_keys($_POST['OrderID']);
$solo = sizeof(array_unique(array_values($_POST['OrderID']))) == 1 ? true : false;	// true 일 경우, 단일 주문 처리 or 상세 페이지내 처리

unset($_POST['mode'],$_POST['OrderID'],$_POST['x'],$_POST['y']);
?>
<div class="title title_top">네이버 체크아웃 주문정보 처리</div>
<div id="el-screen" style="width:100%;border:1px solid #E6E6E6;height:300px;overflow-y:auto;padding:10px;margin:0 0 10px 0;">
<?

$queue = array();
$divide_etcFee = (int)$_POST['EtcFeeDemandAmount'] > 0 ? true : false;

for ($i=0,$m=sizeof($idxs);$i<$m;$i++) {
	$idx = $idxs[$i];

	$param = array();

	foreach($_POST as $k => $v) {

		if (is_array($v) && isset($v[$idx]))
			$param[$k] = $v[$idx];
		else
			$param[$k] = $v;
	}

	if ($divide_etcFee && $solo) $param['EtcFeeDemandAmount'] = ($i==0) ? $param['EtcFeeDemandAmount'] : 0;

	if (isset($param['ProductOrderIDList'])) {
		$tmp = explode(',',$param['ProductOrderIDList']);
		unset($param['ProductOrderIDList']);

		for ($j=0,$m2=sizeof($tmp);$j<$m2;$j++) {
			$param['ProductOrderID'] = $tmp[$j];
			if ($divide_etcFee) $param['EtcFeeDemandAmount'] = ($j==0) ? $param['EtcFeeDemandAmount'] : 0;
			$queue[] = $param;
		}
	}
	else {
		$queue[] = $param;
	}

}

foreach($queue as $param) {

	echo '<strong>'.$param['ProductOrderID'].'</strong> 의 처리를 위해 중계서버와 통신중입니다.<br>';
	echo '<script>document.getElementById("el-screen").scrollTop = document.getElementById("el-screen").scrollHeight;</script>';

	flush();
	if (($rs = $naverCheckoutAPI->request( $api_name , $param )) !== false) {
		echo '정상적으로 처리하였습니다<br><br>';
	}
	else {
		echo '작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error.'<br><br>';
	}

}

echo '<hr>완료되었습니다<br>';
echo '<script>document.getElementById("el-screen").scrollTop = document.getElementById("el-screen").scrollHeight;</script>';
?>
</div>

<input type="button" value="닫기" onclick="parent.location.href=parent.location.href;">
