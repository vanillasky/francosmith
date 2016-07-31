<?

if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';

	$ordno = $_POST['ordno'];

	### 금액 데이타 유효성 체크
	$data = $db->fetch("select * from gd_order where ordno='$ordno'",1);
	if ($set['receipt']['compType'] == '1'){ // 면세/간이사업자
		$data['supply'] = $data['prn_settleprice'];
		$data['vat'] = 0;
	}
	else { // 과세사업자
		$data['supply'] = round($data['prn_settleprice'] / 1.1);
		$data['vat'] = $data['prn_settleprice'] - $data['supply'];
	}
	if ($data['supply'] != $_POST['allat_supply_amt'] || $data['vat'] != $_POST['allat_vat_amt']) msg('금액이 일치하지 않습니다',-1);

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	@include dirname(__FILE__).'/../../../lib/cashreceipt.class.php';
	if (class_exists('cashreceipt'))
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

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno = $crdata['ordno'];
	$data['supply'] = $crdata['supply'];
	$data['vat'] = $crdata['surtax'];
	$crno = $_GET['crno'];
}
//include dirname(__FILE__).'/../../../conf/pg.allat.php';
// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();
include dirname(__FILE__).'/allatutil.php';


$at_shop_id		= $pg['id'];
$at_cross_key	= $pg['crosskey'];
$at_supply_amt	= $data['supply']; //금액을 다시 계산해서 만들어야 함(해킹방지)
$at_vat_amt		= $data['vat'];

// 요청 데이터 설정
//----------------------
$at_data   = 'allat_shop_id='.$at_shop_id .
			 '&allat_supply_amt='.$at_supply_amt.
			 '&allat_vat_amt='.$at_vat_amt.
			 '&allat_enc_data='.$_POST['allat_enc_data'].
			 '&allat_cross_key='.$at_cross_key;

// 올앳 결제 서버와 통신 : CashAppReq->통신함수, $at_txt->결과값
//----------------------------------------------------------------
$at_txt = CashAppReq($at_data,$pg['ssl']);    //설정 필요 https(SSL),http(NOSSL)

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
	echo nl2br($settlelog);

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
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 발급 실패'."\n";
	$settlelog .= '결과코드 : '.$REPLYCD."\n";
	$settlelog .= '결과내용 : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

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
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}

?>