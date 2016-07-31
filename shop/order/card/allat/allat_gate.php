<?

### All@Pay™ Plus 2.0

//include "../conf/pg.allat.php";

$ordno			= $_POST[ordno];
$settlekind		= $_POST[settlekind];
$settleprice	= $_POST[settleprice];

$pg	= array(
	'id'		=> '',
	'formkey'	=> '',
	'crosskey'	=> '',
	);

$pg['zerofee']	= "N";								// 무이자 여부 (Y/N)
$pg['quota']	= "0:2:3:4:5:6:7:8:9:10:11:12";		// 할부기간
$pg['bonus']	= "N";								// 보너스포인트 사용여부 (Y/N)
$pg['cert']		= "N";								// 카드 인증여부 (Y:인증,N:인증사용안함,X:인증만사용)
$pg['tax']		= "N";								// 과세여부 (Y/N) - 현금영수증사용시 필요 (N:미사용시)
$pg['test']		= "N";								// 테스트 여부 (Y:테스트,N:실서비스)
$pg['real']		= "Y";								// 상품 실물여부 (Y:실물,N:실물아님)
$pg['escrow']	= "N";								// 에스크로 사용여부 (Y:사용,N:미사용)

?>

<script language=JavaScript src="https://tx.allatpay.com/common/allatpayX.js"></script>
<!--//----------------------초기화------------------------------//-->
<script language=JavaScript>
// 설정 필요(ShopId,무이자 여부[Y/N])
ALLAT_INIT_FUNC("<?=$pg['id']?>","<?=$pg['zerofee']?>");
</script>
<!--//-------------------- ISP 결제 -----------------------------//-->
<script language=JavaScript src="http://www.vpay.co.kr/KVPplugin.js"></script>
<script language=JavaScript>
StartSmartUpdate();
</script>
<!--//-------------------- 3D 결제  -----------------------------//-->
<script language=JavaScript src="https://www.isaackorea.net/update/ILKactx.js"></script>
<script language=JavaScript ID=ALLAT_3D_JS></script>s
<!--//-------------------- 계좌이체 ----------------------------//--->
<script language=javascript src="http://www.bankpay.or.kr/KFTCWallet.js"></script>
<script language=javascript>InstallCertManager()</script>
<script language=javascript>SmartUpdate()</script>
<script language=Javascript>
function ftn_app(dfm) {
	var ret;
	var app_type = "";
	<? if ($settlekind=="c"){ // 카드결제 ?>
	for( i=0; i< dfm.chkapp.length; i++ ){
		if( dfm.chkapp[i].checked == true ){
			app_type = dfm.chkapp[i].value;
			break;
		}
	}
	<? } else { ?>
	app_type = dfm.chkapp.value;
	<? } ?>

	// 일반 카드 결제시의 개인/법인 구분
	if( dfm.allat_business[0].checked == true ){
		dfm.allat_business_type.value = 0;
	}
	if( dfm.allat_business[1].checked == true ){
		dfm.allat_business_type.value = 1;
	}

	<? if ($pg['tax'] == "Y"){ // 현금영수증 신청 여부 ?>
	// 계좌이체 결제시의 현금영수증 등록 여부 구분
	if( dfm.allat_cash[0].checked == true ){
		dfm.allat_cash_yn.value = "Y";
	}
	if( dfm.allat_cash[1].checked == true ){
		dfm.allat_cash_yn.value = "N";
	}
	<? } ?>

	if(app_type == "ISP"){
		ret = invisible_ISP(dfm);		//Function 내부에서 submit을 하게 되어있음.
	}else if(app_type == "C3D"){
		ret = invisible_3D(dfm);		//Function 내부에서 submit을 하게 되어있음.
	}else if(app_type == "NOR"){
		ret = invisible_NOR(dfm);		//Function 내부에서 submit을 하게 되어있음.
	}else if(app_type == "ABANK"){
		ret = invisible_ABANK(dfm);		//Function 내부에서 submit을 하게 되어있음.
	}else{
		alert("app_type Error"+app_type);
	return;
	}

	if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
		// 오류 코드 : 0001~9998 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
		alert(ret.substring(4,ret.length));		// Message 가져오기
	}
	if( ret.substring(0,4)=="9999" ){
		// 오류 코드 : 9999 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
		alert(ret.substring(8,ret.length));	    // Message 가져오기
	}
}

