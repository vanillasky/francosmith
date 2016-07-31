<?php

/* INIreceipt.php
 *
 * 현금결제(실시간 은행계좌이체, 무통장입금)에 대한 현금결제 영수증 발행 요청한다.
 *
 *
 *
 * Date : 2004/12
 * Author : izzylee@inicis.com
 * Project : INIpay V4.11 for Unix
 *
 * http://www.inicis.com
 * http://support.inicis.com
 * Copyright (C) 2002 Inicis, Co. All rights reserved.
 */


/**************************
 * 1. 라이브러리 인클루드 *
 **************************/
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../../lib/library.php';
	include dirname(__FILE__).'/../../../../conf/config.pay.php';
	extract($_POST);

	### 금액 데이타 유효성 체크
	$data = $db->fetch("select * from gd_order where ordno='{$ordno}'",1);

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	@include dirname(__FILE__).'/../../../../lib/cashreceipt.class.php';
	$cashreceipt = new cashreceipt();
	$multitax = $cashreceipt->getCashReceiptCalCulate($ordno);
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
		$indata['goodsnm'] = $_POST['goodname'];
		$indata['buyername'] = $_POST['buyername'];
		$indata['useopt'] = $_POST['useopt'];
		$indata['certno'] = $_POST['reg_num'];
		$indata['amount'] = $multitax['caseReceiptAmount'];
		$indata['supply'] = $multitax['supply'];
		$indata['taxfree'] = $multitax['taxfree'];
		$indata['surtax'] = $multitax['vat'];

		$cr_price = $indata['amount'];
		$sup_price = $indata['supply'];
		$tax = $indata['surtax'];
		$srvc_price = 0;

		$crno = $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno = $crdata['ordno'];
	$goodname = $crdata['goodsnm'];
	$cr_price = $crdata['amount'];
	$sup_price = $crdata['supply'];
	$tax = $crdata['surtax'];
	$srvc_price = 0;
	$buyername = $crdata['buyername'];
	$buyeremail = $crdata['buyeremail'];
	$buyertel = $crdata['buyerphone'];
	$reg_num = $crdata['certno'];
	$useopt = $crdata['useopt'];
	$crno = $_GET['crno'];
}
include dirname(__FILE__).'/../../../../conf/pg.inicis.php';
require_once(dirname(__FILE__).'/INIpay41Lib.php');


/***************************************
 * 2. INIpay41 클래스의 인스턴스 생성 *
 ***************************************/
$inipay = new INIpay41;


/*********************
 * 3. 발급 정보 설정 *
 *********************/
$inipayhome = substr(dirname(__FILE__),0,-7);
$inipay->m_inipayHome = $inipayhome; // 이니페이 홈디렉터리
$inipay->m_type = 'receipt'; // 고정
$inipay->m_pgId = 'INIpayRECP'; // 고정
$inipay->m_payMethod = 'CASH'; // 고정 (요청분류)
$inipay->m_subPgIp = '203.238.3.10'; // 고정
$inipay->m_currency = 'WON'; // 화폐단위 (고정)
$inipay->m_keyPw = '1111'; // 키패스워드(상점아이디에 따라 변경)
$inipay->m_debug = 'true'; // 로그모드('true'로 설정하면 상세로그가 생성됨.)
$inipay->m_mid = $pg['id']; // 상점아이디
$inipay->m_uip = getenv('REMOTE_ADDR'); // 고정
$inipay->m_goodName = $goodname; // 상품명
$inipay->m_cr_price = $cr_price; // 총 현금결제 금액
$inipay->m_sup_price = $sup_price; // 공급가액
$inipay->m_tax = $tax; // 부가세
$inipay->m_srvc_price = $srvc_price; // 봉사료
$inipay->m_buyerName = $buyername; // 구매자 성명
$inipay->m_buyerEmail = $buyeremail; // 구매자 이메일 주소
$inipay->m_buyerTel = $buyertel; // 구매자 전화번호
$inipay->m_reg_num = $reg_num; // 현금결제자 주민등록번호
$inipay->m_useopt = $useopt; // 현금영수증 발행용도 ('0' - 소비자 소득공제용, '1' - 사업자 지출증빙용)

