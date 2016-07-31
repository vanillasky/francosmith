<?php

$location = "모바일샵관리 > 모바일샵 사용여부 설정";
include "../_header.php";
include "../../conf/config.mobileShop.php";

## URL 조립을 위한 기본정보 추출 
$aServerProtocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
$sServerHost = $_SERVER['HTTP_HOST']; 
$sServerPort = ( $_SERVER['SERVER_PORT'] == '80' )? "":":".$_SERVER['SERVER_PORT']; 

## 모바일웹 V1.0 어드민 URL 구하기
$sMobileWebV2AdminURL = $aServerProtocol[0]."://".$sServerHost.$sServerPort."/shop/admin/mobileShop2/mobile_set.php";
$sMobileWebV2UserURL = $aServerProtocol[0]."://".$sServerHost.$sServerPort."/m2";

## 현재 적용된 버전은 버전파일 존재 여부로 확인한다 
$version2_apply_file_name = ".htaccess";

$version2_apply_file_path = dirname(__FILE__)."/../../../m/".$version2_apply_file_name; 

$bCurrent_V2_htaccess = file_exists($version2_apply_file_path);
$bCurrent_V2_applied = false; 
 ## 현재 적용버전을 확인하다 
if ( $bCurrent_V2_htaccess ) {
	$aFileContent = file(dirname(__FILE__)."/../../../m/".$version2_apply_file_name);
	for ($i=0; $i<count($aFileContent); $i++) {
		if (preg_match("/RewriteRule/i", $aFileContent[$i])) {
			break; 
		}
	}
	if ($i == count($aFileContent)) {
		$bCurrent_V2_applied = false; 
	} else {
		$bCurrent_V2_applied = true; 
	}
} else {
	$bCurrent_V2_applied = false;
}

## 원래 처리 로직
if(!$cfgMobileShop['useMobileShop']) $cfgMobileShop['useMobileShop'] = 0;
$checked['useMobileShop'][$cfgMobileShop['useMobileShop']] = 'checked';

if(!$cfgMobileShop['vtype_goods']) $cfgMobileShop['vtype_goods'] = 0;
$checked['vtype_goods'][$cfgMobileShop['vtype_goods']] = 'checked';

if(!$cfgMobileShop['vtype_category']) $cfgMobileShop['vtype_category'] = 0;
$checked['vtype_category'][$cfgMobileShop['vtype_category']] = 'checked';

$selected[tplSkinMobile][$cfgMobileShop['tplSkinMobile']] = 'selected';

{ // 스킨 디렉토리 정의

	$skins = array();

	$skinDir = dirname(__FILE__) . "/../../data/skin_mobile/";
	$odir = @opendir( $skinDir );

	while (false !== ($rdir = readdir($odir))) {
		// 디렉토리인지를 체크
		if(is_dir($skinDir . $rdir) && !in_array($rdir,array('.','..'))){
			$skins[] = $rdir;
		}
	}

	@closedir($odir);

	sort ( $skins );

}
?>

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="config">

<script>
function doMobileVerConvert() {
	var rc = confirm("모바일샵V2.0 으로 전환시 디자인 작업을 미리 하셔야합니다. \n메인상품진열을 하지 않으신 경우, 모바일샵 메인페이지가 진열되는 상품없이 노출될 수 있습니다..\n모바일샵V2.0 전환을 진행할까요?");
	if (rc != true) {
		return;
	}
	// 모바일 2.0 으로 전환 처리를 함.
	document.forms[0].mode.value = 'convert'; 
	document.forms[0].submit();
}

function goMobileAdminV2() {
	window.open('<?=$sMobileWebV2AdminURL?>');
}

function goMobileUserV2() {
	window.open('<?=$sMobileWebV2UserURL?>');
}

</script>

