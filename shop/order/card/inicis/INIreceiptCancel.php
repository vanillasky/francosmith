<?php
/**
 * 이니시스 PG 현금영수증 취소 모듈 처리 페이지
 * 원본 파일명 INIcancel.php
 * 이니시스 PG 버전 : INIpay V5.0 (V 0.1.1 - 20120302)
 */

//--- PG 정보
include dirname(__FILE__).'/../../../conf/pg.inipay.php';

//--- 라이브러리 인클루드
require_once dirname(__FILE__).'/libs/INILib.php';

//--- 주문번호 처리
$ordno	= $crdata['ordno'];

//--- INIpay50 클래스의 인스턴스 생성
$inipay	= new INIpay50;

//--- 취소 정보 설정
$inipay->SetField('inipayhome',		dirname(__FILE__));		// 이니페이 홈디렉터리
$inipay->SetField('type',			'cancel');				// 고정 (절대 수정 불가)
$inipay->SetField('debug',			'true');				// 로그모드('true'로 설정하면 상세로그가 생성됨.)
$inipay->SetField('mid',			$pg['id']);				// 상점아이디
$inipay->SetField('admin',			'1111');				// 비대칭 사용키 키패스워드
$inipay->SetField('tid',			$crdata['tid']);		// 취소할 거래의 거래아이디
$inipay->SetField('cancelmsg',		'관리자취소');			// 취소사유

//--- 취소 요청
$inipay->startAction();
/********************************************************************
* 취소 결과															*
*																	*
* 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)	*
* 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명)	*
* 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)			*
* 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)				*
* 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')	*
* (현금영수증 발급 취소시에만 리턴됨)								*
********************************************************************/

//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '주문번호 : '.$ordno.chr(10);
$settlelog	.= '거래번호 : '.$crdata['tid'].chr(10);
$settlelog	.= '결과코드 : '.$inipay->GetResult('ResultCode').chr(10);
$settlelog	.= '결과내용 : '.$inipay->GetResult('ResultMsg').chr(10);
$settlelog	.= '취소날짜 : '.$inipay->GetResult('CancelDate').chr(10);
$settlelog	.= '취소시각 : '.$inipay->GetResult('CancelTime').chr(10);

//--- 승인여부 / 결제 방법에 따른 처리 설정
if($inipay->GetResult('ResultCode') == "00"){
	// PG 결과
	$getPgResult	= true;

	$settlelog	.= '현금영수증 취소 승인번호 : '.$inipay->GetResult('CSHR_CancelNum').chr(10);
	$settlelog	= '===================================================='.chr(10).'현금영수증 취소 성공 : 취소완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	// PG 결과
	$getPgResult	= false;

	$settlelog	= '===================================================='.chr(10).'현금영수증 취소 실패 : 취소오류시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}

//--- 디비 처리
if( $getPgResult === true )
{
	$db->query("UPDATE ".GD_CASHRECEIPT." SET moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$_GET['crno']."'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
else
{
	$db->query("UPDATE ".GD_CASHRECEIPT." SET errmsg='".$inipay->GetResult('ResultCode').":".$inipay->GetResult('ResultMsg')."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$_GET['crno']."'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
?>