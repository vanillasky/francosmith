<?php

$location = "����ϼ����� > ����ϼ� ���� ����";
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

{ // ��Ų ���丮 ����

	$skins = array();

	$skinDir = dirname(__FILE__) . "/../../data/skin_mobileV2/";
	$odir = @opendir( $skinDir );

	while (false !== ($rdir = readdir($odir))) {
		// ���丮������ üũ
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

<div class="title title_top">����ϼ� ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���� ��ǰ ����<br/>���� ����</td>
	<td class="noline">
		<div>
			<input type="radio" name="vtype_main" value="pc" id="vtype-main-pc" <?php echo $checked['vtype_main']['pc']; ?>/>
			<label for="vtype-main-pc">�¶��� ���θ�(PC����)�� �����ϰ� ���� ��ǰ���� ����</label><br/>
			<span class="extext">* ���÷��� ������ "��ǰ��ũ����"���� ����Ǿ� ��µ˴ϴ�.</span>
		</div>
		<div style="margin-top: 7px;">
			<input type="radio" name="vtype_main" value="mobile" id="vtype-main-mobile" <?php echo $checked['vtype_main']['mobile']; ?>/>
			<label for="vtype-main-mobile">����ϼ� ���� ���� ��ǰ���� ����</label><br/>
			<span class="extext">* <a href="<?php echo $cfg['rootDir']; ?>/admin/mobileShop2/disp_main.php" class="extext">[����ϼ� �������� > ����ϼ� ���� ��ǰ����]</a>���� ���� ���λ�ǰ������ ����, ���� �մϴ�.</span>
		</div>
	</td>
</tr>
<tr>
	<td>��ǰ ����</td>
	<td class="noline">
		<input type="radio" name="vtype_goods" value="0" <?=$checked['vtype_goods'][0]?> />�¶��� ���θ�(PC����)�� ���⼳�� �����ϰ� ����<br />
		<input type="radio" name="vtype_goods" value="1" <?=$checked['vtype_goods'][1]?> />����ϼ� ���� ���⼳�� ����
	</td>
</tr>
<tr>
	<td>ī�װ� ����</td>
	<td class="noline">
		<input type="radio" name="vtype_category" value="0" <?=$checked['vtype_category'][0]?> />�¶��� ���θ�(PC����)�� ���⼳�� �����ϰ� ����<br />
		<input type="radio" name="vtype_category" value="1" <?=$checked['vtype_category'][1]?> />����ϼ� ���� ���⼳�� ����
		<br />
		<font class="extext">* ī�װ� ���� ���θ� ���� ������ �� �ֽ��ϴ�.<br />
		"����ϼ� ���� ���⼳�� ����" ���� ��� �Ŀ� [��ǰ����>��ǰ�з�(ī�װ�)����] ���� "����ϼ����� ���߱�"�� �����ϼ���.<br />
		������ ī�װ� ���� ���� ��ɵ��� �¶��� ���θ��� �����ϰ� ����ϼ��� ����˴ϴ�.
		</font>
	</td>
</tr>

<tr>
	<td>��ǰ �������� <br />��Ų���� ����<br />(default ��Ų)</td>
	<td class="noline">
		<input type="radio" name="vtype_goods_view_skin" value="0" <?=$checked['vtype_goods_view_skin'][0]?> />���� V2 default ��Ų<br />
		<input type="radio" name="vtype_goods_view_skin" value="1" <?=$checked['vtype_goods_view_skin'][1]?> />�ű� V2 default_upgrade ��Ų <br />
		<font class=extext>* default ��Ų �� ���ε� �Ͻ� default(���ε� �Ͻ� �̸��� ���� ��Ų �̸��� �ٸ��� �ֽ��ϴ�.) ��Ų�� ��� �ϰ� ���� ��쿡�� ��Ų���� ������ �����մϴ�. �ٸ� ��Ų�̳� ������ ������ ���� ������ ��Ų������ ������� �ʽ��ϴ�.</font>
	</td>
</tr>
<tr>
	<td>
		��ǰ ������ ����<br/>
		���޴� ���
	</td>
	<td class="noline">
		<input id="goods-view-quick-menu-useyn-n" type="radio" name="goods_view_quick_menu_useyn" value="n" <?php echo $checked['goods_view_quick_menu_useyn']['n']; ?>/>
		<label for="goods-view-quick-menu-useyn-n">������</label>
		<input id="goods-view-quick-menu-useyn-y" type="radio" name="goods_view_quick_menu_useyn" value="y" style="margin-left: 10px;" <?php echo $checked['goods_view_quick_menu_useyn']['y']; ?>/>
		<label for="goods-view-quick-menu-useyn-y">���</label>
		<div class="extext">default ��Ų�� "��ǰ ������ ���� ���޴�" ����� �������� �ʽ��ϴ�.</div>
	</td>
</tr>
</table>
<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>