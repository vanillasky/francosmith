<?php
/**
 * 이니시스 PG 에스크로 고객 구매 확정(확인 또는 거절) 페이지
 * 원본 파일명 INIescrow_confirm.php
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

$iniescrow->SetField('escrowtype', 'confirm');				// 고정 (절대 수정 불가)
$iniescrow->SetField('dlv_ip', getenv('REMOTE_ADDR'));		// IP
$iniescrow->SetField('debug','true');						// 로그모드('true'로 설정하면 상세한 로그가 생성됨)

$iniescrow->SetField('encrypted', $encrypted);				// 고정
$iniescrow->SetField('sessionkey', $sessionkey);			// 고정

//--- 구매 확인 요청
$iniescrow->startAction();

//--- 구매 확인 결과
$tid			= $iniescrow->GetResult('tid');				// 거래번호
$resultCode		= $iniescrow->GetResult('ResultCode');		// 결과코드 ('00'이면 지불 성공)
$resultMsg		= $iniescrow->GetResult('ResultMsg');		// 결과내용 (지불결과에 대한 설명)
$resultDate		= $iniescrow->GetResult('CNF_Date');		// 처리 날짜
$resultTime		= $iniescrow->GetResult('CNF_Time');		// 처리 시각

// 거절인 경우
if ($iniescrow->GetResult('CNF_Date') == '') {
	$resultDate	= $iniescrow->GetResult('DNY_Date');		// 처리 날짜
	$resultTime	= $iniescrow->GetResult('DNY_Time');		// 처리 시각
	$confirmFl	= 'reject';
	$resultMsg	= '에스크로 거절이 신청되었습니다.';
} else {
	$confirmFl	= 'accept';
	$resultMsg	= '에스크로 구매확인이 완료 되었습니다.';
}

//--- 주문번호 처리
$ordno		= $_POST['ordno'];

//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '주문번호 : '.$ordno.chr(10);
$settlelog	.= '결과코드 : '.$iniescrow->GetResult('ResultCode').chr(10);
$settlelog	.= '결과내용 : '.$iniescrow->GetResult('ResultMsg').chr(10);
if ($confirmFl == 'accept') {
	$settlelog	.= '구매확정날짜 : '.$resultDate.chr(10);
	$settlelog	.= '구매확정시각 : '.$resultTime.chr(10);
} else {
	$settlelog	.= '구매거절날짜 : '.$resultDate.chr(10);
	$settlelog	.= '구매거절시각 : '.$resultTime.chr(10);
}
$settlelog	.= 'IP : '.$_SERVER['REMOTE_ADDR'].chr(10);

//--- 결과에 따른 처리 설정
if($iniescrow->GetResult('ResultCode') == "00"){

	// PG 결과
	$getPgResult		= true;

	$settlelog	= '===================================================='.chr(10).'에스크로 구매 확인 : 처리완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	$settlelog	= '===================================================='.chr(10).'에스크로 구매 확인 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG 결과
	$getPgResult		= true;
	$resultMsg			= '에스크로 구매확인에 오류가 있습니다.';
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
