<?php
/**
 * 이니시스 PG 에스크로 배송 등록 처리 페이지
 * 원본 파일명 INIescrow_delivery.php
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

$iniescrow->SetField('escrowtype', 'dlv');					// 고정 (절대 수정 불가)
$iniescrow->SetField('dlv_ip', getenv('REMOTE_ADDR'));		// IP
$iniescrow->SetField('debug','true');						// 로그모드('true'로 설정하면 상세한 로그가 생성됨)

$iniescrow->SetField('oid', $oid);							// 주문번호
$iniescrow->SetField('soid', '1');							// 고정
$iniescrow->SetField('dlv_date', $dlv_date);				// 배송등록 일자
$iniescrow->SetField('dlv_time', $dlv_time);				// 배송등록 시간
$iniescrow->SetField('dlv_report', $EscrowType);			// 에스크로 타입
$iniescrow->SetField('dlv_invoice', $invoice);				// 운송장 번호
$iniescrow->SetField('dlv_name', $dlv_name);				// 배송등록자

$iniescrow->SetField('dlv_excode', $dlv_exCode);			// 택배사코드
$iniescrow->SetField('dlv_exname', $dlv_exName);			// 택배사명
$iniescrow->SetField('dlv_charge', $dlv_charge);			// 배송비 지급방법

$iniescrow->SetField('dlv_invoiceday', $dlv_invoiceday);	// 배송등록 확인일시
$iniescrow->SetField('dlv_sendname', $sendName);			// 송신자 이름
$iniescrow->SetField('dlv_sendpost', $sendPost);			// 송신자 우편번호
$iniescrow->SetField('dlv_sendaddr1', $sendAddr1);			// 송신자 주소1
$iniescrow->SetField('dlv_sendaddr2', $sendAddr2);			// 송신자 주소2
$iniescrow->SetField('dlv_sendtel', $sendTel);				// 송신자 전화번호

$iniescrow->SetField('dlv_recvname', $recvName);			// 수신자 이름
$iniescrow->SetField('dlv_recvpost', $recvPost);			// 수신자 우편번호
$iniescrow->SetField('dlv_recvaddr', $recvAddr);			// 수신자 주소
$iniescrow->SetField('dlv_recvtel', $recvTel);				// 수신자 전화번호

$iniescrow->SetField('dlv_goodscode', $goodsCode);			// 상품코드
$iniescrow->SetField('dlv_goods', $goods);					// 상품명
$iniescrow->SetField('dlv_goodscnt', $goodCnt);				// 상품수량
$iniescrow->SetField('price', $price);						// 상품가격
$iniescrow->SetField('dlv_reserved1', $reserved1);			// 상품상품옵션1
$iniescrow->SetField('dlv_reserved2', $reserved2);			// 상품상품옵션2
$iniescrow->SetField('dlv_reserved3', $reserved3);			// 상품상품옵션3

$iniescrow->SetField('pgn', $pgn);

//--- 배송 등록 요청
$iniescrow->startAction();

//--- 배송 등록 결과
$resultCode	= $iniescrow->GetResult('ResultCode');			// 결과코드 ('00'이면 지불 성공)
$resultMsg	= $iniescrow->GetResult('ResultMsg');			// 결과내용 (지불결과에 대한 설명)
$dlv_date	= $iniescrow->GetResult('DLV_Date');
$dlv_time	= $iniescrow->GetResult('DLV_Time');

//--- 주문번호 처리
$ordno		= $_POST['ordno'];

//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '주문번호 : '.$ordno.chr(10);
$settlelog	.= '거래번호 : '.$iniescrow->GetResult('TID').chr(10);
$settlelog	.= '결과코드 : '.$iniescrow->GetResult('ResultCode').chr(10);
$settlelog	.= '결과내용 : '.$iniescrow->GetResult('ResultMsg').chr(10);
$settlelog	.= '처리날짜 : '.$iniescrow->GetResult('DLV_Date').chr(10);
$settlelog	.= '처리시각 : '.$iniescrow->GetResult('DLV_Time').chr(10);
$settlelog	.= '처리자IP : '.$_SERVER['REMOTE_ADDR'].chr(10);

//--- 승인여부에 따른 처리 설정
if($iniescrow->GetResult('ResultCode') == "00"){

	// PG 결과
	$getPgResult		= true;
	$settlelog	= '===================================================='.chr(10).'에스크로 배송등록 : 처리완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	$settlelog	= '===================================================='.chr(10).'에스크로 배송등록 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG 결과
	$getPgResult		= false;
}

//--- 성공시 디비 처리
if( $getPgResult === true ){
	// 실데이타 저장
	$db->query("
	UPDATE ".GD_ORDER." SET
		escrowconfirm	= 1,
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

msg($resultMsg);
exit;
?>