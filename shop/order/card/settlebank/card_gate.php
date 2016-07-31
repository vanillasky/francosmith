<?

### 세틀뱅크

include "../conf/pg.settlebank.php";
@include "../conf/pg.escrow.php";
require_once "../lib/Pg_RingToPay.class.php";

	//ringTOpay 설정정보 로드 
	$RtP = new Pg_RingToPay();
	$RtP->RtoPConfigRead();

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
	//상품명에 특수문자 및 태그 제거
	$ordnm	= pg_text_replace(strip_tags($ordnm));
	if($i > 1)$ordnm .= " 외".($i-1)."건";

	/*
	 * 1. 기본결제 정보	 
	 */	
	
	$STT['MID']						= $pg['id'];										//상점아이디
	$STT['KEYCODE']					= $pg['key'];										//세틀뱅크에서 발급받은 키값
	$STT['OID']						= $_POST['ordno'];									//주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$STT['AMOUNT']					= $_POST['settleprice'];							//결제금액("," 를 제외한 결제금액을 입력하세요)
	$STT['PRODUCTINFO']				= $ordnm;											//상품명
	$STT['SETTLEKIND']				= $_POST[settlekind];								// 신용카드 : c , 계좌이체 : o , 가상계좌 : v , 핸드폰 : h
	$STT['RPAY_YN']					= $RtP->getRpay_yn();								// 링투페이의 사용여부를 설정한다. godo서버와 연동

	 $tpl->assign('STT',$STT);
?>