/*----------------------------------------------------------------------------------------*
 * 서브몰 사업자등록번호 *                                                                *
 *----------------------------------------------------------------------------------------*
 * 오픈마켓과 같이 서브몰이 존재하는 경우 반드시 서브몰 사업자등록번호를 입력해야합니다.  *
 * 서브몰 사업자등록번호를 입력하지 않고 현금영수증을 발급하는 경우 상점아이디에 해당하는 *
 * 현금영수증이 발급되어 서브몰 사업자로 현금영수증이 발급되지 않습니다.                  *
 * 상기 사항을 반드시 지켜주시기 바라며, 위 사항을 지키지 않아 발생된 문제에 대해서는     *
 * (주)이니시스에 책임이 없음을 유의하시기 바랍니다.                                      *
 *                                                                                        *
 * 서브몰 현금영수증을 발급하시려면 아래 필드 주석을 제거 하시고 사용하시기 바랍니다.     *
 *----------------------------------------------------------------------------------------*/

 //$inipay->m_companyNumber = '222333444';              // 서브몰 사업자 등록번호


/****************
 * 4. 발급 요청 *
 ****************/
$inipay->startAction();


/********************************************************************************
 * 5. 발급 결과                                                                 *
 *                                                                              *
 * 결과코드 : $inipay->m_rcash_rslt ('0000' 이면 발행 성공)                     *
 * 결과내용 : $inipay->m_resultMsg (발행결과에 대한 설명)                       *
 * 승인번호 : $inipay->m_rcash_noappl (현금영수증 발행 승인번호)                *
 * 승인날짜 : $inipay->m_pgAuthDate (YYYYMMDD)                                  *
 * 승인시각 : $inipay->m_pgAuthTime (HHMMSS)                                    *
 * 거래번호 : $inipay->m_tid                                                    *
 * 총현금결제 금액 : $inipay->rcr_price                                         *
 * 공급가액 : $inipay->m_rsup_price                                             *
 * 부가세 : $inipay->m_rtax                                                     *
 * 봉사료 : $inipay->m_rsrvc_price                                              *
 * 사용구분 : $inipay->m_ruseopt                                                *
 ********************************************************************************/
if( !strcmp($inipay->m_rcash_rslt,'0000') )
{
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 발급 성공'."\n";
	$settlelog .= '결과코드 : '.$inipay->m_rcash_rslt."\n";
	$settlelog .= '결과내용 : '.$inipay->m_resultMsg."\n";
	$settlelog .= '승인번호 : '.$inipay->m_rcash_noappl."\n";
	$settlelog .= '거래번호 : '.$inipay->m_tid."\n";
	$settlelog .= '결제금액 : '.$inipay->rcr_price."\n";
	$settlelog .= '-----------------------------------'."\n";

	if (empty($crno) === true)
	{
		$db->query("update gd_order set cashreceipt='{$inipay->m_tid}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
	}
	else {
		# 현금영수증신청내역 수정
		$db->query("update gd_cashreceipt set pg='inicis',cashreceipt='{$inipay->m_tid}',receiptnumber='{$inipay->m_rcash_noappl}',tid='{$inipay->m_tid}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		$db->query("update gd_order set cashreceipt='{$inipay->m_tid}' where ordno='{$ordno}'");
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
	$settlelog .= '결과코드 : '.$inipay->m_rcash_rslt."\n";
	$settlelog .= '결과내용 : '.$inipay->m_resultMsg."\n";
	$settlelog .= '-----------------------------------'."\n";

	if (empty($crno) === true)
	{
		$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
	}
	else {
		# 현금영수증신청내역 수정
		$db->query("update gd_cashreceipt set pg='inicis',errmsg='{$inipay->m_rcash_rslt}:{$inipay->m_resultMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg($inipay->m_resultMsg);
		exit;
	}
}

?>