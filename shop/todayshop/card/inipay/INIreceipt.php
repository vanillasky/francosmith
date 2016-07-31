<?php
/**
 * 이니시스 PG 현금영수증 모듈 처리 페이지
 * 원본 파일명 INIreceipt.php
 * 이니시스 PG 버전 : INIpay V5.0 (V 0.1.1 - 20120302)
 * 현금결제(실시간 은행계좌이체, 무통장입금)에 대한 현금결제 영수증 발행 요청한다.
 */

//--- 기존 데이타 처리
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	extract($_POST);

	### 금액 데이타 유효성 체크
	$data = $db->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno='".$ordno."'",1);
	if ($set['receipt']['compType'] == '1'){ // 면세/간이사업자
		$data['supply']	= $data['prn_settleprice'];
		$data['vat']	= 0;
	}
	else { // 과세사업자
		$data['supply']	= round($data['prn_settleprice'] / 1.1);
		$data['vat']	= $data['prn_settleprice'] - $data['supply'];
	}
	if ($data['supply']!=$_POST['sup_price'] || $data['vat']!=$_POST['tax']) msg('금액이 일치하지 않습니다',-1);

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
		list($crno) = $db->fetch("SELECT crno FROM ".GD_CASHRECEIPT." WHERE ordno='".$ordno."' AND status='ACK' ORDER BY crno DESC LIMIT 1");
		if ($crno != '') {
			msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
			exit;
		}

		$indata = array();
		$indata['ordno']		= $_POST['ordno'];
		$indata['goodsnm']		= $_POST['goodname'];
		$indata['buyername']	= $_POST['buyername'];
		$indata['useopt']		= $_POST['useopt'];
		$indata['certno']		= $_POST['reg_num'];
		$indata['amount']		= $_POST['cr_price'];
		$indata['supply']		= $_POST['sup_price'];
		$indata['surtax']		= $_POST['tax'];

		$cashreceipt	= new cashreceipt();
		$crno	= $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno		= $crdata['ordno'];
	$goodname	= $crdata['goodsnm'];
	$cr_price	= $crdata['amount'];
	$sup_price	= $crdata['supply'];
	$tax		= $crdata['surtax'];
	$srvc_price	= 0;
	$buyername	= $crdata['buyername'];
	$buyeremail	= $crdata['buyeremail'];
	$buyertel	= $crdata['buyerphone'];
	$reg_num	= $crdata['certno'];
	$useopt		= $crdata['useopt'];
	$crno		= $_GET['crno'];
}

//--- PG 정보
//include dirname(__FILE__).'/../../../conf/pg.inipay.php';

//--- 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

//--- 라이브러리 인클루드
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 클래스의 인스턴스 생성
$inipay	= new INIpay50;

//--- 발급 정보 설정
$inipay->SetField('inipayhome',		dirname(__FILE__));		// 이니페이 홈디렉터리
$inipay->SetField('type',			'receipt');				// 고정
$inipay->SetField('pgid',			'INIphpRECP');			// 고정
$inipay->SetField('paymethod',		'CASH');				// 고정 (요청분류)
$inipay->SetField('currency',		'WON');					// 화폐단위 (WON 또는 CENT 주의 : 미화승인은 별도 계약이 필요합니다.)
$inipay->SetField('admin',			'1111');				// 키패스워드(상점아이디에 따라 변경)
$inipay->SetField('debug',			'true');				// 로그모드('true'로 설정하면 상세로그가 생성됨.)
$inipay->SetField('mid',			$pg['id']);				// 상점아이디
$inipay->SetField('goodname',		$goodname);				// 상품명
$inipay->SetField('cr_price',		$cr_price);				// 총 현금결제 금액
$inipay->SetField('sup_price',		$sup_price);			// 공급가액
$inipay->SetField('tax',			$tax);					// 부가세
$inipay->SetField('srvc_price',		$srvc_price);			// 봉사료
$inipay->SetField('buyername',		$buyername);			// 구매자 성명
$inipay->SetField('buyeremail',		$buyeremail);			// 구매자 이메일 주소
$inipay->SetField('buyertel',		$buyertel);				// 구매자 전화번호
$inipay->SetField('reg_num',		$reg_num);				// 현금결제자 주민등록번호
$inipay->SetField('useopt',			$useopt);				// 현금영수증 발행용도 ('0' - 소비자 소득공제용, '1' - 사업자 지출증빙용)
$inipay->SetField('companynumber',	$companynumber);

