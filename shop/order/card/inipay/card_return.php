<?php
/**
 * 이니시스 PG 모듈 처리 페이지
 * 원본 파일명 INIsecurepay.php
 * 이니시스 PG 버전 : INIpay V5.0 - 오픈웹 (V 0.1.1 - 20120302)
 */

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.inicis.php";

//--- 에러 리포트 수정
error_reporting(E_ALL ^ E_NOTICE);

//--- PG결제 위변조 체크 및 유효성 체크
if (forge_order_check($_POST['ordno'],$_SESSION['INI_PRICE']) === false) {
	msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.','../../order_fail.php?ordno='.$_POST['ordno'],'parent');
	exit();
}

// Ncash 결제 승인 API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($_POST['paymethod']=="VBank") $ncashResult = $naverNcash->payment_approval($_POST['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['ordno'], true);
	if($ncashResult===false)
	{
		msg('네이버 마일리지 사용에 실패하였습니다.', '../../order_fail.php?ordno='.$_POST['ordno'],'parent');
		exit();
	}
}

//--- 라이브러리 인클루드
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 클래스의 인스턴스 생성
$inipay	= new INIpay50;

//--- 지불 정보 설정
$inipay->SetField('inipayhome', dirname(__FILE__));					// 이니페이 홈디렉터리
$inipay->SetField('type', 'securepay');								// 고정 (절대 수정 불가)
$inipay->SetField('pgid', 'INIphp'.$pgid);							// 고정 (절대 수정 불가)
$inipay->SetField('subpgip', '203.238.3.10');						// 고정 (절대 수정 불가)
$inipay->SetField('admin', $_SESSION['INI_ADMIN']);					// 키패스워드(상점아이디에 따라 변경)
$inipay->SetField('debug', 'true');									// 로그모드('true'로 설정하면 상세로그가 생성됨.)
$inipay->SetField('uid', $uid);										// INIpay User ID (절대 수정 불가)
$inipay->SetField('goodname', $goodname);							// 상품명
$inipay->SetField('currency', $currency);							// 화폐단위

$inipay->SetField('mid', $_SESSION['INI_MID']);						// 상점아이디
$inipay->SetField('rn', $_SESSION['INI_RN']);						// 웹페이지 위변조용 RN값
$inipay->SetField('price', $_SESSION['INI_PRICE']);					// 가격
$inipay->SetField('tax', $_SESSION['INI_TAX']);						// 부가세
$inipay->SetField('taxfree', $_SESSION['INI_TAXFREE']);				// 면세
$inipay->SetField('enctype', $_SESSION['INI_ENCTYPE']);				// 고정 (절대 수정 불가)

$inipay->SetField('buyername', $buyername);							// 구매자 명
$inipay->SetField('buyertel', $buyertel);							// 구매자 연락처(휴대폰 번호 또는 유선전화번호)
$inipay->SetField('buyeremail', $buyeremail);						// 구매자 이메일 주소
$inipay->SetField('paymethod', $paymethod);							// 지불방법 (절대 수정 불가)
$inipay->SetField('encrypted', $encrypted);							// 암호문
$inipay->SetField('sessionkey', $sessionkey);						// 암호문
$inipay->SetField('url', "http://".$_SERVER[SERVER_NAME]);			// 실제 서비스되는 상점 SITE URL로 변경할것
$inipay->SetField('cardcode', $cardcode);							// 카드코드 리턴
$inipay->SetField('parentemail', $parentemail);						// 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)

$inipay->SetField('recvname', $recvname);							// 수취인 명
$inipay->SetField('recvtel', $recvtel);								// 수취인 연락처
$inipay->SetField('recvaddr', $recvaddr);							// 수취인 주소
$inipay->SetField('recvpostnum', $recvpostnum);						// 수취인 우편번호
$inipay->SetField('recvmsg', $recvmsg);								// 전달 메세지

