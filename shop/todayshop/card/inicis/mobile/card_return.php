<?

include dirname(__FILE__)."/../../../../lib/library.php";
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inicis.php";

$pg_mobile = $pg;

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

/* 인증 성공 */
if($_POST['P_STATUS'] == '00'){
	$reqData = array(
		'P_TID' => $_POST['P_TID'],
		'P_MID' => $pg_mobile['id'],
	);
	
	/* 실제 결제 요청 */
	$res = readpost($_POST['P_REQ_URL'],$reqData);

	/* 결제결과 리턴값 파싱 */
	$resData = stringUnserialize($res);

	$resData['P_RMESG1'] = strip_tags($resData['P_RMESG1']);

	$ordno = $resData['P_OID'];

	switch ($resData['P_TYPE']){
		case "CARD":
			$card_nm = $cards[$resData['P_FN_CD1']];
			$paymethod = 'Card';
	
			$settlelogAdd = "
승인일시 : ".$resData['P_AUTH_DT']."
승인번호 : ".$resData['P_AUTH_NO']."
할부기간 : ".$resData['P_RMESG2']."
신용카드사 : [".$resData['P_FN_CD1']."] $card_nm
카드발급사 : ".$resData['P_CARD_ISSUER_CODE']."
";
			break;
		case "VBANK":
			$bank_nm = $banks[$resData['P_VACT_BANK_CODE']];
			$paymethod = 'VBank';
			$settlelogAdd = "
은 행 명 : [".$resData['P_VACT_BANK_CODE']."] $bank_nm
가상계좌 : ".$resData['P_VACT_NUM']."
계좌주명 : ".$resData['P_VACT_NAME']."
입금마감일시 : ".$resData['P_VACT_DATE']." ".$resData['P_VACT_TIME']."
";
			break;
		case "MOBILE":
			$paymethod = 'HPP';
			$settlelogAdd = "
휴대폰통신사 : ".$resData['P_HPP_CORP']."
";
			break;
	}

	$settlelog = "INIpay Mobile 결제요청에 대한 결과
$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
거래번호 : ".$resData['P_TID']."
결과코드 : ".$resData['P_STATUS']."
결과내용 : ".$resData['P_RMESG1']."
지불방법 : ".$resData['P_TYPE']."
승인금액 : ".$resData['P_AMT']."
----------------------------------------";

	$settlelog .= $settlelogAdd."----------------------------------------";

	### 가상계좌 결제의 재고 체크 단계 설정
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $resData['P_TYPE']=="VBANK") $res_cstock = false;

	### item check stock
	include dirname(__FILE__)."/../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $res_cstock){

		include dirname(__FILE__)."/../sample/INIpay41Lib.php";
		$inipay = new INIpay41;

		/*********************
		 * 지불 정보 설정 *
		 *********************/
		$inipay->m_inipayHome = dirname($_SERVER['SCRIPT_FILENAME']); // 이니페이 홈디렉터리
		$inipay->m_pgId = "INIpay".$pg_mobileid; // 고정
		$inipay->m_subPgIp = "203.238.3.10"; // 고정
		$inipay->m_keyPw = "1111"; // 키패스워드(상점아이디에 따라 변경)
		$inipay->m_debug = "true"; // 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$inipay->m_mid = $pg_mobile['id']; // 상점아이디
		$inipay->m_uip = '127.0.0.1'; // 고정
		$inipay->m_type = "cancel"; // 고정
		$inipay->m_msg = "OUT OF STOCK"; // 취소사유
		$inipay->startAction();
		if($inipay->m_resultCode == "00")
		{
			$resData['P_STATUS'] = "01";
			$resData['P_RMESG1'] = "OUT OF STOCK";
		}
	}

	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($resData['P_STATUS'],"1179")){		// 중복결제
		### 로그 저장
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else if( !strcmp($resData['P_STATUS'],"00") ){		// 카드결제 성공

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		include dirname(__FILE__)."/../../../../lib/cart.class.php";

		$cart = new Cart($_COOKIE[gd_isDirect]);
		$cart->chkCoupon();
		$cart->delivery = $data[delivery];
		$cart->dc = $member[dc]."%";
		$cart->calcu();
		$cart -> totalprice += $data[price];

		### 주문확인메일
		$data[cart] = $cart;
		$data[str_settlekind] = $r_settlekind[$data[settlekind]];
		sendMailCase($data[email],0,$data);

		### 에스크로 여부 확인
		$escrowyn = ($_POST[escrow]=="Y") ? "y" : "n";
		$escrowno = $resData['P_TID'];

		### 결제 정보 저장
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		### 가상계좌 결제시 계좌정보 저장
		if ($resData['P_TYPE']=="VBANK"){
			$vAccount = $bank_nm." ".$resData['P_VACT_NUM']." ".$resData['P_VACT_NAME'];
			$step = 0; $qrc1 = $qrc2 = "";
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
			cardtno		= '".$resData['P_TID']."'
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);

		### 상품구입시 적립금 사용
		if ($sess[m_no] && $data[emoney]){
			setEmoney($sess[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
		}

		### SMS 변수 설정
		$dataSms = $data;

		if ($resData['P_TYPE']!="VBANK"){
			sendMailCase($data[email],1,$data);			### 입금확인메일
			sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
		}

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else {		// 카드결제 실패
		if($resData['P_RMESG1'] == "OUT OF STOCK"){
			$cancel -> cancel_db_proc($ordno,$inipay->m_tid);
		}else{
			$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$resData['P_TID']."' where ordno='$ordno'");
			$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
		}
		
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
	}
}
else{
	msg($_POST['P_RMESG1']);
	go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
}

/* 결제결과 리턴값 파싱 함수 */
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
?>