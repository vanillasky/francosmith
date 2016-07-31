<?
$location = "모바일샵 디자인관리 > 모바일샵 디자인관리";
include "../_header.php";
include "../../conf/config.mobileShop.php";

// 실명확인 서비스 사용 여부
@include dirname(__FILE__)."/../../conf/fieldset.php";
if($realname['id'] != '' && $realname['useyn'] == 'y') $use_realname = true;
else $use_realname = false;
if($ipin['id'] != '' && $ipin['useyn'] == 'y') $use_ipin = true;
else $use_ipin = false;
if($ipin['nice_useyn'] == 'y' && $ipin['nice_minoryn'] == 'y') $use_niceipin = true;
else $use_niceipin = false;

//휴대폰본인확인 서비스 사용할 시에도 성인인트로 사용가능하게끔 추가 2013-07-26
$hpauth = Core::loader('Hpauth');
$hpauthRequestData = $hpauth->getAdultRequestData();

if($hpauthRequestData['useyn'] =='y') $use_hpauth = true;
else $use_hpauth = false;
if($use_realname || $use_ipin || $use_niceipin || $use_hpauth) $adultro_ready = true;
else $adultro_ready = false;

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_intro";
?>

<script language="javascript"><!--


function fnToggleIntroForm(b) {
	$$('input[name="custom_landingpageMobile"]').each(function(el){
		el.writeAttribute({disabled: !b});
		<? if(!$adultro_ready) { ?>if(el.value == "2") el.writeAttribute({disabled: true});<? } ?>
	});
}

function fnDesignIntroTemplate(t) {

	var url = false;

	switch (t) {
	case 1:			// 기존 인트로
		url = './iframe.codi.php?design_file=intro/intro.htm';
		break;
	case 2:			// 성인
		url = './iframe.codi.php?design_file=intro/intro_adult.htm';
		break;
	case 3:			// 회원
		url = './iframe.codi.php?design_file=intro/intro_member.htm';
		break;
	}

	if (url != false)
	{
		var win = popup_return( url, 'INTRODESIGN', 900, 650, 100, 100, 1 );
		win.focus();
	}
	return;
}

window.onload = function() {
	fnToggleIntroForm(<?=$cfg['introUseYNMobile'] == 'Y' ? 'true' : 'false'?>);

}
--></script>


<form name="fm" method="post" action="indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />
<input type=hidden name=tplSkinMobileWork value="<?=$cfg['tplSkinMobileWork']?>">
<div class="title title_top">인트로/공사중 설정<span>인트로페이지 또는 공사중페이지를 만드실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=4')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>사용여부</td>
	<td class="noline" width=80%>
	<input type="radio" name="introUseYNMobile" value="Y" <?=( $cfg['introUseYNMobile'] == 'Y' ? 'checked' : '' )?> required label="사용여부" onClick="fnToggleIntroForm(true);" /> 사용
	<input type="radio" name="introUseYNMobile" value="N" <?=( $cfg['introUseYNMobile'] != 'Y' ? 'checked' : '' )?> required label="사용여부" onClick="fnToggleIntroForm(false);" /> 사용안함
	</td>
</tr>
</table>

<table border="0" cellpadding="0">
<tr><td height=15 colspan=2></td></tr>
<tr>
	<td><font class=extext>인트로/공사중페이지의 주소는 <font class=ver7 color="#627dce"><b>http://<font class=small1><b>도메인</b></font>/m/intro/intro.php</b></font> 입니다</td>
	<td width=10></td>
	<td><a href="/m/intro/intro.php?tplSkin=<?=$cfg['tplSkinMobileWork']?>" target="_blank"><img src="../img/btn_m_intro.gif"></a></td>
</tr>
<tr><td height=1 colspan=5></td></tr>
<tr>
	<td><font class=extext>쇼핑몰 메인페이지의 주소는 <font class=ver7 color="#627dce"><b>http://<font class=small1><b>도메인</b></font>/m/index.php </b></font> 입니다</td>
	<td width=10></td>
	<td><a href="/m/index.php?tplSkin=<?=$cfg['tplSkinMobileWork']?>" target="_blank"><img src="../img/btn_m_mainpage.gif"></a></td>
