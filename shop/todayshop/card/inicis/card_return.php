<?

$banks	= array(
		'02'	=> '한국산업은행',
		'03'	=> '기업은행',
		'04'	=> '국민은행',
		'05'	=> '외환은행',
		'06'	=> '주택은행',
		'07'	=> '수협중앙회',
		'11'	=> '농협중앙회',
		'12'	=> '단위농협',
		'16'	=> '축협중앙회',
		'20'	=> '우리은행',
		'21'	=> '조흥은행',
		'22'	=> '상업은행',
		'23'	=> '제일은행',
		'24'	=> '한일은행',
		'25'	=> '서울은행',
		'26'	=> '신한은행',
		'27'	=> '한미은행',
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

$cards	= array(
		'01'	=> '외환',
		'03'	=> '롯데',
		'04'	=> '현대',
		'06'	=> '국민',
		'11'	=> 'BC',
		'12'	=> '삼성',
		'13'	=> 'LG',
		'14'	=> '신한',
		'21'	=> '해외비자',
		'22'	=> '해외마스터',
		'23'	=> 'JCB',
		'24'	=> '해외아멕스',
		'25'	=> '해외다이너스',
);

include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.inicis.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

extract($_POST);

include "sample/INIpay41Lib.php";
$inipay = new INIpay41;

/*********************
 * 3. 지불 정보 설정 *
 *********************/
$inipay->m_inipayHome = dirname($_SERVER['SCRIPT_FILENAME']); // 이니페이 홈디렉터리
$inipay->m_type = "securepay"; // 고정
$inipay->m_pgId = "INIpay".$pgid; // 고정
$inipay->m_subPgIp = "203.238.3.10"; // 고정
$inipay->m_keyPw = "1111"; // 키패스워드(상점아이디에 따라 변경)
$inipay->m_debug = "true"; // 로그모드("true"로 설정하면 상세로그가 생성됨.)
$inipay->m_mid = $mid; // 상점아이디
$inipay->m_uid = $uid; // INIpay User ID
$inipay->m_uip = '127.0.0.1'; // 고정
$inipay->m_goodName = $goodname;
$inipay->m_currency = $currency;
$inipay->m_price = $price;
$inipay->m_buyerName = $buyername;
$inipay->m_buyerTel = $buyertel;
$inipay->m_buyerEmail = $buyeremail;
$inipay->m_payMethod = $paymethod;
$inipay->m_encrypted = $encrypted;
$inipay->m_sessionKey = $sessionkey;
$inipay->m_url = "http://".$_SERVER[SERVER_NAME]; // 실제 서비스되는 상점 SITE URL로 변경할것
$inipay->m_cardcode = $cardcode; // 카드코드 리턴
$inipay->m_ParentEmail = $parentemail; // 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)

/*-----------------------------------------------------------------*
 * 수취인 정보 *                                                   *
 *-----------------------------------------------------------------*
 * 실물배송을 하는 상점의 경우에 사용되는 필드들이며               *
 * 아래의 값들은 INIsecurepay.html 페이지에서 포스트 되도록        *
 * 필드를 만들어 주도록 하십시요.                                  *
 * 컨텐츠 제공업체의 경우 삭제하셔도 무방합니다.                   *
 *-----------------------------------------------------------------*/
$inipay->m_recvName = $recvname;	// 수취인 명
$inipay->m_recvTel = $recvtel;		// 수취인 연락처
$inipay->m_recvAddr = $recvaddr;	// 수취인 주소
$inipay->m_recvPostNum = $recvpostnum;  // 수취인 우편번호
$inipay->m_recvMsg = $recvmsg;		// 전달 메세지

/****************
 * 4. 지불 요청 *
 ****************/
$inipay->startAction();

/****************************************************************************************************************
 * 5. 결제  결과																								*
 *																												*
 *  가. 모든 결제 수단에 공통되는 결제 결과 내용																*
 * 	거래번호 : $inipay->m_tid																					*
 * 	결과코드 : $inipay->m_resultCode ("00"이면 지불 성공)														*
 * 	결과내용 : $inipay->m_resultMsg (지불결과에 대한 설명)														*
 * 	지불방법 : $inipay->m_payMethod (매뉴얼 참조)																*
 * 	상점주문번호 : $inipay->m_moid																				*
 *																												*
 *  나. 신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제시에만 결제 결과 내용				*
 *              (무통장입금 , 문화 상품권)																		*
 * 	이니시스 승인날짜 : $inipay->m_pgAuthDate (YYYYMMDD)														*
 * 	이니시스 승인시각 : $inipay->m_pgAuthTime (HHMMSS)															*
 *																												*
 *  다. 신용카드  결제수단을 이용시에만  결제결과 내용															*
 *																												*
 * 	신용카드 승인번호 : $inipay->m_authCode																		*
 * 	할부기간 : $inipay->m_cardQuota																				*
 * 	무이자할부 여부 : $inipay->m_quotaInterest ("1"이면 무이자할부)												*
 * 	신용카드사 코드 : $inipay->m_cardCode (매뉴얼 참조)															*
 * 	카드발급사 코드 : $inipay->m_cardIssuerCode (매뉴얼 참조)													*
 * 	본인인증 수행여부 : $inipay->m_authCertain ("00"이면 수행)													*
 *      각종 이벤트 적용 여부 : $inipay->m_eventFlag															*
 *																												*
 *      아래 내용은 "신용카드 및 OK CASH BAG 복합결제" 또는"신용카드 지불시에 OK CASH BAG적립"시에 추가되는 내용*
 * 	OK Cashbag 적립 승인번호 : $inipay->m_ocbSaveAuthCode														*
 * 	OK Cashbag 사용 승인번호 : $inipay->m_ocbUseAuthCode														*
 * 	OK Cashbag 승인일시 : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)												*
 * 	OCB 카드번호 : $inipay->m_ocbcardnumber																		*
 * 	OK Cashbag 복합결재시 신용카드 지불금액 : $inipay->m_price1													*
 * 	OK Cashbag 복합결재시 포인트 지불금액 : $inipay->m_price2													*
 *																												*
 * 라. OK CASH BAG 결제수단을 이용시에만  결제결과 내용	 출력													*
 * 	OK Cashbag 적립 승인번호 : $inipay->m_ocbSaveAuthCode														*
 * 	OK Cashbag 사용 승인번호 : $inipay->m_ocbUseAuthCode														*
 * 	OK Cashbag 승인일시 : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)												*
 * 	OCB 카드번호 : $inipay->m_ocbcardnumber																		*
 *																												*
 * 마. 무통장 입금 결제수단을 이용시에만  결제 결과 내용														*
 * 	가상계좌 채번에 사용된 주민번호 : $inipay->m_perno															*
 * 	가상계좌 번호 : $inipay->m_vacct																			*
 * 	입금할 은행 코드 : $inipay->m_vcdbank																		*
 * 	입금예정일 : $inipay->m_dtinput (YYYYMMDD)																	*
 * 	송금자 명 : $inipay->m_nminput																				*
 * 	예금주 명 : $inipay->m_nmvacct																				*
 *																												*
 * 바. 핸드폰, 전화결제시에만  결제 결과 내용 ( "실패 내역 자세히 보기"에서 필요 , 상점에서는 필요없는 정보임)  *
 * 	전화결제 사업자 코드 : $inipay->m_codegw                        											*
 *																												*
 * 사. 핸드폰 결제수단을 이용시에만  결제 결과 내용																*
 * 	휴대폰 번호 : $inipay->m_nohpp (핸드폰 결제에 사용된 휴대폰번호)       										*
 *																												*
 * 아. 전화 결제수단을 이용시에만  결제 결과 내용																*
 * 	전화번호 : $inipay->m_noars (전화결제에  사용된 전화번호)      												*
 * 																												*
 * 자. 문화 상품권 결제수단을 이용시에만  결제 결과 내용														*
 * 	컬쳐 랜드 ID : $inipay->m_cultureid	                           												*
 *																												*
 * 차. 모든 결제 수단에 대해 결제 실패시에만 결제 결과 내용 													*
 * 	에러코드 : $inipay->m_resulterrcode                             											*
 *																												*
 ****************************************************************************************************************/

$inipay->m_resultMsg = strip_tags($inipay->m_resultMsg);

switch ($inipay->m_payMethod){
	case "Card": case "VCard":
		$card_nm = $cards[$inipay->m_cardCode];
		$settlelogAdd = "
승인일시 : $inipay->m_pgAuthDate $inipay->m_pgAuthTime
승인번호 : $inipay->m_authCode
할부기간 : $inipay->m_cardQuota
무 이 자 : $inipay->m_quotaInterest
신용카드사 : [$inipay->m_cardCode] $card_nm
카드발급사 : $inipay->m_cardIssuerCode
본인인증 : $inipay->m_authCertain
이 벤 트 : $inipay->m_eventFlag
";
		break;
	case "DirectBank":
		$settlelogAdd = "
승인일시 : $inipay->m_pgAuthDate $inipay->m_pgAuthTime
";
		break;
	case "VBank":
		$bank_nm = $banks[$inipay->m_vcdbank];
		$settlelogAdd = "
주민번호 : $inipay->m_perno
은 행 명 : [$inipay->m_vcdbank] $bank_nm
가상계좌 : $inipay->m_vacct
예금주명 : $inipay->m_nmvacct
입금예정일 : $inipay->m_dtinput
송금자명 : $inipay->m_nminput
";
		break;
	case "HPP":
		$settlelogAdd = "
핸드폰번호 : $inipay->m_nohpp
";
		break;
}

$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
거래번호 : $inipay->m_tid
결과코드 : $inipay->m_resultCode
결과내용 : $inipay->m_resultMsg
지불방법 : $inipay->m_payMethod
승인금액 : $inipay->m_price
----------------------------------------";

$settlelog .= $settlelogAdd."----------------------------------------";

### 전자보증보험 발급
@session_start();
if (session_is_registered('eggData') === true && !strcmp($inipay->m_resultCode,"00")){
	if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION[eggData];
		switch ($inipay->m_payMethod){
			case "Card": case "VCard":
				$eggData[payInfo1] = $cards[$inipay->m_cardCode]; # (*) 결제정보(카드사)
				$eggData[payInfo2] = $inipay->m_authCode; # (*) 결제정보(승인번호)
				break;
			case "DirectBank":
				$eggData[payInfo1] = $banks[$inipay->m_directbankcode]; # (*) 결제정보(은행명)
				$eggData[payInfo2] = $inipay->m_tid; # (*) 결제정보(승인번호 or 거래번호)
				break;
			case "VBank":
				$eggData[payInfo1] = $banks[$inipay->m_vcdbank]; # (*) 결제정보(은행명)
				$eggData[payInfo2] = $inipay->m_vacct; # (*) 결제정보(계좌번호)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $inipay->m_payMethod == "VBank" ){
			$inipay->m_resultCode = '';
		}
		else if ( $eggCls->isErr == true && in_array($inipay->m_payMethod, array("Card","VCard","DirectBank")) );
	}
	session_unregister('eggData');
}