$inipay->SetField('joincard', $joincard);							// 제휴카드코드
$inipay->SetField('joinexpire', $joinexpire);						// 제휴카드유효기간
$inipay->SetField('id_customer', $id_customer);						// user_id

//--- 지불 요청
$inipay->startAction();
/****************************************************************************************************************
* 결제  결과
*
*  1 모든 결제 수단에 공통되는 결제 결과 데이터
* 	거래번호 : $inipay->GetResult('TID')
* 	결과코드 : $inipay->GetResult('ResultCode') ("00"이면 지불 성공)
* 	결과내용 : $inipay->GetResult('ResultMsg') (지불결과에 대한 설명)
* 	지불방법 : $inipay->GetResult('PayMethod') (매뉴얼 참조)
* 	상점주문번호 : $inipay->GetResult('MOID')
*	결제완료금액 : $inipay->GetResult('TotPrice')
*
*  2. 신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터 (무통장입금 , 문화 상품권 포함)
*
* 	이니시스 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)
* 	이니시스 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)
*
*  3. 신용카드 결제 결과 데이터
*
* 	신용카드 승인번호 : $inipay->GetResult('ApplNum')
* 	할부기간 : $inipay->GetResult('CARD_Quota')
* 	무이자할부 여부 : $inipay->GetResult('CARD_Interest') ("1"이면 무이자할부)
* 	신용카드사 코드 : $inipay->GetResult('CARD_Code') (매뉴얼 참조)
* 	카드발급사 코드 : $inipay->GetResult('CARD_BankCode') (매뉴얼 참조)
* 	본인인증 수행여부 : $inipay->GetResult('CARD_AuthType') ("00"이면 수행)
*   각종 이벤트 적용 여부 : $inipay->GetResult('EventCode')
*
*    ** 달러결제 시 통화코드와  환률 정보 **
*	해당 통화코드 : $inipay->GetResult('OrgCurrency')
*	환율 : $inipay->GetResult('ExchangeRate')
*
*   아래는 "신용카드 및 OK CASH BAG 복합결제" 또는"신용카드 지불시에 OK CASH BAG적립"시에 추가되는 데이터
* 	OK Cashbag 적립 승인번호 : $inipay->GetResult('OCB_SaveApplNum')
* 	OK Cashbag 사용 승인번호 : $inipay->GetResult('OCB_PayApplNum')
* 	OK Cashbag 승인일시 : $inipay->GetResult('OCB_ApplDate') (YYYYMMDDHHMMSS)
* 	OCB 카드번호 : $inipay->GetResult('OCB_Num')
* 	OK Cashbag 복합결재시 신용카드 지불금액 : $inipay->GetResult('CARD_ApplPrice')
* 	OK Cashbag 복합결재시 포인트 지불금액 : $inipay->GetResult('OCB_PayPrice')
*
* 4. 실시간 계좌이체 결제 결과 데이터
*
* 	은행코드 : $inipay->GetResult('ACCT_BankCode')
*	현금영수증 발행결과코드 : $inipay->GetResult('CSHR_ResultCode')
*	현금영수증 발행구분코드 : $inipay->GetResult('CSHR_Type')
*
* 5. 무통장 입금 결제 결과 데이터
* 	가상계좌 채번에 사용된 주민번호 : $inipay->GetResult('VACT_RegNum')
* 	가상계좌 번호 : $inipay->GetResult('VACT_Num')
* 	입금할 은행 코드 : $inipay->GetResult('VACT_BankCode')
* 	입금예정일 : $inipay->GetResult('VACT_Date') (YYYYMMDD)
* 	송금자 명 : $inipay->GetResult('VACT_InputName')
* 	예금주 명 : $inipay->GetResult('VACT_Name')
*
* 6. 핸드폰 결제 결과 데이터
* 	전화결제 사업자 코드 : $inipay->GetResult('HPP_GWCode') ( "실패 내역 자세히 보기"에서 필요 , 상점에서는 필요없는 정보임)
* 	휴대폰 번호 : $inipay->GetResult('HPP_Num') (핸드폰 결제에 사용된 휴대폰번호)
*
* 7. 모든 결제 수단에 대해 결제 실패시에만 결제 결과 데이터
* 	에러코드 : $inipay->GetResult('ResultErrorCode')
*
****************************************************************************************************************/