//-----결제창 선택 Script -------//
function chk_app(what){
	if(what == "ISP"){
		NOR_3D.style.display	= "none";
		NOR.style.display		= "none";
		ABANK.style.display		= "none";
	}else if(what == "C3D"){
		NOR_3D.style.display	= "";
		NOR.style.display		= "none";
		ABANK.style.display		= "none";
	}else if(what == "NOR"){
		NOR_3D.style.display	= "";
		NOR.style.display		= "";
		ABANK.style.display		= "none";
	}else if(what == "ABANK"){
		NOR_3D.style.display	= "none";
		NOR.style.display		= "none";
		ABANK.style.display		= "";
	}else{
		return;
	}
}
</script>

<!------------- HTML : Form 설정 --------------//-->
<form name="fm" method=POST action="/shop/card/allat/allat_approval.php"> 			<!--승인요청 및 결과수신페이지 지정 //-->
<input type=hidden name=allat_shop_id value="<?=$pg[id]?>">							<!-- 상점 ID -->
<input type=hidden name=allat_order_no value="<?=$ordno?>">							<!-- 주문번호 -->
<input type=hidden name=allat_amt value="<?=$settleprice?>">						<!-- 승인금액 -->
<input type=hidden name=allat_pmember_id value="<?=$sess[m_id]?>">					<!-- 회원ID : 쇼핑몰의 회원ID (최대 20 bytes) -->
<input type=hidden name=allat_product_cd value="<?=$good_code?>">					<!-- 상품코드 : 여러상품의 경우 대표상품만 기록 (최대 50 bytes) -->
<input type=hidden name=allat_product_nm value="<?=$getProd?>">						<!-- 상품명 : 여러상품의 경우 대표상품만 기록 (최대 100 bytes)-->
<input type=hidden name=allat_buyer_nm value="<?=$ordernm?>">						<!-- 결제자성명 : (최대 20 bytes)-->
<input type=hidden name=allat_email_addr value="<?=$email?>">						<!-- 결제자 Email -->
<input type=hidden name=allat_recp_nm value="<?=$recevernm?>">						<!-- 수취인성명 -->
<input type=hidden name=allat_recp_addr value="<?=$address1?> <?=$exaddress1?>">	<!-- 수취인주소 -->

<input type=hidden name=allat_test_yn value="<?=$g_cardsettle_test?>">				<!-- 테스트 여부 : 테스트(Y),서비스(N) -->
<input type=hidden name=allat_real_yn value="<?=$g_cardsettle_real?>">				<!-- 상품 실물 여부 : 상품이 실물일 경우 (Y), 상품이 실물이 아닐경우 (N) -->
<input type=hidden name=allat_escrow_yn value="<?=$escrowUseYN?>">					<!-- 상품 에스크로 여부 : 적용(Y),미적용(N)-->

<input type=hidden name=etc_CardMode value="<?=$etc_CardMode?>">					<!-- 일반결제, 재결제, 기타결제용 구분 필드 - 추가해준것임 -->

<!-- 암호화 필드 -->
<input type=hidden name=allat_enc_data value="">									<!-- 자동설정 (후에 암호화값 리턴받는 필드) -->
<input type=hidden name=allat_opt_pin value="NOVIEW">								<!-- 고정값 NOVIEW : 올앳참조필드 -->
<input type=hidden name=allat_opt_mod value="WEB">									<!-- 고정값 WEB : 올앳참조필드 -->

<!-- 카드 결제시 필수 필드, 계좌 이체시에는 사용하지 않음 -->
<input type=hidden name=allat_zerofee_yn value="<?=$g_cardsettle_ZeroFee?>">		<!-- 일반/무이자 할부 사용여부 : 일반(N), 무이자 할부(Y) -->
<input type=hidden name=KVP_QUOTA value="">											<!-- 할부개월값 : 자동설정 -->
<input type=hidden name=allat_bonus_yn value="<?=$g_cardsettle_Bonus?>">			<!-- 보너스포인트 사용 여부 : 사용(Y), 사용 않음(N) - Default : N -->
<input type=hidden name=allat_cardcert_yn value="<?=$g_cardsettle_Cert?>">			<!-- 카드 인증 여부 :  : 인증(Y), 인증 사용않음(N), 인증만 사용(X) -->

