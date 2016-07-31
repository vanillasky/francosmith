<?php

$location = "����ϼ����� > ����ϼ� ��뿩�� ����";
include "../_header.php";
include "../../conf/config.mobileShop.php";

## URL ������ ���� �⺻���� ���� 
$aServerProtocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
$sServerHost = $_SERVER['HTTP_HOST']; 
$sServerPort = ( $_SERVER['SERVER_PORT'] == '80' )? "":":".$_SERVER['SERVER_PORT']; 

## ������� V1.0 ���� URL ���ϱ�
$sMobileWebV1AdminURL = $aServerProtocol[0]."://".$sServerHost.$sServerPort."/shop/admin/mobileShop/mobile_set.php";

## ���� ����� ������ �������� ���� ���η� Ȯ���Ѵ� 
$version2_apply_file_name = ".htaccess";

$version2_apply_file_path = dirname(__FILE__)."/../../../m/".$version2_apply_file_name; 

$bCurrent_V2_htaccess = file_exists($version2_apply_file_path);
$bCurrent_V2_applied = false; 
 ## ���� ��������� Ȯ���ϴ� 
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
/////////////////////////////////////////////////////////////////////////////////

## ���� ó�� ����
if(!$cfgMobileShop['useMobileShop']) $cfgMobileShop['useMobileShop'] = 0;
$checked['useMobileShop'][$cfgMobileShop['useMobileShop']] = 'checked';

if(!$cfgMobileShop['useOffCanvas']) $cfgMobileShop['useOffCanvas'] = 0;
$checked['useOffCanvas'][$cfgMobileShop['useOffCanvas']] = 'checked';

if(!$cfgMobileShop['vtype_goods']) $cfgMobileShop['vtype_goods'] = 0;
$checked['vtype_goods'][$cfgMobileShop['vtype_goods']] = 'checked';

if(!$cfgMobileShop['vtype_category']) $cfgMobileShop['vtype_category'] = 0;
$checked['vtype_category'][$cfgMobileShop['vtype_category']] = 'checked';

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

## URL ������ ���� �⺻���� ���� 
$aServerProtocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
$sServerHost = $_SERVER['HTTP_HOST']; 
$sServerPort = ( $_SERVER['SERVER_PORT'] == '80' )? "":":".$_SERVER['SERVER_PORT']; 

?>

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="config">

<script>
function doMobileVerConvert() {
	var rc = confirm("����ϼ�V1.0 ���� ��ȯ�� ������ ��Ų�� �⺻ default �� �����˴ϴ�. \n�ٸ� ��Ų�� ����ϰ� �������� ��� ����ϼ�V1.0���� ��ȯ �� ����ϼ� �����ΰ������� ����� ��Ų���� �缳���� ���ֽñ� �ٶ��ϴ�.\n����ϼ�V1.0 ��ȯ�� �����ұ��?");
	if (rc != true) {
		return;
	}

	// ����� 2.0 ���� ��ȯ ó���� ��.
	document.forms[0].mode.value = 'convert'; 
	document.forms[0].submit();
}

function goMobileAdminV1() {
	window.open('<?=$sMobileWebV1AdminURL?>');
}

function checkOffcanvasUseColor() {
	var f = document.form;
	var obj = document.getElementById("offCanvasBtnColor").getElementsByTagName("td");
	for(var i in obj) {
		if(f.useOffCanvas[0].checked == true) {
			obj[i].style.display = 'block';
		} else {
			obj[i].style.display = 'none';
		}
	}
}

function openColorTable(idx,bu) {
	var hrefStr = '../proc/help_colortable.php?iconidx='+idx+'&target='+bu;
	var win = popup_return( hrefStr, 'colortable', 400, 400, 600, 200, 0 );
	win.focus();
}

</script>

<!-- ���� ��������� ��Ÿ����. -->
<? if ( $bCurrent_V2_applied ) {?>
<div class="title title_top">����ϼ� V2.0 �� ����Ǿ� �ֽ��ϴ� .</div>
<? } else { ?>
<div class="title title_top">����ϼ� V1.0 �� ����Ǿ� �ֽ��ϴ� .</div>
<? } ?>