/*******************************************************************
* DB연동 실패 시 강제취소                                      *
*                                                                 *
* 지불 결과를 DB 등에 저장하거나 기타 작업을 수행하다가 실패하는  *
* 경우, 아래의 코드를 참조하여 이미 지불된 거래를 취소하는 코드를 *
* 작성합니다.                                                     *
*******************************************************************/
/*
$cancelFlag	= "false";
if($cancelFlag == "true")
{
	$TID = $inipay->GetResult("TID");
	$inipay->SetField("type", "cancel"); // 고정
	$inipay->SetField("tid", $TID); // 고정
	$inipay->SetField("cancelmsg", "DB FAIL"); // 취소사유
	$inipay->startAction();
	if($inipay->GetResult('ResultCode') == "00")
	{
		$inipay->MakeTXErrMsg(MERCHANT_DB_ERR,"Merchant DB FAIL");
	}
}
*/

//--- 결제 방법
$pgPayMethod	= array(
		'VCard'			=> '신용카드(ISP)',
		'Card'			=> '신용카드(안심클릭)',
		'DirectBank'	=> '실시간계좌이체',
		'HPP'			=> '핸드폰',
		'VBank'			=> '무통장입금(가상계좌)',
		'YPAY'			=> '옐로페이',
);

//--- 카드사 코드
$pgCards	= array(
		'01'	=> '외환카드',
		'03'	=> '롯데카드',
		'04'	=> '현대카드',
		'06'	=> '국민카드',
		'11'	=> 'BC카드',
		'12'	=> '삼성카드',
		'13'	=> '(구)LG카드',
		'14'	=> '신한카드',
		'15'	=> '한미카드',
		'16'	=> 'NH카드',
		'17'	=> '하나SK카드',
		'21'	=> '해외비자카드',
		'22'	=> '해외마스터카드',
		'23'	=> '해외JCB카드',
		'24'	=> '해외아멕스카드',
		'25'	=> '해외다이너스카드',
);

//--- 은행 코드
$pgBanks	= array(
		'02'	=> '한국산업은행',
		'03'	=> '기업은행',
		'04'	=> '국민은행',
		'05'	=> '외환은행',
		'07'	=> '수협중앙회',
		'11'	=> '농협중앙회',
		'12'	=> '단위농협',
		'16'	=> '축협중앙회',
		'20'	=> '우리은행',
		'21'	=> '신한은행',
		'23'	=> '제일은행',
		'25'	=> '하나은행',
		'26'	=> '신한은행',
		'27'	=> '한국씨티은행',
		'31'	=> '대구은행',
		'32'	=> '부산은행',
		'34'	=> '광주은행',
		'35'	=> '제주은행',
		'37'	=> '전북은행',
		'38'	=> '강원은행',
		'39'	=> '경남은행',
		'41'	=> '비씨카드',
		'53'	=> '씨티은행',
		'54'	=> '홍콩상하이은행',
		'71'	=> '우체국',
		'81'	=> '하나은행',
		'83'	=> '평화은행',
		'87'	=> '신세계',
		'88'	=> '신한은행',
);

//--- 주문번호
$ordno		= $_POST['ordno'];