<!------- ISP : ISP 인터페이스 호출시 필요한 정보 및 리턴값 -->
<input type=hidden name="KVP_QUOTA_INF" value="<?=$g_cardsettle_Quota?>">			<!-- 일반할부 개월 수 : (0:2:3:4:5:6:7:8:9:10:11:12)-->
<input type=hidden name="KVP_PGID" value="A0024">									<!-- 기관코드 : 고정값 (A0024) -->
<input type=hidden name="KVP_CURRENCY" value="WON">									<!-- 화폐단위 : 고정값 (WON)-->
<input type=hidden name="KVP_OACERT_INF" value="NONE">								<!-- 공인 인증서 적용 최소 금액 -->
<input type=hidden name="KVP_NOINT_INF" value="">									<!-- 무이자 할부정보 : 자동설정 -->
<input type=hidden name="KVP_GOODNAME" value="">									<!-- 상품명 : 자동설정 -->
<input type=hidden name="KVP_PRICE" value="">										<!-- 승인금액 : 자동설정 -->
<input type=hidden name="KVP_NOINT" value="">										<!-- 무이자 할부값 : 자동설정 -->
<input type=hidden name="KVP_CARDCODE" value="">									<!-- KVP 카드코드 : 자동설정 -->
<input type=hidden name="KVP_CONAME" value="">										<!-- 제휴사명 : 자동설정 -->
<input type=hidden name="KVP_IMGURL" value="">										<!-- 고정값 -->
<input type=hidden name="KVP_CARD_PREFIX" value="">									<!-- 카드 PREFIX : 자동설정 -->
<input type=hidden name="KVP_SESSIONKEY" value="">									<!-- Session Key : 자동설정 -->
<input type=hidden name="KVP_ENCDATA" value="">										<!-- Encryption Data : 자동설정 -->
<input type=hidden name="KVP_RESERVED1" value="">									<!-- 예약필드1 -->
<input type=hidden name="KVP_RESERVED2" value="">									<!-- 예약필드2 -->
<input type=hidden name="KVP_RESERVED3" value="">									<!-- 예약필드3 -->

<!-- 계좌이체 서비스시 추가 -->

<input type=hidden name=allat_tax_yn value="EFT">									<!-- 과세여부 -->
<input type=hidden name=hd_pre_msg_type value="EFT">								<!-- 고정값 : EFT -->
<input type=hidden name=hd_msg_code value="0200">									<!-- 고정값 : 0200 -->
<input type=hidden name=hd_msg_type value="EFT">									<!-- 요청전문 : 고정값 : EFT-->
<input type=hidden name=hd_ep_type value="">										<!-- 인증서 로그인 : 자동설정 -->
<input type=hidden name=hd_pi value="">												<!-- 암호화된 값 : 자동설정 -->
<input type=hidden name=hd_approve_no value="20000035">								<!-- 기관코드 : 고정값 : 20000035 -->
<input type=hidden name=hd_serial_no value="">										<!-- 거래구분번호 : 자동설정 -->
<input type=hidden name=hd_firm_name value="<?=$g_mallnm?>">						<!-- 쇼핑몰 이름 -->
<input type=hidden name=tx_amount value="">											<!-- 승인요청금액 : 자동설정 -->

<table width="100%" border="0" cellspacing="0" cellpadding="6" style="border:1px #f3f3f3 solid">
  <tr>
    <td bgcolor="fafafa">
      <table width="100%" border="0" cellspacing="0" cellpadding="8">
        <tr>
          <td width="100" align="right" valign="top"><font color="" size="3"><b><?=$settlekindStr?><?=$escrowUseStr?><br>정보입력</b></font></td>
          <td valign="top" bgcolor="#FFFFFF" style="padding-left:12px">

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26" valign="top" style="padding-top:4px">결제선택</td>
                <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <? if($settlekindType == "Y"){	// 신용카드 ?>
                    <tr>
                      <td><input type=radio name="chkapp" value="ISP" onclick="javascript:chk_app('ISP')"> <b>ISP 결제</b></td>
                    </tr>
                    <tr>
                      <td style="letter-spacing:-1;padding-left:24px">국민, BC, 우리(평화)</td>
                    </tr>
                    <tr>
                      <td><input type=radio name="chkapp" value="C3D" onclick="javascript:chk_app('C3D')"> <b>안심클릭</b></td>
                    </tr>
                    <tr>
                      <td style="letter-spacing:-1;padding-left:24px">삼성, LG, 외환, 신한, 현대, 롯데, 하나, 한미, 조흥(강원), 신세계, 전북, 광주, 제주, 수협, 시티</td>
                    </tr>
                    <tr>
                      <td><input type=radio name="chkapp" value="NOR" onclick="javascript:chk_app('NOR')"> <b>일반결제</b></td>
                    </tr>
                    <tr>
                      <td style="letter-spacing:-1;padding-left:24px">산업, 농협, 축협, 해외(VISA, MASTER, JCB, AMEX, DINERS)</td>
                    </tr>
                    <?}else if($settlekindType == "N"){			// 계좌이체 ?>
                    <tr>
                      <td><input type=radio name="chkapp" value="ABANK" onclick="javascript:chk_app('ABANK')" checked> <b>계좌이체</b></td>
                    </tr>
                    <?}?>
                    <tr>
                      <td height="8"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <?if($escrowUseYN == "Y"){?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">에스크로 인증번호</td>
                <td><input type="text" name="allat_escrow_no" value="" maxlength="10" size="10" class="box"> 에스크로 확인시에 사용함 (4~10자리 숫자,영문허용)</td>
              </tr>
            </table>
            <?}?>
            <div id="NOR_3D" style="{display:none}">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">카드번호</td>
                <td><input type="text" name="allat_card_no" value="" maxlength="16" size="20" class="box"> ("-"없이 숫자만 입력)</td>
              </tr>
              <tr>
                <td height="26">할부개월 수</td>
                <td>
                  <select name="allat_sell_mm">
