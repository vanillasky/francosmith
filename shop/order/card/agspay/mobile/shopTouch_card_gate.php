<?php

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.agspay.php";

$UserId = "";	 //회원아이디
$StoreNm = "";	// 상점명
$pg_mobile = $pg;

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
$ordnm = strip_tags($ordnm);
if($i > 1){
	if(strlen($ordnm) > 90 )	$ordnm = substr($ordnm,0,90);
	$ordnm .= " 외".($i-1)."건";
}


switch ($_POST[settlekind]){
	case "c":	// 신용카드
		$settlekind		= "card";
		break;
	case "v":	// 가상계좌
		$settlekind		= "virtual";
		break;
	case "h":	// 핸드폰
		$settlekind		= "hp";
		break;
}

//회원아이디
$UserId = ($sess) ? $sess['m_id']: 'guest';
//상점명
$StoreNm = ($cfg['compName']) ? $cfg['compName'] : $_SERVER['SERVER_NAME'];
?>

<script language=javascript>

var _ua = window.navigator.userAgent.toLowerCase();

var browser = {
	model: _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/) ? _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/)[0] : "",
	skt : /msie/.test( _ua ) && /nate/.test( _ua ),
	lgt : /msie/.test( _ua ) && /([010|011|016|017|018|019]{3}\d{3,4}\d{4}$)/.test( _ua ),
	opera : (/opera/.test( _ua ) && /(ppc|skt)/.test(_ua)) || /opera mobi/.test( _ua ),
	ipod : /webkit/.test( _ua ) && /\(ipod/.test( _ua ) ,
	iphone : /webkit/.test( _ua ) && /\(iphone/.test( _ua ),
	lgtwv : /wv/.test( _ua ) && /lgtelecom/.test( _ua )
};

if(browser.opera) {
	document.write("<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75\" \/>");
} else if (browser.ipod || browser.iphone) {
	setTimeout(function() { if(window.pageYOffset == 0){ window.scrollTo(0, 1);} }, 100);
}

function Pay(){
	form = document.frmAGS_pay;
	if(Check_Common(form) == true){
		form.submit();
	}
}

function Check_Common(form){
	if(form.StoreId.value == ""){
		alert("상점아이디가 입력되지 않았습니다.\n다시 거래를 시도해주시기 바랍니다.");
		return false;
	}
	else if(form.StoreNm.value == ""){
		alert("상점명이 입력되지 않았습니다.\n다시 거래를 시도해주시기 바랍니다.");
		return false;
	}
	else if(form.OrdNo.value == ""){
		alert("주문번호가 입력되지 않았습니다.\n다시 거래를 시도해주시기 바랍니다.");
		return false;
	}
	else if(form.ProdNm.value == ""){
		alert("상품명이 입력되지 않았습니다.\n다시 거래를 시도해주시기 바랍니다.");
		return false;
	}
	else if(form.Amt.value == ""){
		alert("금액이 입력되지 않았습니다.\n다시 거래를 시도해주시기 바랍니다.");
		return false;
	}
	else if(form.MallUrl.value == ""){
		alert("상점URL이 입력되지 않았습니다.\n다시 거래를 시도해주시기 바랍니다.");
		return false;
	}
	return true;
}
</script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<!-- 인코딩 방식을 UTF-8로 하는 경우 action 경로 ☞ http://www.allthegate.com/payment/mobile_utf8/pay_start.jsp -->
<form name="frmAGS_pay" method="post" action="http://www.allthegate.com/payment/mobile/pay_start.jsp">
<!-- ★ => 필수 -->

<!--//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [1] 일반/무이자 결제여부를 설정합니다.
//
// 할부판매의 경우 구매자가 이자수수료를 부담하는 것이 기본입니다. 그러나,
// 상점과 올더게이트간의 별도 계약을 통해서 할부이자를 상점측에서 부담할 수 있습니다.
// 이경우 구매자는 무이자 할부거래가 가능합니다.
//
// 예제)
// 	(1) 일반결제로 사용할 경우
// 	form.DeviId.value = "9000400001";
//
// 	(2) 무이자결제로 사용할 경우
// 	form.DeviId.value = "9000400002";
//
// 	(3) 만약 결제 금액이 100,000원 미만일 경우 일반할부로 100,000원 이상일 경우 무이자할부로 사용할 경우
// 	if(parseInt(form.Amt.value) < 100000)
//		form.DeviId.value = "9000400001";
// 	else
//		form.DeviId.value = "9000400002";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

<!--//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [2] 일반 할부기간을 설정합니다.
//
// 일반 할부기간은 2 ~ 12개월까지 가능합니다.
// 0:일시불, 2:2개월, 3:3개월, ... , 12:12개월
//
// 예제)
// 	(1) 할부기간을 일시불만 가능하도록 사용할 경우
// 	form.QuotaInf.value = "0";
//
// 	(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우
//		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
//
// 	(3) 결제금액이 일정범위안에 있을 경우에만 할부가 가능하게 할 경우
// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
// 	else
// 		form.QuotaInf.value = "0";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [3] 무이자 할부기간을 설정합니다.
// (일반결제인 경우에는 본 설정은 적용되지 않습니다.)
//
// 무이자 할부기간은 2 ~ 12개월까지 가능하며,
// 올더게이트에서 제한한 할부 개월수까지만 설정해야 합니다.
//
// 100:BC
// 200:국민
// 300:외환
// 400:삼성
// 500:신한
// 800:현대
// 900:롯데
//
// 예제)
// 	(1) 모든 할부거래를 무이자로 하고 싶을때에는 ALL로 설정
// 	form.NointInf.value = "ALL";
//
// 	(2) 국민카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
// 	form.NointInf.value = "200-2:3:4:5:6";
//
// 	(3) 외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
// 	form.NointInf.value = "300-2:3:4:5:6";
//
// 	(4) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
// 	form.NointInf.value = "200-2:3:4:5:6,300-2:3:4:5:6";
//
//	(5) 무이자 할부기간 설정을 하지 않을 경우에는 NONE로 설정
//	form.NointInf.value = "NONE";
//
//	(6) 전카드사 특정개월수만 무이자를 하고 싶은경우(2:3:6개월)
//	form.NointInf.value = "100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6";
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->


<input type=hidden name=DeviId value="<? echo ($pg['zerofee'] != 'yes') ? '9000400001': '9000400002' ;?>">			<!-- 단말기아이디 - 일반결제:9000400001, 무이자결제:9000400002 -->
<input type=hidden name=QuotaInf value="<?=$pg['quota']?>">			<!-- 일반할부개월설정변수 -->
<input type=hidden name=NointInf value="<?=$pg['zerofee_period']?>">		<!-- 무이자할부개월설정변수 -->


<input type=hidden name=Job value="<?=$settlekind?>">	<!-- 결제수단 card - 신용카드 , virtual - 가상계좌, hp - 휴대폰 -->
<input type=hidden name=StoreId value="<?=$pg['id']?>">	<!-- ★상점아이디 (20) -->
<input type=hidden name=OrdNo value="<?=$_POST['ordno']?>">	<!-- ★주문번호 (40) -->
<input type=hidden name=Amt value="<?=$_POST['settleprice']?>">	<!-- ★금액 (12) -->
<input type=hidden name=StoreNm value="<?=addslashes($StoreNm)?>">	<!-- ★상점명 (50) -->
<input type=hidden name=ProdNm value="<?=addslashes($ordnm)?>">	<!-- ★상품명 (300) -->
<input type=hidden name=MallUrl value="<?='http://'.$_SERVER['SERVER_NAME']?>">	<!-- ★상점URL (50) -->
<input type=hidden name=UserEmail value="<?=$_POST['email']?>">	<!-- 주문자이메일 (50) -->
<input type=hidden name=UserId value="<?=$UserId?>">	<!-- 회원아이디 (20) -->
<input type=hidden name=OrdNm value="<?=$_POST['nameOrder']?>">	<!-- 주문자명 (40) -->
<input type=hidden name=OrdPhone value="<?=implode('-',$_POST['mobileOrder'])?>">	<!-- 주문자연락처 (21) -->
<input type=hidden name=OrdAddr value="<? echo $_POST['address'].' '.$_POST['address_sub'] ?>">	<!-- 주문자주소 (100) -->
<input type=hidden name=RcpNm value="<?=$_POST['nameReceiver']?>">	<!-- 수신자명 (40) -->
<input type=hidden name=RcpPhone value="<?=implode('-',$_POST['mobileReceiver'])?>">	<!-- 수신자연락처 (21) -->
<input type=hidden name=DlvAddr value="<? echo $_POST['address'].' '.$_POST['address_sub'] ?>">	<!-- 배송지주소 (100) -->
<input type=hidden name=Remark value="<?=addslashes($_POST['memo'])?>">	<!-- 기타요구사항 (350) -->
<input type=hidden name=CardSelect value="">	<!-- 카드사선택 - 모두 사용하고자 할 때에는 아무 값도 입력하지 않습니다. -->
<input type=hidden name=RtnUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].$cfg['rootDir'].'/order/card/agspay/mobile/shopTouch_card_return.php' ?>">	<!-- ★성공 URL (150) - 성공 URL은 반드시 상점의 AGS_pay_ing.php의 전체 경로로 맞춰 주시기 바랍니다. ex)http://www.allthegate.com/mall/AGS_pay_ing.php -->
<input type=hidden name=CancelUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/shopTouch/shopTouch_myp/orderview.php?ordno='.$_POST['ordno'] ?>">	<!-- ★취소 URL (150) - 객이 취소를 눌렀을 경우의 이동 URL 경로로 전체 경로로 맞춰 주시기 입니다. ex)http://www.allthegate.com/mall/AGS_pay_cancel.php -->
<input type=hidden name=Column1 value="">	<!-- 추가사용필드1 (200) -->
<input type=hidden name=Column2 value="">	<!-- 추가사용필드2 (200) -->
<input type=hidden name=Column3 value="">	<!-- 추가사용필드3 (200) -->