//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= 'PG명 : 이니시스 - INIpay V5.0'.chr(10);
$settlelog	.= '주문번호 : '.$ordno.chr(10);
$settlelog	.= '거래번호 : '.$inipay->GetResult('TID').chr(10);
$settlelog	.= '결과코드 : '.$inipay->GetResult('ResultCode').chr(10);
$settlelog	.= '결과내용 : '.strip_tags($inipay->GetResult('ResultMsg')).chr(10);
$settlelog	.= '지불방법 : '.$inipay->GetResult('PayMethod').' - '.$pgPayMethod[$inipay->GetResult('PayMethod')].chr(10);
if ($_POST['escrow'] == "Y") {
	$settlelog	.= '에스크로 : 해당 결제는 에스크로 결제임'.chr(10);
}
$settlelog	.= '승인금액 : '.$inipay->GetResult('TotPrice').chr(10);
if ($inipay->GetResult('PayMethod') == "YPAY") {
	$settlelog	.= '승인날짜 : '.$inipay->GetResult('YPAY_ApplDate').chr(10);
	$settlelog	.= '승인번호 : '.$inipay->GetResult('YPAY_ApplNum').chr(10);

} else {
	$settlelog	.= '승인날짜 : '.$inipay->GetResult('ApplDate').chr(10);
	$settlelog	.= '승인시간 : '.$inipay->GetResult('ApplTime').chr(10);
	$settlelog	.= '승인번호 : '.$inipay->GetResult('ApplNum').chr(10);
}
$settlelog	.= ' --------------------------------------------------'.chr(10);

