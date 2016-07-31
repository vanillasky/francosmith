<?

include "../_header.php";

if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");

include '../lib/cashreceipt.class.php';
$cashreceipt = new cashreceipt();
if ($_GET['ordno']){
	$url = $cashreceipt->getReceipturl($_GET['ordno'], 'ordno');
}
else {
	$url = $cashreceipt->getReceipturl($_GET['crno']);
}

if ($url){
	header('Location: '.$url);
	exit;
}
?>
<html>
<head>
<title>현 금 영 수 증</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
</head>
<body oncontextmenu="return false">
<div style="margin:0 40 20 40; font-size:9pt;">
본 사이트에서는 현금영수증 출력이 지원되지 않습니다.<br>
국세청 현금영수증(taxsave.go.kr)에서 확인하세요.
</div>
</body>
</html>