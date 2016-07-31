<?php

$location = "모바일샵관리 > 모바일샵 노출 설정";
include "../_header.php";
include "../../conf/config.mobileShop.php";

$goodsDisplay = Core::loader('Mobile2GoodsDisplay');

if ($goodsDisplay->displayTypeIsSet() === false) {
	if ($goodsDisplay->isInitStatus()) {
		$goodsDisplay->saveMainDisplayType('pc');
		$cfgMobileShop['vtype_main'] = 'pc';
	}
	else {
		$goodsDisplay->saveMainDisplayType('mobile');
		$cfgMobileShop['vtype_main'] = 'mobile';
	}
}

if(!$cfgMobileShop['useMobileShop']) $cfgMobileShop['useMobileShop'] = 0;
$checked['useMobileShop'][$cfgMobileShop['useMobileShop']] = 'checked';

if(!$cfgMobileShop['vtype_main']) $cfgMobileShop['vtype_main'] = 'mobile';
$checked['vtype_main'][$cfgMobileShop['vtype_main']] = 'checked';

if(!$cfgMobileShop['vtype_goods']) $cfgMobileShop['vtype_goods'] = 0;
$checked['vtype_goods'][$cfgMobileShop['vtype_goods']] = 'checked';

if(!$cfgMobileShop['vtype_category']) $cfgMobileShop['vtype_category'] = 0;
$checked['vtype_category'][$cfgMobileShop['vtype_category']] = 'checked';

if($cfgMobileShop['vtype_goods_view_skin'] != 0 ) $cfgMobileShop['vtype_goods_view_skin'] = 1;
$checked['vtype_goods_view_skin'][$cfgMobileShop['vtype_goods_view_skin']] = 'checked';

if($cfgMobileShop['goods_view_quick_menu_useyn'] !== 'n' ) $cfgMobileShop['goods_view_quick_menu_useyn'] = 'y';
$checked['goods_view_quick_menu_useyn'][$cfgMobileShop['goods_view_quick_menu_useyn']] = 'checked';

$selected[tplSkinMobile][$cfgMobileShop['tplSkinMobile']] = 'selected';

{ // 스킨 디렉토리 정의

	$skins = array();

	$skinDir = dirname(__FILE__) . "/../../data/skin_mobileV2/";
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

<style type="text/css">
a.extext:hover{
	color: #000000;
}
</style>

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="config_view_set">

<div class="title title_top">모바일샵 노출 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>메인 상품 진열<br/>노출 설정</td>
	<td class="noline">
		<div>
			<input type="radio" name="vtype_main" value="pc" id="vtype-main-pc" <?php echo $checked['vtype_main']['pc']; ?>/>
			<label for="vtype-main-pc">온라인 쇼핑몰(PC버전)과 동일하게 메인 상품진열 적용</label><br/>
			<span class="extext">* 디스플레이 유형은 "상품스크롤형"으로 적용되어 출력됩니다.</span>
		</div>
		<div style="margin-top: 7px;">
			<input type="radio" name="vtype_main" value="mobile" id="vtype-main-mobile" <?php echo $checked['vtype_main']['mobile']; ?>/>
			<label for="vtype-main-mobile">모바일샵 별도 메인 상품진열 적용</label><br/>
			<span class="extext">* <a href="<?php echo $cfg['rootDir']; ?>/admin/mobileShop2/disp_main.php" class="extext">[모바일샵 진열설정 > 모바일샵 메인 상품진열]</a>에서 별도 메인상품진열을 설정, 관리 합니다.</span>
		</div>
	</td>
</tr>
<tr>
	<td>상품 노출</td>
	<td class="noline">
		<input type="radio" name="vtype_goods" value="0" <?=$checked['vtype_goods'][0]?> />온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용<br />
		<input type="radio" name="vtype_goods" value="1" <?=$checked['vtype_goods'][1]?> />모바일샵 별도 노출설정 적용
	</td>
</tr>
<tr>
	<td>카테고리 노출</td>
	<td class="noline">
		<input type="radio" name="vtype_category" value="0" <?=$checked['vtype_category'][0]?> />온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용<br />
		<input type="radio" name="vtype_category" value="1" <?=$checked['vtype_category'][1]?> />모바일샵 별도 노출설정 적용
		<br />
		<font class="extext">* 카테고리 노출 여부만 따로 설정할 수 있습니다.<br />
		"모바일샵 별도 노출설정 적용" 선택 등록 후에 [상품관리>상품분류(카테고리)관리] 에서 "모바일샵에서 감추기"를 선택하세요.<br />
		나머지 카테고리 관리 설정 기능들은 온라인 쇼핑몰과 동일하게 모바일샵에 적용됩니다.
		</font>
	</td>
</tr>

<tr>
	<td>상품 상세페이지 <br />스킨유형 선택<br />(default 스킨)</td>
	<td class="noline">
		<input type="radio" name="vtype_goods_view_skin" value="0" <?=$checked['vtype_goods_view_skin'][0]?> />기존 V2 default 스킨<br />
		<input type="radio" name="vtype_goods_view_skin" value="1" <?=$checked['vtype_goods_view_skin'][1]?> />신규 V2 default_upgrade 스킨 <br />
		<font class=extext>* default 스킨 및 업로드 하신 default(업로드 하신 이름에 따라 스킨 이름은 다를수 있습니다.) 스킨을 사용 하고 있을 경우에만 스킨유형 선택이 가능합니다. 다른 스킨이나 디자인 변경을 위해 복사한 스킨에서는 적용되지 않습니다.</font>
	</td>
</tr>
<tr>
	<td>
		상품 상세정보 보기<br/>
		퀵메뉴 사용
	</td>
	<td class="noline">
		<input id="goods-view-quick-menu-useyn-n" type="radio" name="goods_view_quick_menu_useyn" value="n" <?php echo $checked['goods_view_quick_menu_useyn']['n']; ?>/>
		<label for="goods-view-quick-menu-useyn-n">사용안함</label>
		<input id="goods-view-quick-menu-useyn-y" type="radio" name="goods_view_quick_menu_useyn" value="y" style="margin-left: 10px;" <?php echo $checked['goods_view_quick_menu_useyn']['y']; ?>/>
		<label for="goods-view-quick-menu-useyn-y">사용</label>
		<div class="extext">default 스킨은 "상품 상세정보 보기 퀵메뉴" 기능이 지원되지 않습니다.</div>
	</td>
</tr>
</table>
<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>