//--- 승인여부 / 결제 방법에 따른 처리 설정
if($inipay->GetResult('ResultCode') === "00"){

	// PG 결과
	$getPgResult	= true;
	$pgResultMsg	= '결제자동확인 : 결제확인시간';

	switch($inipay->GetResult('PayMethod')){

		// 신용카드
		case 'Card': case 'VCard':
			$card_nm	= $pgCards[$inipay->GetResult('CARD_Code')];
			//$settlelog	.= '신용카드번호 : '.$inipay->GetResult('CARD_Num').chr(10);
			$settlelog	.= '카드할부여부 : '.$inipay->GetResult('CARD_Interest').' (1이면 무이자할부)'.chr(10);
			$settlelog	.= '카드할부기간 : '.$inipay->GetResult('CARD_Quota').chr(10);
			$settlelog	.= '카드사 코드 : '.$inipay->GetResult('CARD_Code').' - '.$pgCards[$inipay->GetResult('CARD_Code')].chr(10);
			$settlelog	.= '카드 발급사 : '.$inipay->GetResult('CARD_BankCode').' - '.$pgBanks[$inipay->GetResult('CARD_BankCode')].chr(10);
			$settlelog	.= '카드 이벤트 : '.$inipay->GetResult('EventCode').chr(10);
			if ($inipay->GetResult('OCB_Num')) {
				$settlelog	.= ' -------------- OK Cashbag 관련 내용 --------------'.chr(10);
				$settlelog	.= 'OK Cashbag 적립 승인번호 : '.$inipay->GetResult('OCB_SaveApplNum').chr(10);
				$settlelog	.= 'OK Cashbag 사용 승인번호 : '.$inipay->GetResult('OCB_PayApplNum').chr(10);
				$settlelog	.= 'OK Cashbag 승인일시 : '.$inipay->GetResult('OCB_ApplDate').chr(10);
				$settlelog	.= 'OK Cashbag 카드번호 : '.$inipay->GetResult('OCB_Num').chr(10);
				$settlelog	.= '복합결재시 신용카드 지불금액 : '.$inipay->GetResult('CARD_ApplPrice').chr(10);
				$settlelog	.= '복합결재시 포인트 지불금액 : '.$inipay->GetResult('OCB_PayPrice').chr(10);
				$settlelog	.= ' --------------------------------------------------'.chr(10);
			}
		break;

		// 계좌이체
		case 'DirectBank':
			$CSHR_ResultCode	= $inipay->GetResult('CSHR_ResultCode');
			$settlelog	.= '실시간계좌이체 은행 코드 : '.$inipay->GetResult('ACCT_BankCode').' - '.$pgBanks[$inipay->GetResult('ACCT_BankCode')].chr(10);
			$settlelog	.= '현금영수증 발급결과 코드 : '.$inipay->GetResult('CSHR_ResultCode').chr(10);
			$settlelog	.= '현금영수증 발급구분 코드 : '.$inipay->GetResult('CSHR_Type').chr(10);

			// 현금영수증 결과 정보
			if (empty($CSHR_ResultCode) === false) {
				$settlelog	.= ' -------------- 현금영수증 관련 내용 --------------'.chr(10);
				$settlelog	.= '현금영수증 발급완료 : 승인확인시간('.date('Y-m-d H:i:s').')'.chr(10);
				$settlelog	.= '주문번호 : '.$ordno.chr(10);
				$settlelog	.= '결과내용 : 계좌이체 현금영수증 발급 완료'.chr(10);
				$settlelog	.= '현금영수증 발급결과 코드 : '.$inipay->GetResult('CSHR_ResultCode').chr(10);
				$settlelog	.= '현금영수증 발급구분 코드 : '.$inipay->GetResult('CSHR_Type').chr(10);
				$settlelog	.= ' --------------------------------------------------'.chr(10);
			}
		break;

		// 가상계좌
		case 'VBank':
			$bank_nm	= $pgBanks[$inipay->GetResult('VACT_BankCode')];
			$settlelog	.= ' *** 아직 결제가 완료 된것이 아닌 신청 완료임 ***'.chr(10);
			$settlelog	.= '입금계좌번호 : '.$inipay->GetResult('VACT_Num').chr(10);
			$settlelog	.= '입금은행코드 : '.$inipay->GetResult('VACT_BankCode').' - '.$pgBanks[$inipay->GetResult('VACT_BankCode')].chr(10);
			$settlelog	.= '예금주명 : '.$inipay->GetResult('VACT_Name').chr(10);
			$settlelog	.= '송금자명 : '.$inipay->GetResult('VACT_InputName').chr(10);
			$settlelog	.= '송금일자 : '.$inipay->GetResult('VACT_Date').chr(10);
			$settlelog	.= '송금시각 : '.$inipay->GetResult('VACT_Time').chr(10);

			$pgResultMsg	= '계좌할당완료 : 신청확인시간';
		break;

		// 핸드폰
		case 'HPP':
			$settlelog	.= '휴대폰 번호 : '.$inipay->GetResult('HPP_Num').chr(10);
		break;

		// 옐로페이
		case 'YPAY':
			$settlelog	.= '휴대폰 번호 : '.$inipay->GetResult('YPAY_PhoneNum').chr(10);
			$settlelog	.= '승인일자 : '.$inipay->GetResult('YPAY_ApplDate').chr(10);
			$settlelog	.= '승인번호 : '.$inipay->GetResult('YPAY_ApplNum').chr(10);
		break;
	}

	$settlelog	= '===================================================='.chr(10).$pgResultMsg.'('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	// PG 결과
	$getPgResult	= false;

	$settlelog	= '===================================================='.chr(10).'결제실패확인 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}

//--- 가상계좌 결제의 재고 체크 단계 설정
$res_cstock = true;
if($cfg['stepStock'] == '1' && $inipay->GetResult('PayMethod') == "VBank") $res_cstock = false;

//--- 재고 체크 후 재고가 없는 경우 강제 취소
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock === true){
	$TID	= $inipay->GetResult("TID");
	$inipay->SetField("type", "cancel");			// 고정
	$inipay->SetField("tid", $TID);					// 고정
	$inipay->SetField("cancelmsg", "OUT OF STOCK");	// 취소사유
	$inipay->startAction();
	if($inipay->GetResult('ResultCode') === "00")
	{
		$inipay->MakeTXErrMsg(MERCHANT_DB_ERR,"OUT OF STOCK");

		// PG 결과
		$getPgResult	= false;
		$getPgResultCd	= "cancel";

		// 로그 재설정
		$settlelog	= '****************************************************'.chr(10).'강제취소확인 : 강제취소시간('.date('Y-m-d H:i:s').')'.chr(10).'취소사유 : 재고 부족으로 강제 취소'.chr(10).$settlelog.'****************************************************'.chr(10);
	}
}

