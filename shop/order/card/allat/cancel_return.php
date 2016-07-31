<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.allat.php";
include "../../../lib/cardCancel.class.php";

// 올앳관련 함수 Include
//----------------------
include "./allatutil.php";

$ordno = $_POST['allat_order_no'];
$a_sno = $_POST['sno'];
$a_price = $_POST['price'];
$a_repay = $_POST['repay'];

// 요청 데이터 설정
//----------------------
$at_data	= "allat_shop_id=".urlencode($pg['id'])."&allat_enc_data=".$_POST['allat_enc_data']."&allat_cross_key=".$pg['crosskey'];

// 올앳 결제 서버와 통신 : CancelReq->통신함수, $at_txt->결과값
//----------------------------------------------------------------
$at_txt = CancelReq($at_data,$pg[ssl]);

// 결제 결과 값 확인
//------------------
$REPLYCD   =getValue("reply_cd",$at_txt);
$REPLYMSG  =getValue("reply_msg",$at_txt);

// 결과 값이 '0000'이면 정상임. 단, allat_test_yn=Y 일경우 '0001'이 정상임.
// 실제 취소   : allat_test_yn=N 일 경우 reply_cd=0000 이면 정상
// 테스트 취소 : allat_test_yn=Y 일 경우 reply_cd=0001 이면 정상
//----------------------------------------------------------------------------------------
if( !strcmp($REPLYCD,"0000") ){
	// reply_cd "0000" 일때만 성공
	$CANCEL_YMDHMS=getValue("cancel_ymdhms",$at_txt);
	$PART_CANCEL_FLAG=getValue("part_cancel_flag",$at_txt);
	$REMAIN_AMT=getValue("remain_amt",$at_txt);
	$PAY_TYPE=getValue("pay_type",$at_txt);

	$log .= "결과코드    : ".$REPLYCD."\n";
	$log .= "결과메세지  : ".$REPLYMSG."\n";
	$log .= "취소날짜    : ".$CANCEL_YMDHMS."\n";
	$log .= "취소구분    : ".$PART_CANCEL_FLAG."\n"; //신용카드 : 취소(0),부분취소(1),  계좌이체: 취소(0), 환불(2),부분환불(3)
	$log .= "잔액        : ".$REMAIN_AMT."\n";
	$log .= "거래방식구분: ".$PAY_TYPE."\n";

	$cancel = new cardCancel();
	if($_POST['actmode'] == 1){
		if($PART_CANCEL_FLAG == 1){
			$cancel -> partcancel_allat_return($ordno,$a_sno,$settlelog,$a_price, $a_repay);
			msg('결제승인 부분취소완료');
			echo("<script>parent.location.reload();</script>");
		}else{
			$cancel -> cancel_proc($ordno,'관리자 승인취소');
			msg('결제승인취소완료');
			echo("<script>parent.location.reload();</script>");
		}
	}else{
		$cancel -> cancel_db_proc($ordno);
		go("../../order_fail.php?ordno=$ordno","parent");
	}

}else{
	$log .= "결과코드    : ".$REPLYCD."\n";
	$log .= "결과메세지  : ".$REPLYMSG."\n";
	msg('거래취소 실패 관리자에게 문의 하십시요!');
	echo '<div style="font:9pt verdana;">'.nl2br($log).'</div>';
}
?>