<!--  가상계좌 결제 사용 변수 시작 -->
<!-- 가상계좌 결제에서 입/출금 통보를 위한 필수 입력 사항 입니다. -->
<!-- 페이지주소는 도메인주소를 제외한 '/'이후 주소를 적어주시면 됩니다. ex)/mall/AGS_VirAcctResult.php -->
<input type=hidden name=MallPage value="/shop/order/card/agspay/mobile/AGS_VirAcctResult.php">	<!-- ★통보페이지 (100) -->
<input type=hidden name=VIRTUAL_DEPODT value="">	<!-- 입금예정일 (8) -->
<!--  가상계좌 결제 사용 변수 끝 -->

<!-- 휴대폰 결제 사용 변수 시작 -->
<input type=hidden name=HP_ID value="">	<!-- CP아이디 (10) - CP아이디를 핸드폰 결제 실거래 전환후에는 발급받으신 CPID로 변경하여 주시기 바랍니다. -->
<input type=hidden name=HP_PWD value="">	<!-- CP비밀번호 (10) - CP비밀번호를 핸드폰 결제 실거래 전환후에는 발급받으신 비밀번호로 변경하여 주시기 바랍니다. -->
<input type=hidden name=HP_SUBID value="<?=$pg['sub_cpid']?>">	<!-- SUB-CP아이디 (10) - SUB-CPID는 핸드폰 결제 실거래 전환후에 발급받으신 상점만 입력하여 주시기 바랍니다. -->
<input type=hidden name=ProdCode value="">	<!-- 상품코드 (10) - 상품코드를 핸드폰 결제 실거래 전환후에는 발급받으신 상품코드로 변경하여 주시기 바랍니다. -->
<!-- 상품종류를 핸드폰 결제 실거래 전환후에는 발급받으신 상품종류로 변경하여 주시기 바랍니다. -->
<!-- 판매하는 상품이 디지털(컨텐츠)일 경우 = 1, 실물(상품)일 경우 = 2 -->
<input type=hidden name= value="">	<!-- 상품종류 -->
<!-- 휴대폰 결제 사용 변수 끝 -->

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>잠시후 올더게이트 Mobile 결제화면으로 이동합니다.</b></strong></div>

</form>