<?php

$location = "����ϼ����� > ����ϼ� ���� ����";
include "../_header.php";
include "../../conf/config.mobileShop.php";

if(!$cfgMobileShop['useMobileShop']) $cfgMobileShop['useMobileShop'] = 0;
$checked['useMobileShop'][$cfgMobileShop['useMobileShop']] = 'checked';

if(!$cfgMobileShop['vtype_goods']) $cfgMobileShop['vtype_goods'] = 0;
$checked['vtype_goods'][$cfgMobileShop['vtype_goods']] = 'checked';

if(!$cfgMobileShop['vtype_category']) $cfgMobileShop['vtype_category'] = 0;
$checked['vtype_category'][$cfgMobileShop['vtype_category']] = 'checked';

$selected[tplSkinMobile][$cfgMobileShop['tplSkinMobile']] = 'selected';

{ // ��Ų ���丮 ����

	$skins = array();

	$skinDir = dirname(__FILE__) . "/../../data/skin_mobile/";
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

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="config_view_set">

<div class="title title_top">����ϼ� ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��ǰ ����</td>
	<td class="noline">
		<input type="radio" name="vtype_goods" value="0" <?=$checked['vtype_goods'][0]?> />�¶��� ���θ��� ���⼳�� �����ϰ� ����<br />
		<input type="radio" name="vtype_goods" value="1" <?=$checked['vtype_goods'][1]?> />����ϼ� ���� ���⼳�� ����
	</td>
</tr>
<tr>
	<td>ī�װ� ����</td>
	<td class="noline">
		<input type="radio" name="vtype_category" value="0" <?=$checked['vtype_category'][0]?> />�¶��� ���θ��� ���⼳�� �����ϰ� ����<br />
		<input type="radio" name="vtype_category" value="1" <?=$checked['vtype_category'][1]?> />����ϼ� ���� ���⼳�� ����
		<br />
		<font class="extext">* ī�װ� ���� ���θ� ���� ������ �� �ֽ��ϴ�.<br />
		"����ϼ� ���� ���⼳�� ����" ���� ��� �Ŀ� [��ǰ����>��ǰ�з�(ī�װ�)����] ���� "����ϼ����� ���߱�"�� �����ϼ���.<br />
		������ ī�װ� ���� ���� ��ɵ��� �¶��� ���θ��� �����ϰ� ����ϼ��� ����˴ϴ�.
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