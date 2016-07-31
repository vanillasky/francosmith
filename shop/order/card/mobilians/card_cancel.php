<?php

include dirname(__FILE__).'/../../../lib/library.php';

// 관리자 확인
if ($ici_admin !== true) exit;

// 주문번호 확인
if (isset($_GET['ordno']) === false || (int)$_GET['ordno'] < 1) exit;

$cardCancel = Core::loader('cardCancel');
$mobilians = Core::loader('Mobilians');

$ordno = $_GET['ordno'];
$orderData = $db->fetch('SELECT cardtno, settleprice FROM '.GD_ORDER.' WHERE ordno='.$ordno.' LIMIT 1', true);

$gResultcd = $mobilians->paymentCancel($ordno, $orderData['cardtno'], $orderData['settleprice']);

if ($gResultcd === '0000') {
	$cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] 결제취소 : 성공');
}
else if ($gResultcd === '0044') {
	$cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] 결제취소 : 이미 취소처리 되어 주문 업데이트');
}

/*
========================================================================
결과코드              취소 요청 결과 해당 코드 설명
========================================================================
0000 정상처리
0014 해지
0020 고객관리정보 불일치(SKT,LGT의 경우 사용자정보변경에 의한 인증 및 취소 실패)
0041 거래내역 미존재
0042 취소기간경과
0044 중복 취소 요청
0045 취소 요청 시 취소 정보 불일치
0097 요청자료 오류
0098 통신사 통신오류
0099 기타
========================================================================
*/

?>
<html>
	<head>
		<title>mcash 휴대폰취소 결과</title>
	</head>
	<body bgcolor="#FFFFFF" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
		<script type="text/javascript" charset="<?php echo $_CFG['global']['charset']; ?>">
		var resultCode = "<?php echo $gResultcd; ?>";
		switch (resultCode) {
			case "0000" :
				alert("정상적으로 취소되었습니다.");
				parent.location.reload();
				break;
			case "0014" :
				alert("해지된 가맹점 입니다.");
				break;
			case "0020" :
				alert("결제자의 통신사 정보가 변경되어 취소처리가 불가능 합니다.");
				break;
			case "0041" :
				alert("거래내역이 존재하지 않습니다.");
				break;
			case "0042" :
				alert("취소 기간이 경과하여 취소처리가 불가능 합니다.\r\n취소처리는 결제 당월 말일까지만 가능합니다.");
				break;
			case "0044" :
				alert("이미 취소처리 되었습니다.\r\n주문건의 결제정보를 취소상태로 갱신합니다.");
				parent.location.reload();
				break;
			case "0045" :
				alert("요청된 취소정보가 일치하지 않습니다.");
				break;
			case "0097" :
				alert("요청된 정보에 오류가 있습니다.");
				break;
			case "0098" :
				alert("통신사와 통신 오류가 발생하였습니다.\r\n잠시후에 다시 시도하여주시기 바랍니다.");
				break;
			default :
				alert("알수없는 오류입니다.");
				break;
		}
		</script>
	</body>
</html>