</tr>
<tr><td height=15 colspan=2></td></tr>
</table>



<table class=tb>
<tr>
	<td class=cellC style="width:60%;height:30px;">인트로 페이지 사용 설정</td>
	<td class=cellC style="width:25%;">메인 페이지 방문 권한</td>
	<td class=cellC style="width:15%;">페이지 디자인</td>
</tr>
<tr>
	<td class="noline">
		<label><input type="radio" name="custom_landingpageMobile" value="1" <?=$cfg['custom_landingpageMobile'] == 1 ? 'checked' : ''?> />페이지 방문의 제한이 없는 일반적인 인트로 페이지</label>
	</td>
	<td>전체</td>
	<td><a href="javascript:void(0);" onClick="fnDesignIntroTemplate(1);"><img src="../img/btn_view_intro2.gif"></a></td>
</tr>
<tr>
	<td class="noline">
		<label><input type="radio" name="custom_landingpageMobile" value="2" <?=$cfg['custom_landingpageMobile'] == 2 ? 'checked' : ''?> <?=!$adultro_ready ? 'disabled' : ''?> />성인 인증 용 인트로 페이지</label><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(성인인증은 본인확인 인증서비스(예 : 아이핀 등)을 이용하여 주세요.) <img src="../img/<?=(!$adultro_ready ? 'btn_nouse.gif' : 'btn_on_func.gif') ?>" align="absmiddle">
	</td>
	<td>성인</td>
	<td><a href="javascript:void(0);" onClick="fnDesignIntroTemplate(2);"><img src="../img/btn_view_intro2.gif"></a></td>
</tr>
<tr>
	<td class="noline"><label><input type="radio" name="custom_landingpageMobile" value="3" <?=$cfg['custom_landingpageMobile'] == 3 ? 'checked' : ''?> />메인페이지 접속이 회원만 접근 가능한 인트로 페이지</label></td>
	<td>회원</td>
	<td><a href="javascript:void(0);" onClick="fnDesignIntroTemplate(3);"><img src="../img/btn_view_intro2.gif"></a></td>
</tr>
</table>

<p/>

<!--<div style="margin:10px 0 10px 0;"><font class=extext>공사중 페이지를 보려면 '<a href="/shop/main/intro.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://도메인명</u></b></font></a>' 을 클릭하세요.</div>
<div style="margin:10px 0 10px 0;"><font class=extext>메인페이지를 보려면 '<a href="/shop/main/index.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://도메인명/shop/main/index.php</u></b></font></a>' 를 클릭하세요.</div>-->




<div style="padding:20px" align="center">
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class=small_ex>
<tr><td>모바일샵에서 사용할 인트로 페이지 또는 공사중 페이지를 설정할 수 있습니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>성인인증 인트로 페이지는 유형에 따라 2종류로 제공됩니다.</td></tr>
<tr><td>① 메인 페이지 접속이 성인만 접근 가능한 인트로 페이지</td></tr>
<tr><td>&nbsp;- 성인만 접근이 가능한 사이트에 사용됩니다.</td></tr>
<tr>
	<td>&nbsp;- 성인을 인증할 수 있는 본인확인 인증서비스를 신청하고 이용하여 주세요. <a href="../member/adm_member_auth.hpauthDream.php" class=small_ex>[휴대폰 본인확인 관리]</a> <a href="../member/ipin_new.php" class=small_ex>[아이핀 관리]</a></td></tr>
<tr><td>② 메인 페이지 접속이 회원만 접근 가능한 인트로 페이지</td></tr>
<tr><td>&nbsp;- 회원만 접근이 가능한 사이트에 사용되며, 상품 구매는 회원만 가능합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>

<? include "../_footer.php"; ?>