<!-- 현재 적용버전을 나타낸다. -->
<? if ( $bCurrent_V2_applied ) {?>
<div class="title title_top">모바일샵 V2.0 이 적용되어 있습니다 .</div>
<? } else { ?>
<div class="title title_top">모바일샵 V1.0 이 적용되어 있습니다 .</div>
<? } ?>
<!-- 모바일웹 V2.0 선택 화면  -->
<? 
## 모바일웹 V2.0 파일이 있는지 확인한다. 
if ( !$bCurrent_V2_applied ) {?>
<div class="title title_top">모바일샵 V2.0 선택화면 </div>
<table class=tb style='margin-bottom:30px'>
<col class=cellC style='width:160px'><col class=cellL>
<tr>
	<td>모바일샵 V2.0 전환 화면</td>
	<td class="noline">
		현재 모바일샵   V1.0 을 사용 중입니다.  <br>
 
		새로운 모바일샵  V2.0 으로 전환할 수 있습니다. 
		<div class="button">
		<input type="hidden" name="btnConvertV20" value="모바일웹 V2.0 전환" onclick="doMobileVerConvert()" style="width:170px" >
		<img src="../img/btn_convert_to_mobile2.gif"  onclick="doMobileVerConvert()" />
		<input type="hidden" name="btnViewAdminV20" value="모바일웹V2.0 관리자 미리보기" onclick="goMobileAdminV2()" style="width:170px">
		<img src="../img/btn_view_mobile2.adm.gif"  onclick="goMobileAdminV2()" />
		<input type="hidden" name="btnViewFrontV20" value="모바일웹V2.0 사용자 미리보기" onclick="goMobileUserV2()" style="width:170px">
		<img src="../img/btn_view_mobile2.usr.gif"  onclick="goMobileUserV2()" />
		</div> 
		<div>현재 적용되어 있는 모바일샵의 사용자 화면 접근경로는 <span style='font-weight:bold;color:blue'>http://도메인/m/</span> 입니다.</div>
		<div>모바일샵 V2.0 으로 전환 시 디자인작업을 다시 하셔야 합니다.</div>
		<div>모바일샵 V2.0 으로 전환하기 전, 모바일샵 2.0 관리자 미리보기로 사용여부설정 및 메인상품진열을 필수로 하셔야 합니다.</div>
		<div>모바일샵 V2.0 으로 전환하기 전, 사용자화면은 <span style='font-weight:bold'>PC브라우저</span> 혹은  <span style='font-weight:bold'>스마트폰</span>에서 <span style='font-weight:bold;color:red'>http://도메인/m2/</span> 로 접근 가능합니다. </div>
		<div>모바일샵 V2.0 으로 전환 후,  다시 원래 모바일버전인 모바일 V1.0 으로 전환이 가능합니다. </div>
		<div>모바일샵 V2.0 으로 전환 후,  모바일 V1.0 접속이 안됩니다. </div>
	</td>
</tr>
</table>
<? } ?>

<div class="title title_top">모바일샵 V1 사용여부 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>모바일샵V1  사용여부</td>
	<td class="noline">
		<input type="radio" name="useMobileShop" value="1" <?=$checked['useMobileShop'][1]?> />사용 <input type="radio" name="useMobileShop" value="0" <?=$checked['useMobileShop'][0]?> />미사용
		<span class="small"><font class="extext">모바일샵 사용여부를 설정합니다.</font></span>
	</td>
</tr>
<tr>
	<td>스킨선택</td>
	<td>
		<select name="tplSkinMobile">
		<?php foreach($skins as $row){?>
		<option value="<?php echo $row;?>" <?=$selected[tplSkinMobile][$row]?>><?php echo $row;?></option>
		<?php }?>
		</select>
	</td>
</tr>
<tr>
	<td>로고등록</td>
	<td>
		<input type="file" name="mobileShopLogo_up" size="50" class=line><input type="hidden" name="mobileShopLogo" value="<?=$cfgMobileShop[mobileShopLogo]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopLogo] != '' ? '/data/skin_mobile/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopLogo] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopLogo] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopLogo_del" value="Y">삭제</span><? } ?>
		<span class="small"><font class="extext">(기본사이즈 : 110px * 35px, 세로길이는 자유롭게 적용 가능합니다.)</font></span>
	</td>
</tr>
<tr>
	<td>아이콘등록</td>
	<td>
		<input type="file" name="mobileShopIcon_up" size="50" class=line><input type="hidden" name="mobileShopIcon" value="<?=$cfgMobileShop[mobileShopIcon]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopIcon] != '' ? '/data/skin_mobile/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopIcon] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopIcon] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopIcon_del" value="Y">삭제</span><? } ?>
		<span class="small"><font class="extext">(기본사이즈 : 32px * 32px)</font></span>
	</td>
</tr>
<tr>
	<td>메인배너이미지등록</td>
	<td>
		<input type="file" name="mobileShopMainBanner_up" size="50" class=line><input type="hidden" name="mobileShopMainBanner" value="<?=$cfgMobileShop[mobileShopMainBanner]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopMainBanner] != '' ? '/data/skin_mobile/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopMainBanner] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopMainBanner] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopMainBanner_del" value="Y">삭제</span><? } ?>
		<span class="small"><font class="extext">(기본사이즈 : 300px * 50px, 세로길이는 자유롭게 적용 가능합니다.)</font></span>
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>