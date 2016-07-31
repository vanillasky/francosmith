<?php

/* INIcancel.php
 *
 * 이미 승인된 지불을 취소한다.
 * 은행계좌 이체 , 무통장입금은 이 모듈을 통해 취소 불가능.
 *  [은행계좌이체는 상점정산 조회페이지 (https://iniweb.inicis.com)를 통해 취소 환불 가능하며, 무통장입금은 취소 기능이 없습니다.]
 *
 * Date : 2006/04
 * Author : ts@inicis.com
 * Project : INIpay V4.11 for Unix
 *
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis, Co. All rights reserved.
 */


/**************************
 * 1. 라이브러리 인클루드 *
 **************************/
include dirname(__FILE__).'/../../../../conf/pg.inicis.php';
require(dirname(__FILE__).'/INIpay41Lib.php');
$ordno = $crdata['ordno'];


/***************************************
 * 2. INIpay41 클래스의 인스턴스 생성 *
 ***************************************/
$inipay = new INIpay41;


/*********************
 * 3. 취소 정보 설정 *
 *********************/
$inipayhome = substr(dirname(__FILE__),0,-7);
$inipay->m_inipayHome = $inipayhome; // 이니페이 홈디렉터리
$inipay->m_type = 'cancel'; // 고정
$inipay->m_pgId = 'INIpayRECP'; // 고정
$inipay->m_subPgIp = '203.238.3.10'; // 고정
$inipay->m_keyPw = '1111'; // 키패스워드(상점아이디에 따라 변경)
$inipay->m_debug = 'true'; // 로그모드('true'로 설정하면 상세로그가 생성됨.)
$inipay->m_mid = $pg['id']; // 상점아이디
$inipay->m_tid = $crdata['tid']; // 취소할 거래의 거래아이디
$inipay->m_cancelMsg = ''; // 취소사유
$inipay->m_uip = getenv('REMOTE_ADDR'); // 고정


/****************
 * 4. 취소 요청 *
 ****************/
$inipay->startAction();


/****************************************************************
 * 5. 취소 결과                                           	*
 *                                                        	*
 * 결과코드 : $inipay->m_resultCode ('00'이면 취소 성공)  	*
 * 결과내용 : $inipay->m_resultMsg (취소결과에 대한 설명) 	*
 * 취소날짜 : $inipay->m_pgCancelDate (YYYYMMDD)          	*
 * 취소시각 : $inipay->m_pgCancelTime (HHMMSS)            	*
 * 현금영수증 취소 승인번호 : $inipay->m_rcash_cancel_noappl    *
 * (현금영수증 발급 취소시에만 리턴됨)                          *
 ****************************************************************/
if( !strcmp($inipay->m_resultCode,'00') )
{
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 취소 성공'."\n";
	$settlelog .= '결과코드 : '.$inipay->m_resultCode."\n";
	$settlelog .= '결과내용 : '.$inipay->m_resultMsg."\n";
	$settlelog .= '취소일시 : '.$inipay->m_pgCancelDate.' '.$inipay->m_pgCancelTime."\n";
	$settlelog .= '취소 승인번호 : '.$inipay->m_rcash_cancel_noappl."\n";
	$settlelog .= '-----------------------------------'."\n";

	$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '현금영수증 취소 실패'."\n";
	$settlelog .= '결과코드 : '.$inipay->m_resultCode."\n";
	$settlelog .= '결과내용 : '.$inipay->m_resultMsg."\n";
	$settlelog .= '-----------------------------------'."\n";

	$db->query("update gd_cashreceipt set errmsg='{$inipay->m_resultCode}:{$inipay->m_resultMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
}
?>