<!-- ������� V1.0 ���� ȭ��  -->
<? if ( $bCurrent_V2_applied ) {?>
<div class="title title_top">����ϼ� V1.0 ����ȭ�� </div>
<table class=tb style='margin-bottom:30px'>
<col class=cellC style='width:160px'><col class=cellL>
<tr>
	<td>����ϼ�  V1.0 ��ȯ ����</td>
	<td class="noline">
		���� ����ϼ�  V2.0 �� ��� ���Դϴ�.  <br><br>
		����ϼ�  V1.0 ���� ��ȯ�� �� �ֽ��ϴ�. 
		<div class="button">
		<input type="hidden" name="btnConvertV20" value="������� V1.0 ��ȯ" onclick="doMobileVerConvert()" style="width:170px" >
		<img src="../img/btn_convert_to_mobile1.gif"  onclick="doMobileVerConvert()" />
		<input type="hidden" name="btnViewAdminV20" value="�������V1.0 ������ �̸�����" onclick="goMobileAdminV1()" style="width:170px">
		<img src="../img/btn_view_mobile1.adm.gif"  onclick="goMobileAdminV1()" />
		</div>  
		<div>���� �����Ǿ� �ִ¸���ϼ� �� ����� ȭ�� ���ٰ�δ� <span style='font-weight:bold;color:blue'>http://������/m/</span> �Դϴ�.</div>
		<div>����ϼ� V1.0 ���� ��ȯ�� �����ν�Ų�� default �����˴ϴ�. </div>
		<div>�ٸ���Ų�� ����ϰ� �������� ���, <span style='font-weight:bold;color:blue'>����ϼ�V1.0���� ��ȯ ��, ����ϼ� �����ΰ������� ��Ų�� �缳��</span>���ֽñ� �ٶ��ϴ�.</div>
		<div>����ϼ� V1.0 ���� ��ȯ�ϱ� ��, ����ϼ� 1.0 ������ �̸������ ��뿩�μ��� �� ���λ�ǰ������ �ʼ��� �ϼž� �մϴ�.</div>
		<div>����ϼ� V1.0 �� �����ȭ���� �̸����Ⱑ �Ұ� �մϴ�. </div>
		<div>����ϼ� V1.0 ���� ��ȯ ��,  �ٽ� ����ϼ� V2.0 ���� ��ȯ�� �����մϴ�. </div>
	</td>
</tr>
</table>
<? } ?>

<div class="title title_top">����ϼ� V2 ��뿩�� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>����ϼ� V2 ��뿩��</td>
	<td class="noline">
		<input type="radio" name="useMobileShop" value="1" <?=$checked['useMobileShop'][1]?> />��� <input type="radio" name="useMobileShop" value="0" <?=$checked['useMobileShop'][0]?> />�̻��
		<span class="small"><font class="extext">����ϼ� ��뿩�θ� �����մϴ�.</font></span>
	</td>
</tr>
<tr>
	<td>��Ų����</td>
	<td>
		<select name="tplSkinMobile">
		<?php foreach($skins as $row){?>
		<option value="<?php echo $row;?>" <?=$selected[tplSkinMobile][$row]?>><?php echo $row;?></option>
		<?php }?>
		</select>
	</td>
</tr>
<tr>
	<td>�ΰ���</td>
	<td>
		<input type="file" name="mobileShopLogo_up" size="50" class=line><input type="hidden" name="mobileShopLogo" value="<?=$cfgMobileShop[mobileShopLogo]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopLogo] != '' ? '/data/skin_mobileV2/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopLogo] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopLogo] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopLogo_del" value="Y">����</span><? } ?>
		<span class="small"><font class="extext">(�⺻������ : 110px * 35px. �����ϸ� ��Ÿ���� �ʽ��ϴ�.)</font></span>
	</td>
</tr>
<tr>
	<td>�����ܵ��</td>
	<td>
		<input type="file" name="mobileShopIcon_up" size="50" class=line><input type="hidden" name="mobileShopIcon" value="<?=$cfgMobileShop[mobileShopIcon]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopIcon] != '' ? '/data/skin_mobileV2/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopIcon] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopIcon] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopIcon_del" value="Y">����</span><? } ?>
		<span class="small"><font class="extext">(�⺻������ : 32px * 32px �������� ������� ������ ����� ���ã�� ����� �̿��Ͻ� �� �����ϴ�.)</font></span>
	</td>
</tr>
<tr>
	<td>���ι���̹������</td>
	<td>
		<input type="file" name="mobileShopMainBanner_up" size="50" class=line><input type="hidden" name="mobileShopMainBanner" value="<?=$cfgMobileShop[mobileShopMainBanner]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopMainBanner] != '' ? '/data/skin_mobileV2/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopMainBanner] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopMainBanner] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopMainBanner_del" value="Y">����</span><? } ?>
		<span class="small"><font class="extext">(�⺻������ : 300px * 50px. �����ϸ� ��Ÿ���� �ʽ��ϴ�.)</font></span>
	</td>
</tr>
<tr>
	<td>�����̵� �޴�</td>
	<td>
		<input type="radio" name="useOffCanvas" value="1" <?=$checked['useOffCanvas'][1]?> onclick="checkOffcanvasUseColor()" />��� <input type="radio" name="useOffCanvas" value="0" <?=$checked['useOffCanvas'][0]?> onclick="checkOffcanvasUseColor()" />�̻��
		<span class="small"><font class="extext">����ϼ��� �����̵� �޴� ��뿩�θ� �����մϴ�. (��Ų��ġ�� �Ǿ� �־�� ��� ����)</font></span>
	</td>
</tr>
<tr id="offCanvasBtnColor">
	<td style="display:<?=$checked['useMobileShop'][$cfgMobileShop['useMobileShop']] == 'checked' ? 'block' : 'none'?>;">�����̵� �޴���ư ����</td>
	<td style="display:<?=$checked['useMobileShop'][$cfgMobileShop['useMobileShop']] == 'checked' ? 'block' : 'none'?>;">
		#<input type="text" name="offCanvasBtnColor[<?=get_js_compatible_key($cfgMobileShop['offCanvasBtnColor'])?>]" size="6" maxlength="6" value="<?=$cfgMobileShop['offCanvasBtnColor']?>" />
		<a href="javascript:openColorTable('<?=get_js_compatible_key($cfgMobileShop['offCanvasBtnColor'])?>','offCanvasBtnColor');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="����ǥ ����" align="absmiddle"></a>
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>