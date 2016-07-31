<?php /* Template_ 2.2.7 2015/08/31 17:51:33 /www/francotr3287_godo_co_kr/shop/data/skin/standard/order/card/lgdacom.htm 000008505 */ ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="no-cache"/> 
<meta http-equiv="Expires" content="0"/> 
<meta http-equiv="Pragma" content="no-cache"/>
<script language = 'javascript'>
<!--
/*
 * 상점결제 인증요청후 PAYKEY를 받아서 최종결제 요청.
 */
function doPay_ActiveX(){
	ret = xpay_check(document.getElementById('LGD_PAYINFO'), '<?php echo $TPL_VAR["LGD"]["PLATFORM"]?>');

	if (ret=="00"){	 //ActiveX 로딩 성공
		var LGD_RESPCODE		= dpop.getData('LGD_RESPCODE');		//결과코드
		var LGD_RESPMSG			= dpop.getData('LGD_RESPMSG');		//결과메세지

		if( "0000" == LGD_RESPCODE ) { //인증성공
			var LGD_PAYKEY	  = dpop.getData('LGD_PAYKEY');			//LG데이콤 인증KEY
			var msg = "인증결과 : " + LGD_RESPMSG + "\n";
			msg += "LGD_PAYKEY : " + LGD_PAYKEY +"\n\n";
			document.getElementById('LGD_PAYKEY').value = LGD_PAYKEY;
			//alert(msg);
			document.getElementById('LGD_PAYINFO').submit();
		} else { //인증실패
			alert("인증이 실패하였습니다. " + LGD_RESPMSG);
		}
	} else {
		alert("LG U+ 전저결제를 위한 XPayPlugin 모듈이 설치되지 않았습니다.");
		xpay_showInstall(); //설치안내 팝업페이지 표시 코드 추가
	}
}

function doPay_CUPS() {
	var f = document.getElementById('LGD_PAYINFO');
	f.action=f.instance.value + f.page.value;
	f.target = "Window";
	f.submit();
}

// 플러그인 설치가 올바른지 확인
function chkPgFlag(){
	if(!hasXpayObject()){
		alert('LG데이콤 전자결제를 위한 플러그인 설치 후 다시 시도 하십시오.');
		return false;
	}
	return true;
}
//-->
</script>
<form id="LGD_PAYINFO" method="POST" action="<?php echo $GLOBALS["cfg"]["rootDir"]?>/order/card/lgdacom/card_return.php">
<input type="hidden" name="CST_PLATFORM"				value="<?php echo $TPL_VAR["LGD"]["PLATFORM"]?>">					<!-- 테스트, 서비스 구분 -->
<input type="hidden" name="CST_MID"						value="<?php echo $TPL_VAR["LGD"]["CMID"]?>">						<!-- 상점아이디 -->
<input type="hidden" name="LGD_MID"						value="<?php echo $TPL_VAR["LGD"]["MID"]?>">						<!-- 상점아이디 -->
<input type="hidden" name="LGD_OID"						value="<?php echo $TPL_VAR["LGD"]["OID"]?>">						<!-- 주문번호 -->
<input type="hidden" name="LGD_PRODUCTINFO"				value="<?php echo $TPL_VAR["LGD"]["PRODUCTINFO"]?>">				<!-- 상품정보 -->
<input type="hidden" name="LGD_AMOUNT"					value="<?php echo $TPL_VAR["LGD"]["AMOUNT"]?>">					<!-- 결제금액 -->
<input type="hidden" name="LGD_TAXFREEAMOUNT"			value="">								<!-- 면세금액 -->
<input type="hidden" name="LGD_BUYER"					value="<?php echo $TPL_VAR["nameOrder"]?>">					<!-- 구매자 -->
<input type="hidden" name="LGD_BUYERID"					value="<?php if($GLOBALS["sess"]["m_id"]){?><?php echo $GLOBALS["sess"]["m_id"]?><?php }elseif($TPL_VAR["email"]){?><?php echo $TPL_VAR["email"]?><?php }else{?>guest<?php }?>">	<!-- 구매자 ID -->
<input type="hidden" name="LGD_BUYERPHONE"				value="<?php echo implode('-',$TPL_VAR["mobileOrder"])?>">	<!-- 구매자 전화 -->
<input type="hidden" name="LGD_BUYEREMAIL"				value="<?php echo $TPL_VAR["email"]?>">						<!-- 구매자 이메일 -->
<input type="hidden" name="LGD_BUYERADDRESS"			value="<?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?>">		<!-- 배송처 -->
<input type="hidden" name="LGD_RECEIVER"				value="<?php echo $TPL_VAR["nameReceiver"]?>">					<!-- 수취인 -->
<input type="hidden" name="LGD_RECEIVERPHONE"			value="<?php echo implode('-',$TPL_VAR["mobileReceiver"])?>">	<!-- 수취인 전화번호 -->