//--- 전자보증보험 발급
@session_start();
if (session_is_registered('eggData') === true && $getPgResult === true){
	if ($_SESSION['eggData']['ordno'] == $ordno && $_SESSION['eggData']['resno1'] != '' && $_SESSION['eggData']['resno2'] != '' && $_SESSION['eggData']['agree'] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION['eggData'];
		switch($inipay->GetResult('PayMethod')){
			case 'Card': case 'VCard':
				$eggData['payInfo1'] = $pgCards[$inipay->GetResult('CARD_Code')];		// (*) 결제정보(카드사)
				$eggData['payInfo2'] = $inipay->GetResult('ApplNum');					// (*) 결제정보(승인번호)
				break;
			case "DirectBank":
				$eggData['payInfo1'] = $pgBanks[$inipay->GetResult('ACCT_BankCode')];	// (*) 결제정보(은행명)
				$eggData['payInfo2'] = $inipay->GetResult('TID');						// (*) 결제정보(승인번호 or 거래번호)
				break;
			case "VBank":
				$eggData['payInfo1'] = $pgBanks[$inipay->GetResult('VACT_BankCode')];	// (*) 결제정보(은행명)
				$eggData['payInfo2'] = $inipay->GetResult('VACT_Num');				// (*) 결제정보(계좌번호)
				break;
			case "YPAY":
				$eggData['payInfo1'] = $inipay->GetResult('YPAY_ApplNum');			// (*) 결제정보(승인번호)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $inipay->GetResult('PayMethod') == "VBank" ){
			//$inipay->GetResult('ResultCode') = '';
		}
		else if ( $eggCls->isErr == true && in_array($inipay->GetResult('PayMethod'), array("Card","VCard","DirectBank")) );
	}
	session_unregister('eggData');
}

//--- 중복 결제 체크
$oData = $db->fetch("SELECT step, vAccount FROM ".GD_ORDER." WHERE ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($inipay->GetResult('ResultCode'),"1179")){		// 중복결제

	// 로그 저장
	$db->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'$settlelog') WHERE ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
	exit();

}

//--- 결제 성공시 디비 처리
if( $getPgResult === true ){

	$query = "
	SELECT * from
		".GD_ORDER." a
		LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
	WHERE
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	// 에스크로 여부 확인
	$escrowyn = ($_POST['escrow']=="Y") ? "y" : "n";
	$escrowno = $inipay->GetResult('TID');

	// 결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	// 가상계좌 결제시 계좌정보 저장
	if ($inipay->GetResult('PayMethod')=="VBank"){
		$vAccount = $bank_nm." ".$inipay->GetResult('VACT_Num')." ".$inipay->GetResult('VACT_Name');
		$step = 0; $qrc1 = $qrc2 = "";
	}

	// 현금영수증 저장
	if (empty($CSHR_ResultCode) === false) {
		$qrc1 .= "cashreceipt='{$inipay->GetResult('TID')}',";
	}

	// 실데이타 저장
	$db->query("
	UPDATE ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
		cardtno		= '".$inipay->GetResult('TID')."'
	WHERE ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	// 주문로그 저장
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	// 재고 처리
	setStock($ordno);

	// 상품구입시 적립금 사용
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
	}

	### 주문확인메일
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	// SMS 변수 설정
	$dataSms = $data;

	if ($inipay->GetResult('PayMethod')!="VBank"){
		sendMailCase($data[email],1,$data);			### 입금확인메일
		sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
	}

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else {		// 카드결제 실패
	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$inipay->GetResult('TID')."' where ordno='$ordno'");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

	if($getPgResultCd == "cancel"){
		$cancel -> cancel_db_proc($ordno,$inipay->GetResult('TID'));
	}

	// Ncash 결제 승인 취소 API 호출
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>