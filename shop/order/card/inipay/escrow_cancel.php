<?php
/**
 * 이니시스 PG 에스크로 거절 확인 페이지
 * 원본 파일명 INIescrow_denyconfirm.html
 * 이니시스 PG 버전 : INIpay V5.0 - 오픈웹 (V 0.1.1 - 20120302)
 */

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET['ordno'];

$query = "
SELECT
	escrowno
FROM
	".GD_ORDER."
WHERE
	ordno = '$ordno'
";
$data = $db->fetch($query);
?>
<html>
<head>
<title>이니시스 자체 에스크로(INIescrow) 거절확인</title>
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
	else if(document.ini.dcnf_name.value == ""){
		alert("거절 확인자 이름을 선택하십시요.")
		return;
	}
	document.ini.submit();
}
</script>
</head>

<body>
<form name="ini" method="post" action="./INIescrow_denyconfirm.php">
<input type="hidden" name="ordno"			value="<?php echo $ordno;?>" />								<!-- 주문 번호 - PG 처리와는 전혀 상관이 없는 옵션임 -->
<input type="hidden" name="mid"				value="<?php echo $escrow['id'];?>" />						<!-- * 에스크로 아이디 -->
<input type="hidden" name="tid"				value="<?php echo $data['escrowno'];?>" />					<!-- * 상품구매 거래번호(TID) -->
<input type="hidden" name="dcnf_name"		value="관리자" />											<!-- * 구매거절 확인자 -->
</form>
<script>f_check();</script>
</body>
</html>