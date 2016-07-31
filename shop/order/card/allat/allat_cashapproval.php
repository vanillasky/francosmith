<?

if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	@include_once(dirname(__FILE__).'/../../../lib/cashreceipt.class.php');

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$_POST = validation::xssCleanArray($_POST, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	$ordno = $_POST['ordno'];

	if(!is_object($cashreceipt) && class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

	### 금액 데이타 유효성 체크
	$data = $cashreceipt->getCashReceiptCalCulate($ordno);
	if ($data['supply'] != $_POST['allat_supply_amt'] || $data['vat'] != $_POST['allat_vat_amt']) msg('금액이 일치하지 않습니다',-1);

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	if (is_object($cashreceipt))
	{
		// 발급상태체크
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
			exit;
		}

		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $_POST['allat_product_nm'];
		$indata['buyername'] = $_POST['allat_shop_member_id'];
		$indata['useopt'] = $_POST['useopt'];
		$indata['certno'] = $_POST['allat_cert_no'];
		$indata['amount'] = $_POST['allat_supply_amt'] + $_POST['allat_vat_amt'];
		$indata['supply'] = $_POST['allat_supply_amt'];
		$indata['surtax'] = $_POST['allat_vat_amt'];

		$crno = $cashreceipt->putReceipt($indata);
	}

	// 추가 정보
	$data['buyername']	= $_POST['allat_shop_member_id'];
	$data['certno']		= $_POST['allat_cert_no'];
	$data['goodsnm']	= $_POST['allat_product_nm'];
	$data['type']		= $_POST['allat_receipt_type'];
	$data['tno']		= $_POST['allat_seq_no'];
}
else {
	$ordno				= $crdata['ordno'];
	$data['supply']		= $crdata['supply'];
	$data['vat']		= $crdata['surtax'];
	$crno				= $_GET['crno'];

	// 추가 정보
	$data['buyername']	= $crdata['buyername'];
	$data['certno']		= $crdata['certno'];
	$data['goodsnm']	= $crdata['goodsnm'];
	$data['type']		= $type;
	$data['tno']		= $tno;
}
include dirname(__FILE__).'/../../../conf/pg.allat.php';
include_once dirname(__FILE__).'/allatutil.php';

// Set Value
// -------------------------------------------------------------------
$at_shop_id			= $pg['id'];
$at_cross_key		= $pg['crosskey'];
$at_supply_amt		= $data['supply'];			// 공급가액, 금액을 다시 계산해서 만들어야 함(해킹방지)
$at_vat_amt			= $data['vat'];				// VAT금액
$at_apply_ymdhms    = date('YmdHis');			// 거래요청일자(최대 14Byte)
$at_shop_member_id  = $data['buyername'];		// 쇼핑몰의 회원ID(최대 20Byte)
$at_cert_no         = $data['certno'];			// 인증정보(최대 13Byte) : 핸드폰번호,주민번호,사업자번호
$at_product_nm      = $data['goodsnm'];			// 상품명(최대 100Byte)
$at_receipt_type    = $data['type'];			// 현금영수증구분(최대 6Byte):계좌이체(ABANK),무통장(NBANK)
$at_seq_no          = $data['tno'];				// 거래일련번호(최대 10Byte)
$at_reg_business_no	= '';						// 등록할사업자번호(최대 10Byte):상점 ID와 다른경우
$at_buyer_ip		= $_SERVER['REMOTE_ADDR'];	// 신청자 IP
$at_test_yn			= 'N';						// TEST 여부
$at_opt_pin         = 'NOUSE';
$at_opt_mod         = 'APP';

// set Enc Data
// -------------------------------------------------------------------
$at_enc_data	= setValue($at_enc_data,"allat_shop_id",$at_shop_id);
$at_enc_data	= setValue($at_enc_data,"allat_apply_ymdhms",$at_apply_ymdhms);
$at_enc_data	= setValue($at_enc_data,"allat_shop_member_id",$at_shop_member_id);
$at_enc_data	= setValue($at_enc_data,"allat_cert_no",$at_cert_no);
$at_enc_data	= setValue($at_enc_data,"allat_supply_amt",$at_supply_amt);
$at_enc_data	= setValue($at_enc_data,"allat_vat_amt",$at_vat_amt);
$at_enc_data	= setValue($at_enc_data,"allat_product_nm",$at_product_nm);
$at_enc_data	= setValue($at_enc_data,"allat_receipt_type",$at_receipt_type);
$at_enc_data	= setValue($at_enc_data,"allat_seq_no",$at_seq_no);
$at_enc_data	= setValue($at_enc_data,"allat_reg_business_no",$at_reg_business_no);
$at_enc_data	= setValue($at_enc_data,"allat_buyer_ip",$at_buyer_ip);
$at_enc_data	= setValue($at_enc_data,"allat_opt_pin",$at_opt_pin);
$at_enc_data	= setValue($at_enc_data,"allat_opt_mod",$at_opt_mod);

// 요청 데이터 설정
//----------------------
$at_data   = "allat_shop_id=".$at_shop_id.
             "&allat_enc_data=".$at_enc_data.
             "&allat_cross_key=".$at_cross_key;

// 올앳 결제 서버와 통신 : CashAppReq->통신함수, $at_txt->결과값
//----------------------------------------------------------------
$at_txt = CashAppReq($at_data,'SSL');    //설정 필요 https(SSL),http(NOSSL)

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
	$APPROVAL_NO = trim(getValue('approval_no',$at_txt));
	$CASH_BILL_NO = trim(getValue('cash_bill_no',$at_txt));

	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '----------------------------------'."\n";
	$settlelog .= '현금영수증 발급 성공'."\n";
	$settlelog .= '결과코드 : '.$REPLYCD."\n";
	$settlelog .= '결과내용 : '.$REPLYMSG."\n";
	$settlelog .= '승인번호 : '.$APPROVAL_NO."\n";
	$settlelog .= '일련번호 : '.$CASH_BILL_NO."\n";
	$settlelog .= '-----------------------------------'."\n";

	if (empty($crno) === true)
	{
		$db->query("update gd_order set cashreceipt='{$CASH_BILL_NO}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
	}
	else {
		# 현금영수증신청내역 수정
		$db->query("update gd_cashreceipt set pg='allat',cashreceipt='{$CASH_BILL_NO}',receiptnumber='{$APPROVAL_NO}',tid='{$CASH_BILL_NO}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		$db->query("update gd_order set cashreceipt='{$CASH_BILL_NO}' where ordno='{$ordno}'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg('현금영수증이 정상발급되었습니다');
		echo '<script>parent.location.reload();</script>';
	}
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 발급 실패'."\n";
	$settlelog .= '결과코드 : '.$REPLYCD."\n";
	$settlelog .= '결과내용 : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";

	if (empty($crno) === true)
	{
		$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n$settlelog') where ordno='$ordno'");
	}
	else {
		# 현금영수증신청내역 수정
		$db->query("update gd_cashreceipt set pg='allat',errmsg='{$REPLYCD}:{$REPLYMSG}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg("$REPLYMSG");
		exit;
	}
}

?>