### 가상계좌 결제의 재고 체크 단계 설정
$res_cstock = true;
if($cfg['stepStock'] == '1' && $inipay->m_payMethod=="VBank") $res_cstock = false;

### item check stock
include "../../../lib/cardCancel.class.php";
include "../../../lib/cardCancel_social.class.php";
$cancel = new cardCancel_social();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$inipay->m_type = "cancel"; // 고정
	$inipay->m_msg = "OUT OF STOCK"; // 취소사유
	$inipay->startAction();
	if($inipay->m_resultCode == "00")
	{
		$inipay->m_resultCode = "01";
		$inipay->m_resultMsg = "OUT OF STOCK";
	}
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($inipay->m_resultCode,"1179")){		// 중복결제

	### 로그 저장
	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else if( !strcmp($inipay->m_resultCode,"00") ){		// 카드결제 성공

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	include "../../../lib/cart.class.php";

	$cart = new Cart($_COOKIE[gd_isDirect]);
	$cart->chkCoupon();
	$cart->delivery = $data[delivery];
	$cart->dc = $member[dc]."%";
	$cart->calcu();
	$cart -> totalprice += $data[price];

	### 주문확인메일
	$data[cart] = $cart;
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	//sendMailCase($data[email],0,$data);
	// 투데이샵 주문 sms & 메일 그리고 쿠폰 발급
	$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
	$orderinfo = $todayshop_noti->getorderinfo($ordno);
	$todayshop_noti->set($ordno,'order');
	$todayshop_noti->send();

	### 에스크로 여부 확인
	$escrowyn = ($_POST[escrow]=="Y") ? "y" : "n";
	$escrowno = $inipay->m_tid;

	### 결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### 가상계좌 결제시 계좌정보 저장
	if ($inipay->m_payMethod=="VBank"){
		$vAccount = $bank_nm." ".$inipay->m_vacct." ".$inipay->m_nmvacct;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### 현금영수증 저장
	if ($inipay->rcash_rslt == '00' || $inipay->rcash_rslt == '0000' || $inipay->m_rcash_rslt == '00' || $inipay->m_rcash_rslt == '0000'){
		$qrc1 .= "cashreceipt='{$inipay->m_tid}',";
	}

	### 실데이타 저장
	$db->query("
	update ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
		cardtno		= '".$inipay->m_tid."'
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	### 주문로그 저장
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### 재고 처리
	setStock($ordno);

	### 상품구입시 적립금 사용
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
	}

	### SMS 변수 설정
	$dataSms = $data;

	if ($inipay->m_payMethod!="VBank"){
		/*/
		sendMailCase($data['email'],1,$data);			### 입금확인메일
		sendSmsCase('incash',$data['mobileOrder']);	### 입금확인SMS
		/*/
		// 즉시 발급 쿠폰 생성 및 문자 전송 (todayshop_noti 클래스는 todayshop 을 상속받았기 때문에 멤버를 사용해도 됨)
		if ($orderinfo['goodstype'] == 'coupon') { // 쿠폰인 경우
			if ($orderinfo['processtype'] == 'i') { // 즉시 발급 쿠폰만 발급하고 SMS/MAIL
				if (($cp_sno = $todayshop_noti->publishCoupon($ordno)) !== false) {
					$formatter = &load_class('stringFormatter', 'stringFormatter');
					if ($phone = $formatter->get($data['mobileReceiver'],'dial','-')) {
						$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$cp_sno'");	// 발급 처리
						ctlStep($ordno,4,1);
					}
				}
			}
		}
		else {	
			// 쿠폰이 아닌 실물상품인 경우, 판매량 증가
			$query = "
				select
				TG.tgsno from ".GD_ORDER_ITEM." AS O
				INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
				ON O.goodsno = TG.goodsno
				where O.ordno='$ordno'
			";
			$res = $db->query($query);
			while($tmp = $db->fetch($res)) {
	
				$query = "
					SELECT
	
						IFNULL(SUM(OI.ea), 0) AS cnt
	
					FROM ".GD_ORDER." AS O
					INNER JOIN ".GD_ORDER_ITEM." AS OI
						ON O.ordno=OI.ordno
					INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
						ON OI.goodsno = TG.goodsno
	
					WHERE
							O.step > 0
						AND O.step2 < 40
						AND TG.tgsno='".$tmp['tgsno']."'
	
				";
	
				$_res = $db->query($query);
	
				while ($_tmp = $db->fetch($_res)) {
	
					$query = "
					UPDATE
						".GD_TODAYSHOP_GOODS_MERGED."		AS TGM
						INNER JOIN ".GD_TODAYSHOP_GOODS."	AS TG	ON TGM.goodsno = TG.goodsno
					SET
						TGM.buyercnt = ".$_tmp['cnt'].",
						TG.buyercnt = ".$_tmp['cnt']."
					WHERE
						TG.tgsno = ".$tmp['tgsno']."
					";
					$db->query($query);
	
				}
	
			}
		}			
		// eof 투데이샵 쿠폰 발급
		/**/

	}
	/*
	else {
		sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
	}
	*/

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else {		// 카드결제 실패
	if($inipay->m_msg == "OUT OF STOCK"){
		$cancel -> cancel_db_proc($ordno,$inipay->m_tid);
	}else{
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$inipay->m_tid."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
	}

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>