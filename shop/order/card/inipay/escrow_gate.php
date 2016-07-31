<?php
/**
 * 이니시스 PG 에스크로 배송 등록 페이지
 * 원본 파일명 INIescrow_delivery.html
 * 이니시스 PG 버전 : INIpay V5.0 - 오픈웹 (V 0.1.1 - 20120302)
 */

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET['ordno'];

$query = "
SELECT
	a.settleprice,a.delivery,a.nameReceiver,a.phoneReceiver,a.mobileReceiver,a.zipcode,a.address,a.escrowno,
	a.deliveryno,a.deliverycode,a.delivery,a.ddt
FROM
	".GD_ORDER." a
WHERE
	a.ordno = '$ordno'
";
$data = $db->fetch($query);

// 운송장 번호, 택배사 체크
if (empty($data['deliveryno']) || empty($data['deliverycode'])) {
	msg('운송장 번호나 선택된 택배사가 없습니다. 다시 확인 바랍니다.');
	exit;
}

// 배송비 지급방법 설정
if ($data['delivery'] > 0) {
	$dlvChargeVal	= 'BH';
} else {
	$dlvChargeVal	= 'SH';
}

// 배송등록 확인일시
if (strlen($data['ddt'] > 9)) {
	$dlvInvoiceDay	= $data['ddt'];
} else {
	$dlvInvoiceDay	= date('Y-m-d H:i:s');
}

// 수신자 전화번호
if (empty($data['mobileReceiver']) === false) {
	$recvTel	= $data['mobileReceiver'];
} else {
	$recvTel	= $data['phoneReceiver'];
}

// 택배사 코드 및 택배사 명 설정
$compcode			= array();
$compcode['15']		= array('code'	=> 'cjgls', 'name' =>'CJ GLS');
$compcode['13']		= array('code'	=> 'hyundai', 'name' =>'현대택배');
$compcode['12']		= array('code'	=> 'hanjin', 'name' =>'한진택배');
$compcode['4']		= array('code'	=> 'korex', 'name' =>'대한통운');
$compcode['1']		= array('code'	=> 'kgbls', 'name' =>'KGB택배');
$compcode['5']		= array('code'	=> 'kgb', 'name' =>'로젠택배');
$compcode['9']		= array('code'	=> 'EPOST', 'name' =>'우체국택배');
$compcode['100']	= array('code'	=> 'EPOST', 'name' =>'우체국택배');
$compcode['6']		= array('code'	=> 'hth', 'name' =>'삼성HTH');
$compcode['14']		= array('code'	=> '', 'name' =>'훼미리택배');
$compcode['7']		= array('code'	=> 'ajutb', 'name' =>'아주택배');
$compcode['8']		= array('code'	=> 'yellow', 'name' =>'옐로우캡');
$compcode['22']		= array('code'	=> '', 'name' =>'일양택배');
$compcode['11']		= array('code'	=> 'tranet', 'name' =>'트라넷');
$compcode['2']		= array('code'	=> 'ktlogistics', 'name' =>'KT로지스');
$compcode['18']		= array('code'	=> 'registpost', 'name' =>'우편등기');
$compcode['20']		= array('code'	=> 'Hanaro', 'name' =>'하나로택배');
$compcode['17']		= array('code'	=> 'Sagawa', 'name' =>'사가와익스프레스');
$compcode['16']		= array('code'	=> 'sedex', 'name' =>'SEDEX');
$compcode['21']		= array('code'	=> 'dongbu', 'name' =>'동부택배');
$compcode['9999']	= array('code'	=> '9999', 'name' =>'기타택배');

if (in_array($data['deliveryno'], array_keys($compcode))) {
	$dlvExArr	= $compcode[$data['deliveryno']];
} else {
	$dlvExArr	= $compcode['9999'];
}
?>
<html>
<head>
<title>이니시스 자체 에스크로(INIescrow)</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />

