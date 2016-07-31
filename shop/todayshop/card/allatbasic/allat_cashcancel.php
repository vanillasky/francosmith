<?

// 올앳관련 함수 Include
//include dirname(__FILE__).'/../../../conf/pg.allat.php';
// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();
include dirname(__FILE__).'/allatutil.php';

$ordno = $crdata['ordno'];

$at_shop_id		= $pg['id'];
$at_cross_key	= $pg['crosskey'];

// 요청 데이터 설정
//----------------------
$at_data   = 'allat_shop_id='.$at_shop_id.
			'&allat_enc_data='.$_POST['allat_enc_data'].
			'&allat_cross_key='.$at_cross_key;

// 올앳 결제 서버와 통신 : SendApproval->통신함수, $at_txt->결과값
//----------------------------------------------------------------
$at_txt = CashCanReq($at_data,'SSL');

// 결제 결과 값 확인
//------------------
$REPLYCD   =getValue('reply_cd',$at_txt);
$REPLYMSG  =getValue('reply_msg',$at_txt);

// 결과값 처리
//--------------------------------------------------------------------------
// 결과 값이 '0000'이면 정상임. 단, allat_test_yn=Y 일경우 '0001'이 정상임.
// 실제 결제   : allat_test_yn=N 일 경우 reply_cd=0000 이면 정상
// 테스트 결제 : allat_test_yn=Y 일 경우 reply_cd=0001 이면 정상
//--------------------------------------------------------------------------
if( !strcmp($REPLYCD,'0000') )
{
	$CANCEL_YMDHMS = getValue('cancel_ymdhms',$at_txt);
	$PART_CANCEL_FLAG = getValue('part_cancel_flag',$at_txt);
	$REMAIN_AMT = getValue('remain_amt',$at_txt);

	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 취소 성공'."\n";
	$settlelog .= '결과코드 : '.$REPLYCD."\n";
	$settlelog .= '결과내용 : '.$REPLYMSG."\n";
	$settlelog .= '취소일시 : '.$CANCEL_YMDHMS."\n";
	$settlelog .= '취소여부 : '.$PART_CANCEL_FLAG."\n";
	$settlelog .= '잔액 : '.$REMAIN_AMT."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 취소 실패'."\n";
	$settlelog .= '결과코드 : '.$REPLYCD."\n";
	$settlelog .= '결과내용 : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	$db->query("update gd_cashreceipt set errmsg='{$REPLYCD}:{$REPLYMSG}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}

?>