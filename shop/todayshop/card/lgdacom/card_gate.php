<?

### 데이콤 (XPay)

//include "../conf/pg.lgdacom.php";
require_once "../lib/load.class.php";
@include "../conf/pg.escrow.php";

	// 투데이샵 사용중인 경우 PG 설정 교체
	resetPaymentGateway();

	// 무이자 여부
	$pg['zerofee']	= ( $pg['zerofee'] == "yes" ? '1' : '0' );			// 무이자 여부 (Y:1 / N:0)

	// 상품 정보
	if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
		$item = $cart -> item;
	}
	foreach($item as $v){
		$i++;
		if($i == 1) $ordnm = $v['goodsnm'];
	}
	if($i > 1)$ordnm .= " 외".($i-1)."건";

	/*
	 * 1. 기본결제 인증요청 정보 변경
	 *
	 * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
	 */
	if(!$pg['serviceType']) $pg['serviceType'] = "service";
	$LGD['PLATFORM']				= $pg['serviceType'];								//LG데이콤 결제 서비스 선택(test:테스트, service:서비스)
	$LGD['CMID']					= $pg['id'];										//상점아이디
	$LGD['MID']						= (("test" == $LGD['PLATFORM'])?"t":"").$pg['id'];	//상점아이디
	$LGD['MERTKEY']					= $pg['mertkey'];									//데이콤에서 발급받은 키값
	$LGD['OID']						= $_POST['ordno'];									//주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$LGD['AMOUNT']					= $_POST['settleprice'];							//결제금액("," 를 제외한 결제금액을 입력하세요)
	$LGD['PRODUCTINFO']				= $ordnm;											//상품명
	$LGD['TIMESTAMP']				= date(YmdHms);										//타임스탬프
	$LGD['CUSTOM_SKIN']				= $pg['skin']?$pg['skin']:"blue";					//상점정의 결제창 스킨 (red, blue, cyan, green, yellow)
	$LGD['CUSTOM_PROCESSTIMEOUT']	= "600";											//인증후 승인요청까지 가능 허용 시간(초단위), 디폴트는 10min

	$configPath						= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_today";		//LG데이콤에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

	switch ($_POST[settlekind]){

		case "c":	// 신용카드
			$LGD['USABLEPAY']		= "SC0010";
			break;
		case "o":	// 계좌이체
			$LGD['USABLEPAY']		= "SC0030";
			break;
		case "v":	// 가상계좌
			$LGD['USABLEPAY']		= "SC0040";
			break;
		case "h":	// 핸드폰
			$LGD['USABLEPAY']		= "SC0060";
			break;
	}

	/*
	 * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다.
	 */
	$tmpUrl     = explode(':', $_SERVER['HTTP_HOST']);     // 보안서버 사용인 경우 포트 제거
	$LGD['CASNOTEURL']  = "http://".$tmpUrl[0].str_replace(basename($_SERVER['PHP_SELF']),"",$_SERVER['PHP_SELF'])."card/lgdacom/cas_noteurl.php";

	/*
	 *************************************************
	 * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
	 *
	 * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
	 *************************************************
	 *
	 * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
	 * LGD_MID			: 상점아이디
	 * LGD_OID			: 주문번호
	 * LGD_AMOUNT		: 금액
	 * LGD_TIMESTAMP	: 타임스탬프
	 * LGD_MERTKEY		: 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
	 *
	 * MD5 해쉬데이터 암호화 검증을 위해
	 * LG데이콤에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
	 */
	require_once(dirname(__FILE__)."/XPayClient.php");
	$xpay = &new XPayClient($configPath, $LGD['PLATFORM']);
   	$xpay->Init_TX($LGD['MID']);
	$LGD['HASHDATA'] = md5($LGD['MID'].$LGD['OID'].$LGD['AMOUNT'].$LGD['TIMESTAMP'].$LGD['MERTKEY']);
	$LGD['CUSTOM_PROCESSTYPE'] = "TWOTR";
	/*
	 *************************************************
	 * 2. MD5 해쉬암호화 (수정하지 마세요) - END
	 *************************************************
	 */
	 $tpl->assign('LGD',$LGD);
?>