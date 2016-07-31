<?php
/**
 * 이니시스 PG 모듈 페이지
 * 원본 파일명 INIsecurepaystart.php
 * 이니시스 PG 버전 : INIpay V5.0 - 오픈웹 (V 0.1.1 - 20120302)
 */

include "../conf/pg.inipay.php";
@include "../conf/pg.escrow.php";

//--- 에스크로 결제시 pgId 변경
if ($_POST['escrow'] == "Y") {
	$pg['id']	= $escrow['id'];
}

//--- 라이브러리 인클루드
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 클래스의 인스턴스 생성
$inipay = new INIpay50;

//--- 무이자 설정
if ($pg['zerofee'] == 'yes') {
 $quotabase  = $pg['quota'].'('.$pg['zerofee_period'].')';
} else {
 $quotabase  = $pg['quota'];
}

//--- 암호화 대상/값 설정
$inipay->SetField('inipayhome', dirname(__FILE__));		// 이니페이 홈디렉터리
$inipay->SetField('type', 'chkfake');					// 고정 (절대 수정 불가)
$inipay->SetField('debug', 'true');						// 로그모드('true'로 설정하면 상세로그가 생성됨.)
$inipay->SetField('enctype', 'asym');					// asym:비대칭, symm:대칭(현재 asym으로 고정)
$inipay->SetField('admin', '1111');						// 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
$inipay->SetField('checkopt', 'false');					// base64함:false, base64안함:true(현재 false로 고정)
$inipay->SetField('mid', $pg['id']);					// 상점아이디
$inipay->SetField('price', $_POST['settleprice']);		// 가격
$inipay->SetField('nointerest', $pg['zerofee']);		// 무이자여부(no:일반, yes:무이자)
$inipay->SetField('quotabase', $quotabase);			// 할부기간

// --- 암호화 대상/값을 암호화함
$inipay->startAction();

//--- 암호화 결과
if ($inipay->GetResult('ResultCode') != '00'){
	msg($inipay->GetResult('ResultMsg'));
	exit();
}

//--- 세션정보 저장
$_SESSION['INI_MID']		= $pg['id'];						// 상점ID
$_SESSION['INI_ADMIN']		= '1111';							// 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
$_SESSION['INI_PRICE']		= $_POST['settleprice'];			// 가격
$_SESSION['INI_RN']			= $inipay->GetResult('rn');			// 고정 (절대 수정 불가)
$_SESSION['INI_ENCTYPE']	= $inipay->GetResult('enctype');	// 고정 (절대 수정 불가)

//--- 결제 수단 설정
$tmpSettleCode	= array(
	'c'		=> 'onlycard',
	'o'		=> 'onlydbank',
	'v'		=> 'onlyvbank',
	'h'		=> 'onlyhpp',
	'y'		=> 'onlyypay',
);
$settlekindCode	= $tmpSettleCode[$_POST['settlekind']];

//--- 상품명 설정
if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
	$item = $cart -> item;
}
foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = str_replace("`", "'", $v[goodsnm]);
}
if($i > 1)$ordnm .= " 외".($i-1)."건";
$ordnm	= pg_text_replace(strip_tags($ordnm));

//--- 이니시스 코드 assign
$tpl->assign('INIConfEncfield',$inipay->GetResult("encfield"));
$tpl->assign('INIConfCertid',$inipay->GetResult("certid"));
?>