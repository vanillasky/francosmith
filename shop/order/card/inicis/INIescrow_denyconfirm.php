<?php
/**
 * 이니시스 PG 에스크로 거절 확인 처리 페이지
 * 원본 파일명 INIescrow_denyconfirm.php
 * 이니시스 PG 버전 : INIpay V5.0 - 오픈웹 (V 0.1.1 - 20120302)
 */

//--- 기본 정보
include "../../../lib/library.php";

//--- 라이브러리 인클루드
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 클래스의 인스턴스 생성
$iniescrow	= new INIpay50;

//--- 지불 정보 설정
$iniescrow->SetField('inipayhome', dirname(__FILE__));		// 이니페이 홈디렉터리
$iniescrow->SetField('type', 'escrow');						// 고정 (절대 수정 불가)
$iniescrow->SetField('tid', $tid);							// 거래아이디
$iniescrow->SetField('mid', $mid);							// 상점아이디
$iniescrow->SetField('admin', '1111');						// 키패스워드(상점아이디에 따라 변경)

$iniescrow->SetField('escrowtype', 'dcnf');					// 고정 (절대 수정 불가)
$iniescrow->SetField('dcnf_name', $dcnf_name);				// 구매거절 확인자
$iniescrow->SetField('debug','true');						// 로그모드('true'로 설정하면 상세한 로그가 생성됨)

//--- 거절 확인 요청
$iniescrow->startAction();

//--- 거절 확인 결과
$tid		= $iniescrow->GetResult('tid');					// 거래번호
$resultCode	= $iniescrow->GetResult('ResultCode');			// 결과코드 ('00'이면 지불 성공)
$resultMsg	= $iniescrow->GetResult('ResultMsg');			// 결과내용 (지불결과에 대한 설명)
$resultDate	= $iniescrow->GetResult('DCNF_Date');			// 처리 날짜
$resultTime	= $iniescrow->GetResult('DCNF_Time');			// 처리 시간

//--- 주문번호 처리
$ordno		= $_POST['ordno'];

//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '주문번호 : '.$ordno.chr(10);
$settlelog	.= '거래번호 : '.$iniescrow->GetResult('TID').chr(10);
$settlelog	.= '결과코드 : '.$iniescrow->GetResult('ResultCode').chr(10);
$settlelog	.= '결과내용 : '.$iniescrow->GetResult('ResultMsg').chr(10);
$settlelog	.= '처리날짜 : '.$iniescrow->GetResult('DCNF_Date').chr(10);
$settlelog	.= '처리시각 : '.$iniescrow->GetResult('DCNF_Time').chr(10);
$settlelog	.= '처리자IP : '.$_SERVER['REMOTE_ADDR'].chr(10);

//--- 승인여부에 따른 처리 설정
if($iniescrow->GetResult('ResultCode') == "00"){

	// PG 결과
	$getPgResult		= true;
	$settlelog	= '===================================================='.chr(10).'에스크로 거절 확인 : 처리완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	$settlelog	= '===================================================='.chr(10).'에스크로 거절 확인 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG 결과
	$getPgResult		= false;
}

//--- 성공시 디비 처리
if( $getPgResult === true ){
	// 실데이타 저장
	$db->query("
	UPDATE ".GD_ORDER." SET
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$ordno'"
	);
} else {
	// 실데이타 저장
	$db->query("
	UPDATE ".GD_ORDER." SET
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$ordno'"
	);
}

msg($resultMsg,'close');
exit;
?>