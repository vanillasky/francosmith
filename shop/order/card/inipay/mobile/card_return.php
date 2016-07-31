<?php
/**
 * 이니시스 PG 모듈 페이지
 * 이니시스 PG 버전 : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../lib/library.php";
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inipay.php";

parse_str($_POST['P_NOTI'],$P_NOTI);
// PG결제 위변조 체크 및 유효성 체크
if (forge_order_check($_GET['ordno'],$P_NOTI['P_AMT']) === false) {
	msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.',$cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=".$_GET['ordno'],'parent');
	exit();
}

// 네이버 마일리지 결제 승인 API
include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
$naverNcash = new naverNcash(true);
if ($naverNcash->useyn == 'Y') {
	if ($_GET['settlekind'] == 'v') $ncashResult = $naverNcash->payment_approval($_GET['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_GET['ordno'], true);
	if ($ncashResult === false) {
		msg('네이버 마일리지 사용에 실패하였습니다.', $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$_GET['ordno'], 'parent');
		exit();
	}
}

// 모바일 변수로 처리
$pg_mobile	= $pg;

// 인증 로그 저장 (이니시스 로그로 파일로 저장 이니시스의 모든 값을 저장)
$logfile		= fopen( dirname(__FILE__) . '/../log/INI_Mobile_auth_'.date('Ymd').'.log', 'a+' );
$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
foreach ($_POST as $key => $val) {
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
}
$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
fwrite( $logfile, $logInfo);
fclose( $logfile );

//--- 결제결과 리턴값 파싱 함수
function stringUnserialize($string){
	$string = trim($string);
	$arr = explode("&",$string);
	$result = array();
	foreach($arr as $v){
		$div = explode("=",$v);
		$result[$div[0]] = $div[1];
	}
	return $result;
}

//--- 결제 방법
$pgPayMethod	= array(
		'CARD'			=> '신용카드',
		'BANK'			=> '실시간계좌이체',
		'MOBILE'		=> '핸드폰',
		'VBANK'			=> '무통장입금(가상계좌)',
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

//--- 인증 성공
if($_POST['P_STATUS'] === '00')
{
	//--- 이니시스에 결제 요청을 위한 세팅
	$reqData	= array(
		'P_TID'	=> $_POST['P_TID'],
		'P_MID'	=> $pg_mobile['id'],
	);

	//--- 실제 결제 요청
	$res	= readpost($_POST['P_REQ_URL'],$reqData);

	//--- 결제 결과 리턴값 파싱
	$resData	= stringUnserialize($res);

	//--- 결과 로그 저장 (이니시스 로그로 파일로 저장 이니시스의 모든 값을 저장)
	$logfile		= fopen( dirname(__FILE__) . '/../log/INI_Mobile_result_'.date('Ymd').'.log', 'a+' );
	$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
	foreach ($resData as $key => $val) {
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
	}
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
	$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
	fwrite( $logfile, $logInfo);
	fclose( $logfile );

	//--- 주문 번호
	$ordno	= $resData['P_OID'];

	//--- 결과 메시지
	$resData['P_RMESG1']	= strip_tags($resData['P_RMESG1']);

	//--- 로그 생성
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= 'PG명 : 이니시스 - INIpay Mobile'.chr(10);
	$settlelog	.= '주문번호 : '.$ordno.chr(10);
	$settlelog	.= '거래번호 : '.$resData['P_TID'].chr(10);
	$settlelog	.= '결과코드 : '.$resData['P_STATUS'].chr(10);
	$settlelog	.= '결과내용 : '.$resData['P_RMESG1'].chr(10);
	$settlelog	.= '지불방법 : '.$resData['P_TYPE'].' - '.$pgPayMethod[$resData['P_TYPE']].chr(10);
	$settlelog	.= '승인금액 : '.$resData['P_AMT'].chr(10);
	$settlelog	.= '승인일자 : '.$resData['P_AUTH_DT'].chr(10);
	$settlelog	.= '승인번호 : '.$resData['P_AUTH_NO'].chr(10);
	$settlelog	.= ' --------------------------------------------------'.chr(10);

	//--- 승인여부 / 결제 방법에 따른 처리 설정
	if($resData['P_STATUS'] === "00"){

		// PG 결과
		$getPgResult	= true;
		$pgResultMsg	= '결제자동확인 : 결제확인시간';

		switch ($resData['P_TYPE']){
			case "CARD":
				$card_nm	= $pgCards[$resData['P_FN_CD1']];

				$settlelog	.= '카드할부기간 : '.$resData['P_RMESG2'].chr(10);
				$settlelog	.= '카드사 코드 : '.$resData['P_FN_CD1'].' - '.$card_nm.chr(10);
				$settlelog	.= '카드 발급사 : '.$resData['P_CARD_ISSUER_CODE'].' - '.$pgBanks[$resData['P_CARD_ISSUER_CODE']].chr(10);
				break;

			case 'BANK':

			break;

			case "VBANK":
				$bank_nm	= $pgBanks[$resData['P_VACT_BANK_CODE']];

				$settlelog	.= ' *** 아직 결제가 완료 된것이 아닌 신청 완료임 ***'.chr(10);
				$settlelog	.= '입금계좌번호 : '.$resData['P_VACT_NUM'].chr(10);
				$settlelog	.= '입금은행코드 : '.$resData['P_VACT_BANK_CODE'].' - '.$bank_nm.chr(10);
				$settlelog	.= '계좌주명 : '.$resData['P_VACT_NAME'].chr(10);
				$settlelog	.= '송금일자 : '.$resData['P_VACT_DATE'].chr(10);
				$settlelog	.= '송금시각 : '.$resData['P_VACT_TIME'].chr(10);

				$pgResultMsg	= '계좌할당완료 : 신청확인시간';
				break;

			case "MOBILE":
				$settlelog	.= '휴대폰통신사 : '.$resData['P_HPP_CORP'].chr(10);
				break;
		}

		$settlelog	= '===================================================='.chr(10).$pgResultMsg.'('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

		if (forge_order_check($ordno, $resData['P_AMT']) === false) {
			include SHOPROOT.'/conf/pg.inipay.php';
			include SHOPROOT.'/order/card/inipay/libs/INILib.php';
			$inipay	= new INIpay50;
			$inipay->SetField('inipayhome',	SHOPROOT.'/order/card/inipay');	// 이니페이 홈디렉터리
			$inipay->SetField('type', 'cancel');	// 고정 (절대 수정 불가)
			$inipay->SetField('debug', 'true');	// 로그모드('true'로 설정하면 상세로그가 생성됨.)
			$inipay->SetField('mid', $pg['id']);	// 상점아이디
			$inipay->SetField('admin', '1111');	// 비대칭 사용키 키패스워드
			$inipay->SetField('tid', $resData['P_TID']);	// 취소할 거래의 거래아이디
			$inipay->SetField('cancelmsg', '거래금액 위변조 감지로 인한 자동취소');	// 취소사유
			$inipay->startAction();
			$getPgResult = false;
			$settlelog = '----------------------------------------'.
				PHP_EOL.'결과내용 : 거래금액 위변조 감지로 인한 자동취소'.
				PHP_EOL.'----------------------------------------'.
				PHP_EOL.$settlelog;
		}
	} else {
		// PG 결과
		$getPgResult	= false;

		$settlelog	= '===================================================='.chr(10).'결제실패확인 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}

	//--- 중복 결제 체크
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($resData['P_STATUS'],"1179")){		// 중복결제

		// 로그 저장
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
		exit();

	}

	//--- 결제 성공시 디비 처리
	if( $getPgResult === true ){

		$query = "
		SELECT * FROM
			".GD_ORDER." a
			LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
		WHERE
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		// 결제 정보 저장
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		// 가상계좌 결제시 계좌정보 저장
		if ($resData['P_TYPE']=="VBANK"){
			$vAccount = $bank_nm." ".$resData['P_VACT_NUM']." ".$resData['P_VACT_NAME'];
			$step = 0; $qrc1 = $qrc2 = "";
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
			cardtno		= '".$resData['P_TID']."'
		WHERE ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		// 주문로그 저장
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		// 재고 처리
		setStock($ordno);

		// 상품구입시 적립금 사용
		if ($sess[m_no] && $data[emoney]){
			setEmoney($sess[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
		}

		### 주문확인메일
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($ordno));
		}

		// SMS 변수 설정
		$dataSms = $data;

		if ($resData['P_TYPE']!="VBANK"){
			sendMailCase($data[email],1,$data);			### 입금확인메일
			sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
		}

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else {		// 카드결제 실패
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$resData['P_TID']."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

		// 네이버 마일리지 결제 승인 취소 API 호출
		if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
	}
}
else
{
	$ordno = $_GET['ordno'];

	// 네이버 마일리지 결제 승인 취소 API 호출
	if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);

	msg($_POST['P_RMESG1']);
	go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
}
?>