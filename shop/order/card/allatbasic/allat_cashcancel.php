<?

// 올앳관련 함수 Include
include dirname(__FILE__).'/../../../conf/pg.allatbasic.php';
include_once dirname(__FILE__).'/allatutil.php';

$ordno = $crdata['ordno'];

// Set Value
// -------------------------------------------------------------------
$at_cross_key		= $pg['crosskey'];
$at_shop_id         = $pg['id'];			// ShopId값(최대 20Byte)
$at_cash_bill_no    = $crdata['tid'];		// 현금영수증일련번호(최대 10Byte)
$at_supply_amt      = $crdata['supply'];	// 취소공급가액(최대 10Byte)
$at_vat_amt         = $crdata['surtax'];	// 취소VAT금액(최대 10Byte)
$at_reg_business_no = '';					// 등록할사업자번호(최대 10Byte):상점 ID와 다른경우
$at_opt_pin         = "NOUSE";
$at_opt_mod         = "APP";

// set Enc Data
// -------------------------------------------------------------------
$at_enc_data		= setValue($at_enc_data,"allat_shop_id",$at_shop_id);
$at_enc_data		= setValue($at_enc_data,"allat_cash_bill_no",$at_cash_bill_no);
$at_enc_data		= setValue($at_enc_data,"allat_supply_amt",$at_supply_amt);
$at_enc_data		= setValue($at_enc_data,"allat_vat_amt",$at_vat_amt);
$at_enc_data		= setValue($at_enc_data,"allat_reg_business_no",$at_reg_business_no);
$at_enc_data		= setValue($at_enc_data,"allat_opt_pin",$at_opt_pin);
$at_enc_data		= setValue($at_enc_data,"allat_opt_mod",$at_opt_mod);

// Set Request Data
//---------------------------------------------------------------------
$at_data   = "allat_shop_id=".$at_shop_id.
             "&allat_enc_data=".$at_enc_data.
             "&allat_cross_key=".$at_cross_key;

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

	$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 취소 실패'."\n";
	$settlelog .= '결과코드 : '.$REPLYCD."\n";
	$settlelog .= '결과내용 : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";

	$db->query("update gd_cashreceipt set errmsg='{$REPLYCD}:{$REPLYMSG}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
}

?>