<script language="Javascript">
function f_check(){
	if(document.ini.tid.value == ""){
		alert("거래번호가 빠졌습니다.")
		return;
	}
	else if(document.ini.mid.value == ""){
		alert("상점 아이디가 빠졌습니다.")
		return;
	}
	else if(document.ini.EscrowType.value == ""){
		alert("에스크로 작업을 선택하십시요.")
		return;
	}
	else if(document.ini.invoice.value == ""){
		alert("운송장번호가 빠졌습니다.")
		return;
	}
	else if(document.ini.oid.value == ""){
		alert("주문번호가 빠졌습니다.")
		return;
	}
	document.ini.submit();
}
</script>
</head>

<body>
<form name="ini" method="post" action="./INIescrow_delivery.php">
<input type="hidden" name="ordno"			value="<?php echo $ordno;?>" />								<!-- 주문 번호 - PG 처리와는 전혀 상관이 없는 옵션임 -->
<input type="hidden" name="mid"				value="<?php echo $escrow['id'];?>" />						<!-- * 에스크로 아이디 -->
<input type="hidden" name="tid"				value="<?php echo $data['escrowno'];?>" />					<!-- * 상품구매 거래번호(TID) -->
<input type="hidden" name="oid"				value="<?php echo $ordno;?>" />								<!-- * 상품구매 주문번호(OID) -->
<input type="hidden" name="EscrowType"		value="I" />												<!-- * 에스크로 등록형태 (등록:I, 변경:U) -->

<input type="hidden" name="dlv_name"		value="관리자" />											<!-- * 배송등록자 -->
<input type="hidden" name="dlv_exCode"		value="<?php echo $dlvExArr['code'];?>" />					<!-- * 택배사코드 -->
<input type="hidden" name="dlv_exName"		value="<?php echo $dlvExArr['name'];?>" />					<!-- * 택배사명 -->
<input type="hidden" name="dlv_charge"		value="<?php echo $dlvChargeVal;?>" />						<!-- * 배송비 지급형태 (SH : 판매자부담, BH : 구매자부담) -->
<input type="hidden" name="dlv_invoiceday"	value="<?php echo $dlvInvoiceDay;?>">						<!-- * 배송등록 확인일시 -->
<input type="hidden" name="invoice"			value="<?php echo $data['deliverycode'];?>" />				<!-- * 운송장번호 -->

<input type="hidden" name="sendName"		value="<?php echo $cfg['adminName'];?>" />					<!-- * 송신자 이름 -->
<input type="hidden" name="sendPost"		value="<?php echo $cfg['zipcode'];?>" />					<!-- * 송신자 우편번호 -->
<input type="hidden" name="sendAddr1"		value="<?php echo ($cfg['road_address'] ? $cfg['road_address'] : $cfg['address']);?>" />	<!-- * 송신자 주소1 -->
<input type="hidden" name="sendAddr2"		value="" />													<!-- 송신자 주소2 -->
<input type="hidden" name="sendTel"			value="<?php echo $cfg['compPhone'];?>" />					<!-- * 송신자 전화번호 -->

<input type="hidden" name="recvName"		value="<?php echo $data['nameReceiver'];?>" />				<!-- * 수신자 이름 -->
<input type="hidden" name="recvPost"		value="<?php echo str_replace('-', '', $data['zipcode']);?>" />		<!-- * 수신자 우편번호 -->
<input type="hidden" name="recvAddr"		value="<?php echo $data['address'];?>" />					<!-- * 수신자 주소 -->
<input type="hidden" name="recvTel"			value="<?php echo $recvTel;?>" />							<!-- * 수신자 전화번호 -->

<input type="hidden" name="goodsCode"		value="" />													<!-- 상품코드(선택) -->
<input type="hidden" name="goods"			value="" />													<!-- 상품명(선택) -->
<input type="hidden" name="goodCnt"			value="" />													<!-- 상품수량(선택) -->
<input type="hidden" name="price"			value="<?php echo $data['settleprice'];?>" />				<!-- * 상품가격(필수) -->
<input type="hidden" name="reserved1"		value="" />													<!-- 상품상품옵션1(선택) -->
<input type="hidden" name="reserved2"		value="" />													<!-- 상품상품옵션2(선택) -->
<input type="hidden" name="reserved3"		value="" />													<!-- 상품상품옵션3(선택) -->
</form>
<script>f_check();</script>
</body>
</html>