<?
		$QuotaArr	= explode(":",$g_cardsettle_Quota);
		$QuotaCnt	= sizeof($QuotaArr);
		if($totalPrice < 50000) $QuotaCnt = 1;

		for($ii = 0; $ii < $QuotaCnt; $ii++){
			if(strlen($QuotaArr[$ii]) < 2){
				$QuotaArr[$ii] = "0".$QuotaArr[$ii];
			}
			if($QuotaArr[$ii] == "00"){
				$QuotaArrStr = "일시불";
			}else{
				$QuotaArrNum = $QuotaArr[$ii];
				$QuotaArrStr = $QuotaArrNum . " 개월";
			}
			echo"<option value='".$QuotaArr[$ii]."'>".$QuotaArrStr."</option>";
		}
?>
                  </select> (5만원이상 가능)
                </td>
              </tr>
            </table>
            </div>
            <div id="NOR" style="{display:none}">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">카드유효기간</td>
                <td><input type="text" name="allat_cardvalid_ym" value="" maxlength="4" size="6" class="box"> (년월 예)2006년 04월 이면 0604)</td>
              </tr>
              <tr>
                <td height="26">카드비밀번호</td>
                <td><input type="text" name="allat_passwd_no" value="" maxlength="2" size="4" class="box">XX (앞 두자리를 입력해 주세요)</td>
              </tr>
              <tr>
                <td height="26">개인/법인 구분</td>
                <td>
                  <input type="radio" name="allat_business" value="0" checked> 개인
                  <input type="radio" name="allat_business" value="1"> 법인
                  <input type=hidden name=allat_business_type value="">
                </td>
              </tr>
              <tr>
                <td height="26">주민번호</td>
                <td>&nbsp;XXXXXX - <input type="text" name="allat_registry_no" value="" maxlength="7" size="10" class="box"> (뒷자라만 입력) ※ 개인인 경우
                </td>
              </tr>
              <tr>
                <td height="26">사업자번호</td>
                <td><input type="text" name="allat_biz_no" value="" maxlength="30" size="24" class="box"> ("-"없이 숫자만 입력) ※ 법인인 경우
                </td>
              </tr>
            </table>
            </div>
            <div id="ABANK" style="{display:none}">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">예금주명</td>
                <td><input type="text" name="allat_account_nm" value="" maxlength="30" size="16" class="box"></td>
              </tr>
              <? if($g_cardsettle_Tax == "Y"){	// 현금영수증 신청 여부 ?>
              <tr>
                <td height="26">현금영수증 등록</td>
                <td>
                  <input type="radio" name="allat_cash" value="Y" checked> 등록
                  <input type="radio" name="allat_cash" value="N"> 미등록
                  <input type=hidden name=allat_cash_yn value="">
                </td>
              </tr>
              <?}else{?>
              <input type=hidden name=allat_cash_yn value="N">
              <?}?>
              <tr>
                <td height="26">인증정보</td>
                <td><input type="text" name="allat_cert_no" value="" maxlength="13" size="16" class="box"> ※ 핸드폰번호 OR 주민번호 OR 사업자번호 ("-"없이 숫자만 입력)</td>
              </tr>
            </table>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">결제금액</td>
                <td><font color="EC6F4F"><strong><?=number_format($totalPrice)?>원</strong></font></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

    </td>
  </tr>
</table>
<table width=100% cellpadding=0 cellspacing=0 border=0 height="60">
  <tr>
    <td align=center>
      <input type=button value="결제하기" Onclick="javascript:ftn_app(document.fm);" class=xx>&nbsp;&nbsp;
      <input type=button value="취소하기" Onclick="javascript:location.replace('<?=$curr_path?>card/pg_cancel.php?ordno=<?=$ordno?>&etc_CardMode=<?=$etc_CardMode?>');" class=xx>
    </td>
  </tr>
</table>
</form>
<? if($settlekindScript == "Y"){			// 계좌이체 ?>
<script language=Javascript>
chk_app('ABANK');
</script>
<?}?>