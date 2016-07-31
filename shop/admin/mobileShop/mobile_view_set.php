<?php

$location = "모바일샵관리 > 모바일샵 노출 설정";
include "../_header.php";
include "../../conf/config.mobileShop.php";

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
<input type=hidden name=mode value="config_view_set">

<div class="title title_top">모바일샵 노출 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>상품 노출</td>
	<td class="noline">
		<input type="radio" name="vtype_goods" value="0" <?=$checked['vtype_goods'][0]?> />온라인 쇼핑몰과 노출설정 동일하게 적용<br />
		<input type="radio" name="vtype_goods" value="1" <?=$checked['vtype_goods'][1]?> />모바일샵 별도 노출설정 적용
	</td>
</tr>
<tr>
	<td>카테고리 노출</td>
	<td class="noline">
		<input type="radio" name="vtype_category" value="0" <?=$checked['vtype_category'][0]?> />온라인 쇼핑몰과 노출설정 동일하게 적용<br />
		<input type="radio" name="vtype_category" value="1" <?=$checked['vtype_category'][1]?> />모바일샵 별도 노출설정 적용
		<br />
		<font class="extext">* 카테고리 노출 여부만 따로 설정할 수 있습니다.<br />
		"모바일샵 별도 노출설정 적용" 선택 등록 후에 [상품관리>상품분류(카테고리)관리] 에서 "모바일샵에서 감추기"를 선택하세요.<br />
		나머지 카테고리 관리 설정 기능들은 온라인 쇼핑몰과 동일하게 모바일샵에 적용됩니다.
		</font>
	</td>
</tr>
</table>


<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>