<?php if($TPL_VAR["settlekind"]=="c"){?>
<!-- 할부개월 선택창 제어를 위한 선택적인 hidden정보 -->
<input type="hidden" name="LGD_INSTALLRANGE"			value="<?php echo $TPL_VAR["pg"]["quota"]?>">						<!-- 할부개월 범위-->
<!-- 무이자 할부(수수료 상점부담) 여부를 선택하는 hidden정보 -->
<input type="hidden" name="LGD_NOINTINF"				value="<?php if($TPL_VAR["pg"]["zerofee"]=="1"){?><?php echo $TPL_VAR["pg"]["zerofee_period"]?><?php }?>">			<!-- 신용카드 무이자 할부 적용하기 -->
<?php }?>

<?php if($TPL_VAR["settlekind"]=="o"||$TPL_VAR["settlekind"]=="v"){?>
<!--계좌이체|무통장입금(가상계좌)-->
<input type="hidden" name="LGD_CASHRECEIPTYN"   value="<?php if($TPL_VAR["pg"]["receipt"]!="Y"){?>N<?php }else{?>Y<?php }?>"> <!-- 현금영수증 사용여부(Y:사용,N:미사용) -->
<?php }?>

<?php if($TPL_VAR["settlekind"]=="v"){?>
<!-- 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 데이콤에 전송해야 합니다 . -->
<input type="hidden" name="LGD_CASNOTEURL"				value="<?php echo $TPL_VAR["LGD"]["CASNOTEURL"]?>">				<!-- 가상계좌 NOTEURL -->
<?php }?>

<input type="hidden" name="LGD_CUSTOM_SKIN"				value="<?php echo $TPL_VAR["LGD"]["CUSTOM_SKIN"]?>">				<!-- 결제창 SKIN -->
<input type="hidden" name="LGD_CUSTOM_PROCESSTYPE"		value="<?php echo $TPL_VAR["LGD"]["CUSTOM_PROCESSTYPE"]?>">		<!-- 트랜잭션 처리방식 -->
<input type="hidden" name="LGD_TIMESTAMP"				value="<?php echo $TPL_VAR["LGD"]["TIMESTAMP"]?>">				<!-- 타임스탬프 -->
<input type="hidden" name="LGD_HASHDATA"				value="<?php echo $TPL_VAR["LGD"]["HASHDATA"]?>">					<!-- MD5 해쉬암호값 -->
<input type="hidden" name="LGD_CUSTOM_USABLEPAY"		value="<?php echo $TPL_VAR["LGD"]["USABLEPAY"]?>">				<!-- 상점정의결제가능수단 (신용카드:SC0010,계좌이체:SC0030,무통장:SC0040,휴대폰:SC0060)-->
<input type="hidden" name="LGD_CUSTOM_PROCESSTIMEOUT"	value="<?php echo $TPL_VAR["LGD"]["CUSTOM_PROCESSTIMEOUT"]?>">	<!-- TWOTR타임아웃 시간 -->
<input type="hidden" name="LGD_PAYKEY" id="LGD_PAYKEY">								<!-- LG데이콤 PAYKEY(인증후 자동셋팅)-->
<input type="hidden" name="LGD_VERSION"					value="PHP_XPay_1.0">					<!-- 버전정보 (삭제하지 마세요) -->