//--- 발급 요청
$inipay->startAction();
/********************************************************************************
 * 발급 결과																	*
 *																				*
 * 결과코드 : $inipay->GetResult('ResultCode') ("00" 이면 발행 성공)			*
 * 승인번호 : $inipay->GetResult('ApplNum') (현금영수증 발행 승인번호)			*
 * 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)							*
 * 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)							*
 * 거래번호 : $inipay->GetResult('TID')											*
 * 총현금결제 금액 : $inipay->GetResult('CSHR_ApplPrice')						*
 * 공급가액 : $inipay->GetResult('CSHR_SupplyPrice')							*
 * 부가세 : $inipay->GetResult('CSHR_Tax')										*
 * 봉사료 : $inipay->GetResult('CSHR_ServicePrice')								*
 * 사용구분 : $inipay->GetResult('CSHR_Type')									*
 ********************************************************************************/

//--- 발행 용도
$arrType	= array('0' => '소득공제용', '1' => '지출증빙용');

//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '주문번호 : '.$ordno.chr(10);
$settlelog	.= '거래번호 : '.$inipay->GetResult('TID').chr(10);
$settlelog	.= '결과코드 : '.$inipay->GetResult('ResultCode').chr(10);
$settlelog	.= '결과내용 : '.$inipay->GetResult('ResultMsg').chr(10);
$settlelog	.= '승인번호 : '.$inipay->GetResult('ApplNum').chr(10);
$settlelog	.= '승인날짜 : '.$inipay->GetResult('ApplDate').chr(10);
$settlelog	.= '승인시간 : '.$inipay->GetResult('ApplTime').chr(10);
$settlelog	.= '승인금액 : '.$inipay->GetResult('CSHR_ApplPrice').chr(10);
$settlelog	.= '공급가액 : '.$inipay->GetResult('CSHR_SupplyPrice').chr(10);
$settlelog	.= '부가세 : '.$inipay->GetResult('CSHR_Tax').chr(10);
$settlelog	.= '봉사료 : '.$inipay->GetResult('CSHR_ServicePrice').chr(10);
$settlelog	.= '사용구분 : '.$inipay->GetResult('CSHR_Type').' - '.$arrType[$inipay->GetResult('CSHR_Type')].chr(10);

//--- 승인여부 / 결제 방법에 따른 처리 설정
if($inipay->GetResult('ResultCode') == "00"){
	// PG 결과
	$getPgResult	= true;

	$settlelog	= '===================================================='.chr(10).'현금영수증 : 승인확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	// PG 결과
	$getPgResult	= false;

	$settlelog	= '===================================================='.chr(10).'현금영수증 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}

//--- 디비 처리
if( $getPgResult === true )
{
	if (empty($crno) === true)
	{
		$db->query("UPDATE ".GD_ORDER." SET cashreceipt='".$inipay->GetResult('TID')."',settlelog=concat(if(settlelog is null,'',settlelog),'".$settlelog."') WHERE ordno='".$ordno."'");
	}
	else {
		# 현금영수증신청내역 수정
		$db->query("UPDATE ".GD_CASHRECEIPT." SET pg='inipay',cashreceipt='".$inipay->GetResult('TID')."',receiptnumber='".$inipay->GetResult('ApplNum')."',tid='".$inipay->GetResult('TID')."',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') where crno='".$crno."'");
		$db->query("UPDATE ".GD_ORDER." SET cashreceipt='".$inipay->GetResult('TID')."' where ordno='".$ordno."'");
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
else
{
	if (empty($crno) === true)
	{
		$db->query("UPDATE ".GD_ORDER." SET settlelog=concat(if(settlelog is null,'',settlelog),'".$settlelog."') WHERE ordno='".$ordno."'");
	}
	else {
		# 현금영수증신청내역 수정
		$db->query("UPDATE ".GD_CASHRECEIPT." SET pg='inipay',errmsg='".$inipay->GetResult('ResultCode').":".$inipay->GetResult('ResultMsg')."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$crno."'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg($inipay->GetResult('ResultMsg'));
		exit;
	}
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}
?>