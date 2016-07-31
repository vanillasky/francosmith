<?php
/********************************************************************************
*
* 파일명 : AGS_pay_ing.php
* 최종수정일자 : 2009/04/10
*
* 올더게이트 플러그인에서 리턴된 데이타를 받아서 소켓결제요청을 합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
*
*  ※ 유의사항 ※
*  1.  "|"(파이프) 값은 결제처리 중 구분자로 사용하는 문자이므로 결제 데이터에 "|"이 있을경우
*   결제가 정상적으로 처리되지 않습니다.(수신 데이터 길이 에러 등의 사유)
********************************************************************************/

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.agspay.php";

	// PG결제 위변조 체크 및 유효성 체크
	if (forge_order_check($_POST['OrdNo'],$_POST['Amt']) === false) {
		msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.','../../order_fail.php?ordno='.$_POST['OrdNo'],'parent');
		exit();
	}

	// Ncash 결제 승인 API
	include "../../../lib/naverNcash.class.php";
	$naverNcash = new naverNcash();
	if($naverNcash->useyn=='Y')
	{
		if(trim($_POST["AuthTy"])=='virtual') $ncashResult = $naverNcash->payment_approval($_POST['OrdNo'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['OrdNo'], true);
		if($ncashResult===false)
		{
			msg('네이버 마일리지 사용에 실패하였습니다.', '../../order_fail.php?ordno='.$_POST['OrdNo'],'parent');
			exit();
		}
	}

	/****************************************************************************
	*
	* [1] 라이브러리(AGSLib.php)를 인클루드 합니다.
	*
	****************************************************************************/
	require ("./lib/AGSLib.php");


	/****************************************************************************
	*
	* [2]. agspay4.0 클래스의 인스턴스를 생성합니다.
	*
	****************************************************************************/
	$agspay = new agspay40;



	/****************************************************************************
	*
	* [3] AGS_pay.html 로 부터 넘겨받을 데이타
	*
	****************************************************************************/

	/*공통사용*/
	//$agspay->SetValue("AgsPayHome","C:/htdocs/agspay");			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
	$agspay->SetValue("AgsPayHome",$_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/log/agspay');			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
	$agspay->SetValue("StoreId",trim($_POST["StoreId"]));		//상점아이디
	$agspay->SetValue("log","true");							//true : 로그기록, false : 로그기록안함.
	$agspay->SetValue("logLevel","INFO");						//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
	$agspay->SetValue("UseNetCancel","true");					//true : 망취소 사용. false: 망취소 미사용
	$agspay->SetValue("Type", "Pay");							//고정값(수정불가)
	$agspay->SetValue("RecvLen", 7);							//수신 데이터(길이) 체크 에러시 6 또는 7 설정.

	$agspay->SetValue("AuthTy",trim($_POST["AuthTy"]));			//결제형태
	$agspay->SetValue("SubTy",trim($_POST["SubTy"]));			//서브결제형태
	$agspay->SetValue("OrdNo",trim($_POST["OrdNo"]));			//주문번호
	$agspay->SetValue("Amt",trim($_POST["Amt"]));				//금액
	$agspay->SetValue("UserEmail",trim($_POST["UserEmail"]));	//주문자이메일
	$agspay->SetValue("ProdNm",trim($_POST["ProdNm"]));			//상품명

	/*신용카드&가상계좌사용*/
	$agspay->SetValue("MallUrl",trim($_POST["MallUrl"]));		//MallUrl(무통장입금) - 상점 도메인 가상계좌추가
	$agspay->SetValue("UserId",trim($_POST["UserId"]));			//회원아이디


	/*신용카드사용*/
	$agspay->SetValue("OrdNm",trim($_POST["OrdNm"]));			//주문자명
	$agspay->SetValue("OrdPhone",trim($_POST["OrdPhone"]));		//주문자연락처
	$agspay->SetValue("OrdAddr",trim($_POST["OrdAddr"]));		//주문자주소 가상계좌추가
	$agspay->SetValue("RcpNm",trim($_POST["RcpNm"]));			//수신자명
	$agspay->SetValue("RcpPhone",trim($_POST["RcpPhone"]));		//수신자연락처
	$agspay->SetValue("DlvAddr",trim($_POST["DlvAddr"]));		//배송지주소
	$agspay->SetValue("Remark",trim($_POST["Remark"]));			//비고
	$agspay->SetValue("DeviId",trim($_POST["DeviId"]));			//단말기아이디
	$agspay->SetValue("AuthYn",trim($_POST["AuthYn"]));			//인증여부
	$agspay->SetValue("Instmt",trim($_POST["Instmt"]));			//할부개월수
	$agspay->SetValue("UserIp",$_SERVER["REMOTE_ADDR"]);		//회원 IP

	/*신용카드(ISP)*/
	$agspay->SetValue("partial_mm",trim($_POST["partial_mm"]));		//일반할부기간
	$agspay->SetValue("noIntMonth",trim($_POST["noIntMonth"]));		//무이자할부기간
	$agspay->SetValue("KVP_CURRENCY",trim($_POST["KVP_CURRENCY"]));	//KVP_통화코드
	$agspay->SetValue("KVP_CARDCODE",trim($_POST["KVP_CARDCODE"]));	//KVP_카드사코드
	$agspay->SetValue("KVP_SESSIONKEY",$_POST["KVP_SESSIONKEY"]);	//KVP_SESSIONKEY
	$agspay->SetValue("KVP_ENCDATA",$_POST["KVP_ENCDATA"]);			//KVP_ENCDATA
	$agspay->SetValue("KVP_CONAME",trim($_POST["KVP_CONAME"]));		//KVP_카드명
	$agspay->SetValue("KVP_NOINT",trim($_POST["KVP_NOINT"]));		//KVP_무이자=1 일반=0
	$agspay->SetValue("KVP_QUOTA",trim($_POST["KVP_QUOTA"]));		//KVP_할부개월

	/*신용카드(안심)*/
	$agspay->SetValue("CardNo",trim($_POST["CardNo"]));			//카드번호
	$agspay->SetValue("MPI_CAVV",$_POST["MPI_CAVV"]);			//MPI_CAVV
	$agspay->SetValue("MPI_ECI",$_POST["MPI_ECI"]);				//MPI_ECI
	$agspay->SetValue("MPI_MD64",$_POST["MPI_MD64"]);			//MPI_MD64

	/*신용카드(일반)*/
	$agspay->SetValue("ExpMon",trim($_POST["ExpMon"]));				//유효기간(월)
	$agspay->SetValue("ExpYear",trim($_POST["ExpYear"]));			//유효기간(년)
	$agspay->SetValue("Passwd",trim($_POST["Passwd"]));				//비밀번호
	$agspay->SetValue("SocId",trim($_POST["SocId"]));				//주민등록번호/사업자등록번호

	/*계좌이체사용*/
	$agspay->SetValue("ICHE_OUTBANKNAME",trim($_POST["ICHE_OUTBANKNAME"]));		//이체은행명
	$agspay->SetValue("ICHE_OUTACCTNO",trim($_POST["ICHE_OUTACCTNO"]));			//이체계좌번호
	$agspay->SetValue("ICHE_OUTBANKMASTER",trim($_POST["ICHE_OUTBANKMASTER"]));	//이체계좌소유주
	$agspay->SetValue("ICHE_AMOUNT",trim($_POST["ICHE_AMOUNT"]));				//이체금액

	/*핸드폰사용*/
	$agspay->SetValue("HP_SERVERINFO",trim($_POST["HP_SERVERINFO"]));	//SERVER_INFO(핸드폰결제)
	$agspay->SetValue("HP_HANDPHONE",trim($_POST["HP_HANDPHONE"]));		//HANDPHONE(핸드폰결제)
	$agspay->SetValue("HP_COMPANY",trim($_POST["HP_COMPANY"]));			//COMPANY(핸드폰결제)
	$agspay->SetValue("HP_ID",trim($_POST["HP_ID"]));					//HP_ID(핸드폰결제)
	$agspay->SetValue("HP_SUBID",trim($_POST["HP_SUBID"]));				//HP_SUBID(핸드폰결제)
	$agspay->SetValue("HP_UNITType",trim($_POST["HP_UNITType"]));		//HP_UNITType(핸드폰결제)
	$agspay->SetValue("HP_IDEN",trim($_POST["HP_IDEN"]));				//HP_IDEN(핸드폰결제)
	$agspay->SetValue("HP_IPADDR",trim($_POST["HP_IPADDR"]));			//HP_IPADDR(핸드폰결제)

	/*ARS사용*/
	$agspay->SetValue("ARS_NAME",trim($_POST["ARS_NAME"]));				//ARS_NAME(ARS결제)
	$agspay->SetValue("ARS_PHONE",trim($_POST["ARS_PHONE"]));			//ARS_PHONE(ARS결제)

	/*가상계좌사용*/
	$agspay->SetValue("VIRTUAL_CENTERCD",trim($_POST["VIRTUAL_CENTERCD"]));	//은행코드(가상계좌)
	$agspay->SetValue("VIRTUAL_DEPODT",trim($_POST["VIRTUAL_DEPODT"]));		//입금예정일(가상계좌)
	$agspay->SetValue("ZuminCode",trim($_POST["ZuminCode"]));				//주민번호(가상계좌)
	$agspay->SetValue("MallPage",trim($_POST["MallPage"]));					//상점 입/출금 통보 페이지(가상계좌)
	$agspay->SetValue("VIRTUAL_NO",trim($_POST["VIRTUAL_NO"]));				//가상계좌번호(가상계좌)

	/*에스크로사용*/
	$agspay->SetValue("ES_SENDNO",trim($_POST["ES_SENDNO"]));				//에스크로전문번호

	/*계좌이체(소켓) 결제 사용 변수*/
	$agspay->SetValue("ICHE_SOCKETYN",trim($_POST["ICHE_SOCKETYN"]));			//계좌이체(소켓) 사용 여부
	$agspay->SetValue("ICHE_POSMTID",trim($_POST["ICHE_POSMTID"]));				//계좌이체(소켓) 이용기관주문번호
	$agspay->SetValue("ICHE_FNBCMTID",trim($_POST["ICHE_FNBCMTID"]));			//계좌이체(소켓) FNBC거래번호
	$agspay->SetValue("ICHE_APTRTS",trim($_POST["ICHE_APTRTS"]));				//계좌이체(소켓) 이체 시각
	$agspay->SetValue("ICHE_REMARK1",trim($_POST["ICHE_REMARK1"]));				//계좌이체(소켓) 기타사항1
	$agspay->SetValue("ICHE_REMARK2",trim($_POST["ICHE_REMARK2"]));				//계좌이체(소켓) 기타사항2
	$agspay->SetValue("ICHE_ECWYN",trim($_POST["ICHE_ECWYN"]));					//계좌이체(소켓) 에스크로여부
	$agspay->SetValue("ICHE_ECWID",trim($_POST["ICHE_ECWID"]));					//계좌이체(소켓) 에스크로ID
	$agspay->SetValue("ICHE_ECWAMT1",trim($_POST["ICHE_ECWAMT1"]));				//계좌이체(소켓) 에스크로결제금액1
	$agspay->SetValue("ICHE_ECWAMT2",trim($_POST["ICHE_ECWAMT2"]));				//계좌이체(소켓) 에스크로결제금액2
	$agspay->SetValue("ICHE_CASHYN",trim($_POST["ICHE_CASHYN"]));				//계좌이체(소켓) 현금영수증발행여부
	$agspay->SetValue("ICHE_CASHGUBUN_CD",trim($_POST["ICHE_CASHGUBUN_CD"]));	//계좌이체(소켓) 현금영수증구분
	$agspay->SetValue("ICHE_CASHID_NO",trim($_POST["ICHE_CASHID_NO"]));			//계좌이체(소켓) 현금영수증신분확인번호

	/*계좌이체-텔래뱅킹(소켓) 결제 사용 변수*/
	$agspay->SetValue("ICHEARS_SOCKETYN", trim($_POST["ICHEARS_SOCKETYN"]));	//텔레뱅킹계좌이체(소켓) 사용 여부
	$agspay->SetValue("ICHEARS_ADMNO", trim($_POST["ICHEARS_ADMNO"]));			//텔레뱅킹계좌이체 승인번호
	$agspay->SetValue("ICHEARS_POSMTID", trim($_POST["ICHEARS_POSMTID"]));		//텔레뱅킹계좌이체 이용기관주문번호
	$agspay->SetValue("ICHEARS_CENTERCD", trim($_POST["ICHEARS_CENTERCD"]));	//텔레뱅킹계좌이체 은행코드
	$agspay->SetValue("ICHEARS_HPNO", trim($_POST["ICHEARS_HPNO"]));			//텔레뱅킹계좌이체 휴대폰번호

	/****************************************************************************
	*
	* [4] 올더게이트 결제서버로 결제를 요청합니다.
	*
	****************************************************************************/
	$agspay->startPay();


	/****************************************************************************
	*
	* [5] 결제결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
	*
	*	아래의 결과값들을 통하여 각 결제수단별 결제결과값을 사용하실 수 있습니다.
	*
	*	-- 공통사용 --
	*	업체ID : $agspay->GetResult("rStoreId")
	*	주문번호 : $agspay->GetResult("rOrdNo")
	*	상품명 : $agspay->GetResult("rProdNm")
	*	거래금액 : $agspay->GetResult("rAmt")
	*	성공여부 : $agspay->GetResult("rSuccYn") (성공:y 실패:n)
	*	결과메시지 : $agspay->GetResult("rResMsg")
	*
	*	1. 신용카드
	*
	*	전문코드 : $agspay->GetResult("rBusiCd")
	*	거래번호 : $agspay->GetResult("rDealNo")
	*	승인번호 : $agspay->GetResult("rApprNo")
	*	할부개월 : $agspay->GetResult("rInstmt")
	*	승인시각 : $agspay->GetResult("rApprTm")
	*	카드사코드 : $agspay->GetResult("rCardCd")
	*
	*	2.계좌이체(인터넷뱅킹/텔레뱅킹)
	*	에스크로주문번호 : $agspay->GetResult("ES_SENDNO") (에스크로 결제시)
	*
	*	3.가상계좌
	*	가상계좌의 결제성공은 가상계좌발급의 성공만을 의미하며 입금대기상태로 실제 고객이 입금을 완료한 것은 아닙니다.
	*	따라서 가상계좌 결제완료시 결제완료로 처리하여 상품을 배송하시면 안됩니다.
	*	결제후 고객이 발급받은 계좌로 입금이 완료되면 MallPage(상점 입금통보 페이지(가상계좌))로 입금결과가 전송되며
	*	이때 비로소 결제가 완료되게 되므로 결제완료에 대한 처리(배송요청 등)은  MallPage에 작업해주셔야 합니다.
	*	결제종류 : $agspay->GetResult("rAuthTy") (가상계좌 일반 : vir_n 유클릭 : vir_u 에스크로 : vir_s)
	*	승인일자 : $agspay->GetResult("rApprTm")
	*	가상계좌번호 : $agspay->GetResult("rVirNo")
	*
	*	4.핸드폰결제
	*	핸드폰결제일 : $agspay->GetResult("rHP_DATE")
	*	핸드폰결제 TID : $agspay->GetResult("rHP_TID")
	*
	*	5.ARS결제
	*	ARS결제일 : $agspay->GetResult("rHP_DATE")
	*	ARS결제 TID : $agspay->GetResult("rHP_TID")
	*
	****************************************************************************/


	$banks = array(
		'39' => '경남은행',
		'34' => '광주은행',
		'04' => '국민은행',
		'11' => '농협중앙회',
		'31' => '대구은행',
		'32' => '부산은행',
		'02' => '산업은행',
		'45' => '새마을금고',
		'07' => '수협중앙회',
		'48' => '신용협동조합',
		'26' => '(구)신한은행',
		'05' => '외환은행',
		'20' => '우리은행',
		'71' => '우체국',
		'37' => '전북은행',
		'23' => '제일은행',
		'35' => '제주은행',
		'21' => '(구)조흥은행',
		'03' => '중소기업은행',
		'81' => '하나은행',
		'88' => '신한은행',
		'27' => '한미은행',
	);

	$cards = array(
		'0100' => '비씨',
		'0310' => '하나비자',
		'0200' => 'KB',
		'0201' => '농협visa',
		'0206' => '씨티visa',
		'0205' => '우리visa',
		'0304' => '조흥visa',
		'0300' => '외환',
		'0309' => '주택비자',
		'1000' => '해외visa',
		'0500' => '신한',
		'1100' => '해외master',
		'0700' => '해외JCB',
		'0303' => '전북visa',
		'0302' => '광주visa',
		'0301' => '제주visa',
		'0207' => '신세계한미',
		'0203' => '한미visa',
		'0202' => '수협visa',
		'0400' => '삼성',
		'0800' => '현대',
		'0801' => '해외Diners',
		'0900' => '롯데',
		'0901' => '해외AMEX',
	);


	### 주문번호
	$ordno = $agspay->GetResult('OrdNo');

	### 로그
	$tmp_log = array();
	if ($agspay->GetResult('AuthTy') == 'card') {
		if ($agspay->GetResult('SubTy') == 'isp') {
			$tmp = '신용카드결제-안전결제(ISP)';
		} else if($agspay->GetResult('SubTy') == 'visa3d') {
			$tmp = '신용카드결제-안심클릭';
		} else if($agspay->GetResult('SubTy') == 'normal') {
			$tmp = '신용카드결제-일반결제';
		}
	} else if($agspay->GetResult('AuthTy') == 'iche') {
		$tmp = '계좌이체';
	} else if($agspay->GetResult('AuthTy') == 'hp') {
		$tmp = '핸드폰결제';
	} else if($agspay->GetResult('AuthTy') == 'ars') {
		$tmp = 'ARS결제';
	} else if($agspay->GetResult('AuthTy') == 'virtual') {
		$tmp = '가상계좌결제';
	}

	$tmp_log[] = '결제형태 : '.$tmp;
	$tmp_log[] = '상점아이디 : '.$agspay->GetResult('StoreId');
	$tmp_log[] = '주문번호 : '.$agspay->GetResult('OrdNo');
	$tmp_log[] = '주문자명 : '.$agspay->GetResult('OrdNm');
	$tmp_log[] = '상품명 : '.$agspay->GetResult('ProdNm');
	$tmp_log[] = '결제금액 : '.$agspay->GetResult('rAmt');
	$tmp_log[] = '성공여부 : '.$agspay->GetResult('rSuccYn');
	$tmp_log[] = '결과메시지 : '.$agspay->GetResult('rResMsg');

	if($agspay->GetResult('AuthTy') == 'card' || $agspay->GetResult('AuthTy') == 'virtual') {
		$tmp_log[] = '승인시각 : '.$agspay->GetResult('rApprTm');
	}

	if($agspay->GetResult('AuthTy') == 'card' ) {
		$card_nm = $cards[$agspay->GetResult('rCardCd')];
		$tmp_log[] = '전문코드 : '.$agspay->GetResult('rBusiCd');
		$tmp_log[] = '승인번호 : '.$agspay->GetResult('rApprNo');
		$tmp_log[] = '카드사코드 : '.$agspay->GetResult('rCardCd').'('.$card_nm.')';
		$tmp_log[] = '거래번호 : '.$agspay->GetResult('rDealNo');
	}

	if($agspay->GetResult('AuthTy') == 'card' && ($SubTy == 'visa3d' || $SubTy == 'normal') ) {
		$tmp_log[] = '카드사명 : '.$agspay->GetResult('rCardNm');
		$tmp_log[] = '매입사코드 : '.$agspay->GetResult('rAquiCd');
		$tmp_log[] = '매입사명 : '.$agspay->GetResult('rAquiNm');
		$tmp_log[] = '가맹점번호 : '.$agspay->GetResult('rMembNo');
	}

	if($agspay->GetResult('AuthTy') == 'iche') {
		$tmp_log[] = '이체은행명 : '.$agspay->GetResult('ICHE_OUTBANKNAME');
		$tmp_log[] = '이체계좌예금주 : '.$agspay->GetResult('ICHE_OUTBANKMASTER');
		$tmp_log[] = '이체금액 : '.$agspay->GetResult('ICHE_AMOUNT');
	}

	if($agspay->GetResult('AuthTy') == 'hp' ) {
		$tmp_log[] = '핸드폰결제TID : '.$agspay->GetResult('rHP_TID');
		$tmp_log[] = '핸드폰결제날짜 : '.$agspay->GetResult('rHP_DATE');
		$tmp_log[] = '핸드폰결제핸드폰번호 : '.$agspay->GetResult('HP_HANDPHONE');
		$tmp_log[] = '핸드폰결제통신사명 : '.$agspay->GetResult('HP_COMPANY');
	}

	if($agspay->GetResult('AuthTy') == 'virtual' || $agspay->GetResult('AuthTy') == 'evirtual' ) {
		$bank_nm = $banks[$agspay->GetResult('VIRTUAL_CENTERCD')];
		$tmp_log[] = '가상계좌번호 : '.$agspay->GetResult('rVirNo');
		$tmp_log[] = '가상계좌은행코드 : ' . $bank_nm;
	}

	if (preg_match('/ES_SENDNO:([0-9]{6})/', $agspay->GetResult('rResMsg'), $matches) == 1) {
		$es_sendno = $matches['1'];
		$tmp_log[] = '이지스에스크로(전문번호,카드) : '.$es_sendno;
	} else if ($agspay->GetResult('ES_SENDNO') != '') {
		$es_sendno = $agspay->GetResult('ES_SENDNO');
		$tmp_log[] = '이지스에스크로(전문번호,계좌/가상) : '.$es_sendno;
	}

	$settlelog = $ordno." (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	### 전자보증보험 발급
	@session_start();
	if (session_is_registered('eggData') === true && !strcmp($agspay->GetResult('rSuccYn'),"y")){
		if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = $_SESSION[eggData];
			switch ($agspay->GetResult('AuthTy')){
				case "card":
					$eggData[payInfo1] = $card_nm; # (*) 결제정보(카드사)
					$eggData[payInfo2] = $agspay->GetResult('rApprNo'); # (*) 결제정보(승인번호)
					break;
				case "iche":
					$eggData[payInfo1] = $agspay->GetResult('ICHE_OUTBANKNAME'); # (*) 결제정보(은행명)
					$eggData[payInfo2] = $agspay->GetResult("rOrdNo"); # (*) 결제정보(승인번호 or 거래번호)
					break;
				case "virtual":
				case "evirtual":
					$eggData[payInfo1] = $bank_nm; # (*) 결제정보(은행명)
					$eggData[payInfo2] = $agspay->GetResult('rVirNo'); # (*) 결제정보(계좌번호)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true && ($agspay->GetResult('AuthTy')=="virtual" || $agspay->GetResult('AuthTy')=="evirtual") ){
				$agspay->SetValue('rSuccYn', '');
			}
			else if ( $eggCls->isErr == true && in_array($agspay->GetResult('AuthTy'), array("card","iche")) );
		}
		session_unregister('eggData');
	}

	### 가상계좌 결제의 재고 체크 단계 설정
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && ($agspay->GetResult('AuthTy')=="virtual" || $agspay->GetResult('AuthTy')=="evirtual")) $res_cstock = false;

	### item check stock
	include "../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $res_cstock){
		if( !strcmp($agspay->GetResult('rSuccYn'),"y") ) msg('관리자에게 문의하여 카드승인취소 요청하십시요!');
		$step = 51;
	}

	### DB(성공&실패) 처리
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='".$ordno."'");
	if ($oData['step'] > 0 || $oData['vAccount'] != '') { // 중복결제

		### 로그 저장
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
		go('../../order_end.php?ordno='.$ordno.'&card_nm='.$card_nm,'parent');

	} else if ($agspay->GetResult('rSuccYn') == 'y' && $step != 51) { // 결제성공

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='".$ordno."'
		";
		$data = $db->fetch($query);

		### 에스크로 여부 확인
		if ($es_sendno != '') {
			$escrowyn = 'y';
			$escrowno = $es_sendno;
		} else {
			$escrowyn = 'n';
			$escrowno = '';
		}

		### 결제 정보 저장
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		### 가상계좌 결제시 계좌정보 저장
		if ($agspay->GetResult('AuthTy') == 'virtual') {
			//가상계좌결제의 경우 입금이 완료되지 않은 입금대기상태(가상계좌 발급성공)이므로 상품을 배송하시면 안됩니다.
			$vAccount = $bank_nm.' '.$agspay->GetResult('rVirNo').' '.$_POST['StoreNm'];
			$step = 0; $qrc1 = $qrc2 = '';
		}

		### 현금영수증 저장
		if (strpos($agspay->GetResult('rResMsg'),'현금영수증발행성공') !== false) {
			$qrc1 .= "cashreceipt='pg-agspay',";
		}

		### PG정보 저장
		if($agspay->GetResult('AuthTy') == 'card' ) {
			$qrc1 .= "
			cardtno		= '".$agspay->GetResult('rDealNo')."',
			pgAppNo		= '".$agspay->GetResult('rApprNo')."',
			pgCardCd		= '".$agspay->GetResult('rCardCd')."',
			pgAppDt		= '".$agspay->GetResult('rApprTm')."',
			";
		} else if($agspay->GetResult('AuthTy') == 'virtual') {
			$qrc1 .= "
			pgAppDt		= '".$agspay->GetResult('rApprTm')."',
			";
		} else if($agspay->GetResult('AuthTy') == 'iche') {
		} else if($agspay->GetResult('AuthTy') == 'hp' ) {
			$qrc1 .= "
			cardtno		= '".$agspay->GetResult('rHP_TID')."',
			pgAppDt		= '".$agspay->GetResult('rHP_DATE')."',
			";
		}

		### 실데이타 저장
		$db->query("
		update ".GD_ORDER." set ".$qrc1."
			step		= '".$step."',
			step2		= '',
			escrowyn	= '".$escrowyn."',
			escrowno	= '".$escrowno."',
			vAccount	= '".$vAccount."',
			settlelog	= concat(ifnull(settlelog,''),'".$settlelog."')
		where ordno='".$ordno."'"
		);
		$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$data['step2']]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);

		### 상품구입시 적립금 사용
		if ($data['m_no'] && $data['emoney']) {
			setEmoney($data['m_no'],-$data['emoney'],'상품구입시 적립금 결제 사용',$ordno);
		}

		### 주문확인메일
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($ordno));
		}

		### SMS 변수 설정
		$dataSms = $data;

		if ($agspay->GetResult('AuthTy') != 'virtual') {
			sendMailCase($data['email'],1,$data);			### 입금확인메일
			sendSmsCase('incash',$data['mobileOrder']);	### 입금확인SMS
		} else {
			sendSmsCase('order',$data['mobileOrder']);	### 주문확인SMS
		}

		go('../../order_end.php?ordno='.$ordno.'&card_nm='.$card_nm,'parent');
	} else { // 결제실패
		if ($step == '51') {
			$cancel->cancel_db_proc($ordno);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
		}

		// Ncash 결제 승인 취소 API 호출
		if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

		go('../../order_fail.php?ordno='.$ordno,'parent');
	}


	/*******************************************************************
	* [6] 결제가 정상처리되지 못했을 경우 $agspay->GetResult("NetCancID") 값을 이용하여
	* 결제결과에 대한 재확인요청을 할 수 있습니다.
	*
	* 추가 데이터송수신이 발생하므로 결제가 정상처리되지 않았을 경우에만 사용하시기 바랍니다.
	*
	* 사용방법 :
	* $agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*
	*******************************************************************/

	/*
	$agspay->SetValue("Type", "Pay"); // 고정
	$agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*/

	/*******************************************************************
	* [7] 상점DB 저장 및 기타 처리작업 수행실패시 강제취소
	*
	* $cancelReq : "true" 강제취소실행, "false" 강제취소실행안함.
	*
	* 결제결과에 따른 상점처리부분 수행 중 실패하는 경우
	* 아래의 코드를 참조하여 거래를 취소할 수 있습니다.
	*	취소성공여부 : $agspay->GetResult("rCancelSuccYn") (성공:y 실패:n)
	*	취소결과메시지 : $agspay->GetResult("rCancelResMsg")
	*
	* 유의사항 :
	* 가상계좌(virtual)는 강제취소 기능이 지원되지 않습니다.
	*******************************************************************/

	// 상점처리부분 수행실패시 $cancelReq를 "true"로 변경하여
	// 결제취소를 수행되도록 할 수 있습니다.
	// $cancelReq의 "true"값으로 변경조건은 상점에서 판단하셔야 합니다.

	/*
	$cancelReq = "false";

	if($cancelReq == "true")
	{
		$agspay->SetValue("Type", "Cancel"); // 고정
		$agspay->SetValue("CancelMsg", "DB FAIL"); // 취소사유
		$agspay->startPay();
	}
	*/


?>