<input type="hidden" name="LGD_ESCROW_USEYN"			value="<?php echo $_POST["escrow"]?>">					<!-- 에스크로 여부 : 적용(Y),미적용(N)-->
<?php if($_POST["escrow"]=="Y"){?>
<?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<input type="hidden" name="LGD_ESCROW_GOODID"			value="<?php echo $TPL_V1["goodsno"]?>">						<!-- 에스크로상품번호 -->
<input type="hidden" name="LGD_ESCROW_GOODNAME"			value="<?php echo $TPL_V1["goodsnm"]?>">						<!-- 에스크로상품명 -->
<input type="hidden" name="LGD_ESCROW_GOODCODE"			value="">								<!-- 에스크로상품코드 -->
<input type="hidden" name="LGD_ESCROW_UNITPRICE"		value="<?php echo ($TPL_V1["price"]+$TPL_V1["addprice"])?>">			<!-- 에스크로상품가격 -->
<input type="hidden" name="LGD_ESCROW_QUANTITY"			value="<?php echo $TPL_V1["ea"]?>">							<!-- 에스크로상품수량 -->
<?php }}?>

<?php if($TPL_VAR["zonecode"]){?>
		<input type="hidden" name="LGD_ESCROW_ZIPCODE"			value="<?php echo $TPL_VAR["zonecode"]?>">						<!-- 에스크로배송지구역번호 (새우편번호) -->
		<input type="hidden" name="LGD_ESCROW_ADDRESS1"			value="<?php echo $TPL_VAR["road_address"]?>">					<!-- 에스크로배송지주소동까지 (도로명주소) -->
<?php }else{?>
		<input type="hidden" name="LGD_ESCROW_ZIPCODE"			value="<?php echo implode("-",$TPL_VAR["zipcode"])?>">		<!-- 에스크로배송지우편번호 -->
		<input type="hidden" name="LGD_ESCROW_ADDRESS1"			value="<?php echo $TPL_VAR["address"]?>">						<!-- 에스크로배송지주소동까지 -->
<?php }?>
<input type="hidden" name="LGD_ESCROW_ADDRESS2"			value="<?php echo $TPL_VAR["address_sub"]?>">					<!-- 에스크로배송지주소상세 -->
<input type="hidden" name="LGD_ESCROW_BUYERPHONE"		value="<?php echo implode('-',$TPL_VAR["mobileOrder"])?>">	<!-- 에스크로구매자휴대폰번호 -->
<?php }?>

<?php if($TPL_VAR["settlekind"]=="u"){?>
<!-- 중국은련 카드 관련 필드 -->
<input type="hidden" name="instance" id="instance" value="<?php if(!empty($_SERVER["HTTPS"])){?>https<?php }else{?>http<?php }?>://xpay.lgdacom.net"/>
<input type="hidden" name="page" id="page" value="/xpay/Request.do"/>
<input type="hidden" name="LGD_RETURNURL" value="<?php echo $TPL_VAR["LGD"]["CUPRETURNURL"]?>"/>
<input type="hidden" name="LGD_NOTEURL"   value="<?php echo $TPL_VAR["LGD"]["CUPNOTEURL"]?>"/>
<input type="hidden" name="LGD_PAYWINDOWTYPE" value="CUPS">
<?php }?>

</form>
<script language="javascript" src="<?php if(!empty($_SERVER["HTTPS"])){?>https<?php }else{?>http<?php }?>://xpay.lgdacom.net/xpay/js/xpay_ub.js" type="text/javascript"></script>
<script language="javascript" src="<?php if(!empty($_SERVER["HTTPS"])){?>https<?php }else{?>http<?php }?>://xpay.lgdacom.net/xpay/js/xpay_install.js" type="text/javascript"></script>