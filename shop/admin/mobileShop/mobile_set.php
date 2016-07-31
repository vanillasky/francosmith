<?php

$location = "����ϼ����� > ����ϼ� ��뿩�� ����";
include "../_header.php";
include "../../conf/config.mobileShop.php";

## URL ������ ���� �⺻���� ���� 
$aServerProtocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
$sServerHost = $_SERVER['HTTP_HOST']; 
$sServerPort = ( $_SERVER['SERVER_PORT'] == '80' )? "":":".$_SERVER['SERVER_PORT']; 

## ������� V1.0 ���� URL ���ϱ�
$sMobileWebV2AdminURL = $aServerProtocol[0]."://".$sServerHost.$sServerPort."/shop/admin/mobileShop2/mobile_set.php";
$sMobileWebV2UserURL = $aServerProtocol[0]."://".$sServerHost.$sServerPort."/m2";

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

## ���� ó�� ����
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
<input type=hidden name=mode value="config">

<script>
function doMobileVerConvert() {
	var rc = confirm("����ϼ�V2.0 ���� ��ȯ�� ������ �۾��� �̸� �ϼž��մϴ�. \n���λ�ǰ������ ���� ������ ���, ����ϼ� ������������ �����Ǵ� ��ǰ���� ����� �� �ֽ��ϴ�..\n����ϼ�V2.0 ��ȯ�� �����ұ��?");
	if (rc != true) {
		return;
	}
	// ����� 2.0 ���� ��ȯ ó���� ��.
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

<!-- ���� ��������� ��Ÿ����. -->
<? if ( $bCurrent_V2_applied ) {?>
<div class="title title_top">����ϼ� V2.0 �� ����Ǿ� �ֽ��ϴ� .</div>
<? } else { ?>
<div class="title title_top">����ϼ� V1.0 �� ����Ǿ� �ֽ��ϴ� .</div>
<? } ?>
<!-- ������� V2.0 ���� ȭ��  -->
<? 
## ������� V2.0 ������ �ִ��� Ȯ���Ѵ�. 
if ( !$bCurrent_V2_applied ) {?>
<div class="title title_top">����ϼ� V2.0 ����ȭ�� </div>
<table class=tb style='margin-bottom:30px'>
<col class=cellC style='width:160px'><col class=cellL>
<tr>
	<td>����ϼ� V2.0 ��ȯ ȭ��</td>
	<td class="noline">
		���� ����ϼ�   V1.0 �� ��� ���Դϴ�.  <br>
 
		���ο� ����ϼ�  V2.0 ���� ��ȯ�� �� �ֽ��ϴ�. 
		<div class="button">
		<input type="hidden" name="btnConvertV20" value="������� V2.0 ��ȯ" onclick="doMobileVerConvert()" style="width:170px" >
		<img src="../img/btn_convert_to_mobile2.gif"  onclick="doMobileVerConvert()" />
		<input type="hidden" name="btnViewAdminV20" value="�������V2.0 ������ �̸�����" onclick="goMobileAdminV2()" style="width:170px">
		<img src="../img/btn_view_mobile2.adm.gif"  onclick="goMobileAdminV2()" />
		<input type="hidden" name="btnViewFrontV20" value="�������V2.0 ����� �̸�����" onclick="goMobileUserV2()" style="width:170px">
		<img src="../img/btn_view_mobile2.usr.gif"  onclick="goMobileUserV2()" />
		</div> 
		<div>���� ����Ǿ� �ִ� ����ϼ��� ����� ȭ�� ���ٰ�δ� <span style='font-weight:bold;color:blue'>http://������/m/</span> �Դϴ�.</div>
		<div>����ϼ� V2.0 ���� ��ȯ �� �������۾��� �ٽ� �ϼž� �մϴ�.</div>
		<div>����ϼ� V2.0 ���� ��ȯ�ϱ� ��, ����ϼ� 2.0 ������ �̸������ ��뿩�μ��� �� ���λ�ǰ������ �ʼ��� �ϼž� �մϴ�.</div>
		<div>����ϼ� V2.0 ���� ��ȯ�ϱ� ��, �����ȭ���� <span style='font-weight:bold'>PC������</span> Ȥ��  <span style='font-weight:bold'>����Ʈ��</span>���� <span style='font-weight:bold;color:red'>http://������/m2/</span> �� ���� �����մϴ�. </div>
		<div>����ϼ� V2.0 ���� ��ȯ ��,  �ٽ� ���� ����Ϲ����� ����� V1.0 ���� ��ȯ�� �����մϴ�. </div>
		<div>����ϼ� V2.0 ���� ��ȯ ��,  ����� V1.0 ������ �ȵ˴ϴ�. </div>
	</td>
</tr>
</table>
<? } ?>

<div class="title title_top">����ϼ� V1 ��뿩�� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>����ϼ�V1  ��뿩��</td>
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
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopLogo] != '' ? '/data/skin_mobile/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopLogo] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopLogo] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopLogo_del" value="Y">����</span><? } ?>
		<span class="small"><font class="extext">(�⺻������ : 110px * 35px, ���α��̴� �����Ӱ� ���� �����մϴ�.)</font></span>
	</td>
</tr>
<tr>
	<td>�����ܵ��</td>
	<td>
		<input type="file" name="mobileShopIcon_up" size="50" class=line><input type="hidden" name="mobileShopIcon" value="<?=$cfgMobileShop[mobileShopIcon]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopIcon] != '' ? '/data/skin_mobile/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopIcon] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopIcon] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopIcon_del" value="Y">����</span><? } ?>
		<span class="small"><font class="extext">(�⺻������ : 32px * 32px)</font></span>
	</td>
</tr>
<tr>
	<td>���ι���̹������</td>
	<td>
		<input type="file" name="mobileShopMainBanner_up" size="50" class=line><input type="hidden" name="mobileShopMainBanner" value="<?=$cfgMobileShop[mobileShopMainBanner]?>">
		<a href="javascript:webftpinfo( '<?=( $cfgMobileShop[mobileShopMainBanner] != '' ? '/data/skin_mobile/'.$cfgMobileShop['tplSkinMobile'].'/' . $cfgMobileShop[mobileShopMainBanner] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
		<? if ( $cfgMobileShop[mobileShopMainBanner] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="mobileShopMainBanner_del" value="Y">����</span><? } ?>
		<span class="small"><font class="extext">(�⺻������ : 300px * 50px, ���α��̴� �����Ӱ� ���� �����մϴ�.)</font></span>
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>