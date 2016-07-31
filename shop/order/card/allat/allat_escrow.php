<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005,  Flyfox All right reserved.
@파일내용: All@Pay™ Plus 2.0 (Version 1.0.0.5) 에스크로 서비스 배송등록 [2006-04-06]
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

# 라이브러리 화일 호출
include_once "../../lib/library.php";

$curr_path=rootpath()."shop/";
$rootpath_cut1=substr($rootpath,sizeOf($rootpath)-1,-1);

session_start();

//-- 쇼핑몰기본설정관련
include $curr_path."conf/public_configure.php"; 
?>
<?=$license_topview?>
<html>
<title><?=$g_malltitle?></title>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<?if($getUseSTYLESHEET[stylesheet]==""){?>
<link rel="styleSheet" Href="<?=$rootpath?>shop/css/css.css">
<?}else{?>
<style type="text/css">
<?=$getUseSTYLESHEET[stylesheet];?>
</style>
<?}?>
</META>
</head>
<?
{	# All@Pay™ Plus 기본 설정
	$strSQL		= "SELECT allat FROM tb_addmallinfo WHERE sno='1'";
	$getData	= getinfo($strSQL);
	$g_cardsettle_ID			=$getData[allat];				# All@Pay ID
	$g_cardsettle_test			="N";							# 테스트 여부 - 테스트(Y),서비스(N)
	
	$Domain_url	="http://".$_SERVER['HTTP_HOST']."/";
	$reconPage	=$Domain_url."shop/card/allat/allat_approval_escrow.php";		// 결제처리 페이지
}

{	# 주문 정보 설정
	
	# 주문 정보 호출
	$strSQL = "SELECT TidNo,escrowInvno,escrowTrans FROM tb_order WHERE ordno='".$_GET['ordno']."'";
	$getData=getinfo( $strSQL );
	
	# 페이징 by Baberina 2005.09.05
	$PageIndexing		= "tic=".time()."&ordno=$ordno";
	$PageIndexing		= $_SERVER['PHP_SELF']."?".$PageIndexing;
}

//	if(dfm.allat_seq_no.value == ""){
//		alert("거래번호가 빠졌습니다.")
//		return;
//	}
//	if(dfm.allat_escrow_send_no.value == ""){
//		alert("운송장번호가 빠졌습니다.")
//		return;
//	}
//	if(dfm.allat_escrow_express_nm.value == ""){
//		alert("택배사이름이 빠졌습니다.")
//		return;
//	}
//
?>
<script language=JavaScript src="https://tx.allatpay.com/common/allatpayX.js"></script>
<script language="Javascript">
function ftn_escrowcheck(dfm) {
	var ret;
	
	ret = invisible_eschk(dfm);//Function 내부에서 submit을 하게 되어있음.
	if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
		// 오류 코드 : 0001~9998 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
		alert(ret.substring(4,ret.length));		// Message 가져오기
	}
	if( ret.substring(0,4)=="9999" ){
		// 오류 코드 : 9999 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
		alert(ret.substring(8,ret.length));	    // Message 가져오기	  	
	}
}
</script>
<table width="100%" border=0 align="center" cellpadding=6 cellspacing=0>
<form name=fm method=post action="<?=$reconPage?>">
<input type=hidden name=allat_shop_id value="<?=$g_cardsettle_ID?>">	<!--상점 아이디-->
<input type=hidden name=allat_order_no value="<?=$_GET['ordno']?>">		<!--주문번호 : 배송등록할 원거래건의 주문번호-->
<input type=hidden name=allat_pay_type value="ABANK">					<!--원거래건의 결제방식 : 카드(CARD), 계좌이체(ABANK) -> 현재, 에스크로는 계좌이체만 적용됨-->
<input type=hidden name=allat_enc_data value="">						<!--주문정보암호화필드-->
<input type=hidden name=allat_opt_pin value="NOVIEW">					<!--올앳참조필드-->
<input type=hidden name=allat_opt_mod value="WEB">						<!--올앳참조필드-->
<input type=hidden name=allat_test_yn value="<?=$g_cardsettle_test?>">	<!-- 테스트 여부 : 테스트(Y),서비스(N) -->
<input type=hidden name=returnOrderUrl value="<?=$PageIndexing?>">		<!--리턴스크립트 : 추가한것임-->
<input type=hidden name=etc_CardMode value="<?=$etc_CardMode?>">					<!-- 일반결제, 재결제, 기타결제용 구분 필드 - 추가해준것임 -->
  <tr> 
    <td height="40" colspan="6"  class="m_a"><img src="<?=$curr_path?>admin/images_prime/no1.gif" width="23" height="17" align="absbottom"><font color="595147"><b>에스크로 배송등록</b></font></td>
  </tr>
  <tr><td height="1" colspan="6" bgcolor="#DDDDDD"></td></tr>
  <tr> 
    <td width="15%" bgcolor="#F5F2F0">&nbsp;<img src="<?=$curr_path?>admin/images_prime/icon_03.gif" width="10" height="5" align="absmiddle"> 거래번호</td>
    <td width="15%"> <input name="allat_seq_no" type="text" size="13" class=xx value="<?=$getData['TidNo']?>"></td>
    <td width="15%" bgcolor="#F5F2F0">&nbsp;<img src="<?=$curr_path?>admin/images_prime/icon_03.gif" width="10" height="5" align="absmiddle"> 운송장번호</td>
    <td width="15%"> <input name="allat_escrow_send_no" type="text" size="13" class=xx value="<?=$getData['escrowInvno']?>"></td>
    <td width="15%" bgcolor="#F5F2F0">&nbsp;<img src="<?=$curr_path?>admin/images_prime/icon_03.gif" width="10" height="5" align="absmiddle"> 택배사</td>
    <td width="15%"> <input name="allat_escrow_express_nm" type="text" size="13" class=xx value="<?=$getData['escrowTrans']?>"></td>
  </tr>
  <tr><td height="1" colspan="6" bgcolor="#DDDDDD"></td></tr>
</table>
<table width="100%" border=0 cellPadding=0 cellSpacing=0>
  <TR> 
    <td height=30 align="center"><input type=button value=" 운송장번호 등록요청 " name=app_btn onClick="javascript:ftn_escrowcheck(document.fm);"></TD>
  </tr>
</form>
</TABLE>