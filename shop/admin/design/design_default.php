<?
if (basename($_SERVER['PHP_SELF']) == 'iframe.default.php'){
	include "../_header.popup.php";
	$popupWin = true;
} else {
	$location = "�⺻���� > ������ ��Ų����";
	include "../_header.php";
}

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_default";

switch ( $_GET['mode'] ){
	case "mod_default":
		$checked['subCategory'][$cfg['subCategory']] = "checked";
		$checked['copyProtect'][$cfg['copyProtect']] = "checked";
		if(!$cfg['shopMainGoodsConf']) $cfg['shopMainGoodsConf'] = "T";
		$checked['shopMainGoodsConf'][$cfg['shopMainGoodsConf']] = "checked";
	break;
}

{ // ��Ų ���丮 ����

	$baseSkin = array( 'standard' );
	$tmp = array( 'b' => array(), 'u' => array() );

	$skinDir = dirname(__FILE__) . "/../../data/skin/";
	$odir = @opendir( $skinDir );

	while (false !== ($rdir = readdir($odir))) {
		// ���丮������ üũ
		if(is_dir($skinDir . $rdir)){
			if ( !ereg( "\.$", $rdir ) && in_array( $rdir, $baseSkin ) ) $tmp['b'][] = $rdir;
			else if ( !ereg( "\.$", $rdir ) && !in_array( $rdir, $baseSkin ) ) $tmp['u'][] = $rdir;
		}
	}

	sort ( $tmp['b'] );
	sort ( $tmp['u'] );

	$skins = array_merge($tmp['b'], $tmp['u']);
	unset( $tmp );
}
?>
<script language="javascript">
<!--
function shopSize(){

	var FObj = document.fm;

	{ // ���λ��� ������
		var shopLineSize = 0;

		if ( FObj.shopLineColorL.value != '' ) shopLineSize++;
		if ( FObj.shopLineColorC.value != '' ) shopLineSize++;
		if ( FObj.shopLineColorR.value != '' ) shopLineSize++;

		document.getElementById('shopLineSize').innerHTML = shopLineSize;
	}

	{ // ��ü ������
		document.getElementById('shopSize').innerHTML = eval( FObj.shopOuterSize.value ) + shopLineSize;
	}

	{ // ���� ������
		document.getElementById('shopBodySize').innerHTML = eval( FObj.shopOuterSize.value ) - eval( FObj.shopSideSize.value );
	}
}

function selectSkinDelete(tplSkin){
	if(confirm(tplSkin + "��Ų�� ������ ���� �Ͻðڽ��ϱ�? ������ ������ �Ұ����մϴ�.")){
		location.href="./indb.skin.php?mode=skinDel&tplSkin="+tplSkin;
	}
}

function selectSkinCopy(tplSkin){
	if(confirm("��Ų�̸��� " + tplSkin + "_C �� �����Ǿ� ���簡 ���� �˴ϴ�. Ȯ�ι�ư�� �����ø� ���簡 ���� �˴ϴ�.")){
		location.href="./indb.skin.php?mode=skinCopy&tplSkin="+tplSkin;
	}
}

function selectSkinChange(tplSkin,useWork) {
	var modeStr = '';
	if (useWork == 'workSkin') {
		modeStr = '[�۾���Ų]';
		mode = 'skinChangeWork';
	}
	else {
		modeStr = '[��뽺Ų]';
		mode = 'skinChange';
	}

	if (confirm(tplSkin + " ��Ų�� "+modeStr+"���� ���� �Ͻðڽ��ϱ�?\n\n�ؽ�Ų ���� PHP�±װ� ������ ���,�Ϻ� �Լ� ����� ���ѵ� �� �ֽ��ϴ�.\n�⺻����>��Ÿ����>�����ν�Ų ���ȼ������� ��� ���� PHP�±װ� �ִ��� Ȯ�����ּ���.")) {
		location.href="./indb.skin.php?mode="+mode+"&"+useWork+"="+tplSkin;
	}
}

window.onload = shopSize;
//-->
</script>

<?php if($godo['webCode'] != 'webhost_outside'){?>
<div id="80skins"><script>panel('80skins', 'design');</script></div>
<?php }?>

<div class="title title_top">������ ��Ų����<span>������ �⺻������ �����ϼ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=2')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>

<div style="padding-top:5px"></div>

<!-------------- ��Ų���� ���� --------------->
<table cellpadding="0" cellspacing="0" border="0" background="../img/codi/bg_skin_form_center.gif">
<tr>
	<td height="16" colspan="2"><img src="../img/codi/bg_skin_form_top.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td colspan="2" style="padding:5px 25px 5px 25px;vertical-align:top;">

	<!-- �����ϰ� �ִ� ��Ų -->
	<div style="padding-top:3px"><img src="../img/codi/bar_get_skin.gif" align="absmiddle" /></div>
	<table class="tb">
	<tr>
		<td height="20">

		<div id="skinBoxScroll" class="scroll">
		<table width="96%" cellpadding="0" cellspacing="0" border="0">
<?
	foreach ( $skins as $sKey => $sVal ){
		echo"<tr height=\"22\">".chr(10);

		/* ��Ų�� */
		echo"<td style='text-align:left;'>";
		if($sVal == $cfg['tplSkinWork']) echo"<b style='color:#F54D01;'>";
		if($sVal == $cfg['tplSkin']) echo"<b style='color:#5F8F1A;'>";
		if( in_array( $sVal, $baseSkin ) ){
			echo"�⺻��Ų";
		}else{
			echo"����ڽ�Ų";
		}
		echo" ( ".$sVal." )";
		if($sVal == $cfg['tplSkin']) echo"</b>";
		if($sVal == $cfg['tplSkinWork']) echo"</b>";
		if (file_exists(dirname(__FILE__).'/../../conf/design_meta_'.$sVal.'.php') === true) {
			include dirname(__FILE__).'/../../conf/design_meta_'.$sVal.'.php';
			if ($skinType === 'dtd') echo '<img src="../img/icon_webskin.gif" style="margin-left: 5px;"/>';
		}
		echo"</td>".chr(10);

		/* �۾���Ų */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">";
		if($sVal == $cfg['tplSkinWork']){
			echo"<img src=\"../img/codi/btn_work_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else if ($cfg['skinSecurityMode'] == 'y'){
			echo"<a href=\"javascript:selectSkinChange('".$sVal."','workSkin')\"/><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChangeWork&workSkin=".$sVal."\"><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* ��뽺Ų */
		echo"<td width=\"65\" style=\"padding:0px 20px 0px 3px\">";
		if($sVal == $cfg['tplSkin']){
			echo"<img src=\"../img/codi/btn_use_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else if ($cfg['skinSecurityMode'] == 'y'){
			echo"<a href=\"javascript:selectSkinChange('".$sVal."','useSkin')\"/><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChange&useSkin=".$sVal."\"><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* �̸����� */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">".chr(10);
		echo"<a href=\"/?tplSkin=".$sVal."\" target=\"_blank\"><img src=\"../img/codi/btn_preview.gif\" border=\"0\" align=\"absmiddle\" /></a>".chr(10);
		echo"</td>".chr(10);

		/* �ٿ�ε� */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"./indb.skin.php?mode=skinDown&tplSkin=".$sVal."\"><img src=\"../img/codi/btn_down.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* ���� */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"javascript:selectSkinCopy('".$sVal."');\"><img src=\"../img/codi/btn_copy.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* ���� */
		echo"<td width=\"40\" style=\"padding:0px 0px 0px 3px\">";
		if($sVal != $cfg['tplSkin'] && $sVal != $cfg['tplSkinWork']){
			echo"<a href=\"javascript:selectSkinDelete('".$sVal."');\"><img src=\"../img/codi/btn_delete.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		echo"</tr>".chr(10);
	}
?>
		</table>
		</div>

		</td>
	</tr>
	</table>
	<!-- �����ϰ� �ִ� ��Ų �� -->

	</td>
</tr>
<tr>
	<td colspan="2" style="padding:5px 25px 5px 25px;text-align:right;">
	<a href="javascript:popup2('skin.upload.php',400,300,0);"><img src="../img/codi/btn_skin_upload.gif" align="absmiddle" /></a>
	</td>
</tr>

<?if ($cfg['skinSecurityMode'] != 'y') {?>
<tr>
	<td colspan="2" style="padding:0px 25px 5px 25px;">
	<table border=2 bordercolor=#dce1e1 style="margin-top:10px; border-collapse:collapse; width: 719px;">
	<tr>
		<tr>
		<td style="padding:10px">
			<font color="red">�����ν�Ų ���� ���� �ȳ�</font><br><br>
			���θ������� �����ϰ� ��� �� �ֵ��� �����ν�Ų�� ������ ��ȭ�� �� �ִ� �����ν�Ų ���ȸ�� ����� �����ϰ��ֽ��ϴ�.<br>
			<a href="../basic/adm_etc_design_security.php" target="_blank">[�����ν�Ų ���ȼ��� �ٷΰ���]</a>
		</td>
		</tr>
	</tr>
	</table>
	</td>
<tr>
<?}?>

<tr>
	<td colspan="2" style="padding:0px 25px 5px 25px;">
	<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td>
		<div><img src="../img/icon_list.gif" align="absmiddle" /><span style="letter-spacing:1px;">standard</span>(2014.8��) ���� ��õ� ��Ų�� ��ǰ �����̹��� ����� 200�ȼ��� ����ȭ �Ǿ� �ֽ��ϴ�.</div>
		<div style="padding:0 0 10px 8px;">
		�������������� �����̹����� �۴ٰ� ������ ��� ����� �������ּ���. <a href="../goods/imgsize.php" target="_top" class="small_ex_point">[��ǰ���� > ��ǰ �̹��������� ���]</a><br/>
		��, ���� ��ϵ� ��ǰ�̹����� ������ ������ ��ŭ Ȯ��Ǿ� ���� �� �����Ƿ� ��ǰ �̹����� �ٽ� ����Ͻø� �˴ϴ�.<br/>
		��ǰ ������ ����ġ ������ <a href="../goods/disp_main.php" target="_top" class="small_ex_point">[��ǰ���� > ���������� ��ǰ����]</a>���� ����Ʈ�̹����� ��ü�� �� �ֽ��ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=22')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a>
		</div>
	</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#c8ec50;font-weight:bold;">��뽺Ų :</span> ���õ� ��Ų�� ���� ���θ� ȭ�鿡 �������ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#fec6ac;font-weight:bold;">�۾���Ų :</span> ���õ� ��Ų���� ������ �۾��� �ϰ� �˴ϴ�. �������� ���ÿ� ���� ��뽺Ų�� �۾���Ų�� �ٸ��ų� ������ �� �ֽ��ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">ȭ�麸�� :</span> �ش� ��Ų�� ���θ� ȭ���� ��â���� ���� �帳�ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">�ٿ� :</span> �ش� ��Ų�� �ٿ�ε� �޾Ƽ� ����� �� �ֽ��ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">���� :</span> �ش� ��Ų�� ����Ǿ� ��Ų�� �߰��˴ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">���� :</span> �ش� ��Ų�� �����Ǿ� ���ϴ�. (�⺻ ��Ų, ������� ��Ų, �۾����� ��Ų�� �������� �ʽ��ϴ�.)</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />[����] �������� �������� �ʾҰų� ���۱ǿ� ���˵Ǵ� ��Ų�� ���ε� �Ǵ� ����ؼ��� �ȵǸ�, �׿� ���� å���� ���θ� ��ڿ��� �ֽ��ϴ�.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>
	</td>
</tr>
<tr>
	<td height="17" colspan="2"><img src="../img/codi/bg_skin_form_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />
<!-------------- ��Ų���� �� --------------->

<!-- ���� ������� ��Ų -->
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td height="20"><img src="../img/codi/bar_use_skin.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #78b72a;border-right:1px solid #78b72a;">
	<table cellpadding="0" cellspacing="6" border="0">
	<tr>
		<td width="100"><img src="../img/codi/icon_use_skin.gif" align="absmiddle" /></td>
		<td width="300" style="line-height:30px;">
			<b style="color:5F8F1A;"><?=( in_array( $cfg['tplSkin'], $baseSkin ) ? "�⺻��Ų" : "����ڽ�Ų" )?> (<?=$cfg['tplSkin']?>)</b><br />
			<a href="/?tplSkin=<?=$cfg['tplSkin']?>" target="_blank"><img src="../img/codi/btn_preview.gif" align="absmiddle" /></a>
		</td>
		<td style="line-height:30px;">
		<?if ($cfg['skinSecurityMode'] == 'y'){?>
			<a href="javascript:selectSkinChange('<?=$cfg['tplSkin']?>','workSkin')">
		<?}else{?>
			<a href="./indb.skin.php?mode=skinChangeWork&workSkin=<?=$cfg['tplSkin']?>">
		<?}?>
		<img src="../img/codi/btn_work_skin.gif" align="absmiddle" /></a></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="4"><img src="../img/codi/bg_use_skin_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />
<!-- ���� ������� ��Ų �� -->

<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td height="20"><img src="../img/codi/bar_work_skin.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #f64c01;border-right:1px solid #f64c01;">

	<!-------------- ���� �۾����� ��Ų ���� --------------->
	<table cellpadding="0" cellspacing="6" border="0">
	<tr>
		<td width="100"><img src="../img/codi/icon_work_skin.gif" align="absmiddle" /></td>
		<td width="300" style="line-height:30px;">
			<b style="color:F54D01;"><?=( in_array( $cfg['tplSkinWork'], $baseSkin ) ? "�⺻��Ų" : "����ڽ�Ų" )?> (<?=$cfg['tplSkinWork']?>)</b><br />
			<a href="/?tplSkin=<?=$cfg['tplSkinWork']?>" target="_blank"><img src="../img/codi/btn_preview.gif" align="absmiddle" /></a>
		</td>
		<td style="line-height:30px;">
		<?if ($cfg['skinSecurityMode'] == 'y'){?>
			<a href="javascript:selectSkinChange('<?=$cfg['tplSkin']?>','useSkin')">
		<?}else{?>
			<a href="./indb.skin.php?mode=skinChange&useSkin=<?=$cfg['tplSkinWork']?>">
		<?}?>
		<img src="../img/codi/btn_use_skin.gif" align="absmiddle" /></a></td>
	</tr>
	</table>
	<!-------------- ���� �۾����� ��Ų �� --------------->

	</td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #f64c01;border-right:1px solid #f64c01; padding:0px 33px 0px 33px;">

	<div style="padding-top:20px"></div>

	<form name="fm" method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>">
	<input type="hidden" name="tplSkin" value="<?=$cfg['tplSkin']?>">
	<input type="hidden" name="tplSkinWork" value="<?=$cfg['tplSkinWork']?>">


	<!-------------- ����/���� ���� ���� --------------->
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<!---------- ���ʶ��� ���� ------------>
		<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>���ʶ��λ���</td>
			<td align="right"><img src="../img/back_side_leftline.gif" /></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-right:3px"><input type="text" name=shopLineColorL class="line" value="<?=$cfg['shopLineColorL']?>" maxlength="6" style="width:48px;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a></td>
		</tr>
		<tr><td colspan="2" style="padding-top:2px"><font class="extext">������ �Ⱦ�����<br />�������� �μ���</td></tr></table>
		</td>
		<!---------- ���ʶ��� �� ------------>

		
		<td width="500" height="356" background="../img/back_skinsize_set.gif" valign="top">
		<!-------------- ��ü������ ���� ���� --------------->
		<table width="500" height="56" cellpadding="0" cellspacing="0" background="../img/back_skin_allsize.gif" border="0">
		<tr>
			<td width="90"></td>
			<td align="center" valign="top">��ü <span id="shopSize" style="font:10pt ����;color:#ff4e00;font-weight:bold;"><b>0</b></font></span> �ȼ� = �ܰ� <input type="text" name="shopOuterSize" style="width:50px" value="<?=$cfg['shopOuterSize']?>" class="cline" onkeyup="shopSize();" required label='�ܰ� ������'> �ȼ� + ���� <span id="shopLineSize" style="font:10pt ����;color:#ff4e00;font-weight:bold;">0</span> �ȼ�</td>
			<td width="90"></td>
		</tr>
		</table>
		<!-------------- ��ü������ ���� �� --------------->

		<!------------------------- ����/����/������� ���� -------------------------->
		<div style="background:url(../img/back_skinsize_set.gif) no-repeat; height:300px;">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top">
			<!---------- ��������� ���� ------------>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr><td height="95" colspan="5"></td></tr>
			<tr>
				<td width="10"></td>
				<td><img src="../img/back_side_leftline.gif" /></td>
				<td width="120" align="center">���� <input type="text" name=shopSideSize value="<?=$cfg['shopSideSize']?>" style="width:50px" class="cline" onkeyup="shopSize();"> �ȼ�</td>
				<td><img src="../img/back_side_rightline.gif" /></td>
			</tr>
			</table>
			<!---------- ��������� �� ------------>
			</td>

			<td>
			<!---------- ������� ���� ------------>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr><td height=100 colspan="2"></td></tr>
			<tr>
				<td width="10"></td>
				<td background="../img/back_centersize.gif" width="320" height="7" align="center">���� <span id="shopBodySize" style="font:10pt ����;color:#ff4e00;font-weight:bold;">0</span> �ȼ�</td>
			</tr>
			</table>

			<br /><br />

			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="10"></td>
				<td width="24"><img src="../img/back_rightline.gif" /></td>
				<td>������λ���</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><input type="text" name="shopLineColorC" class="line" value="<?=$cfg['shopLineColorC']?>" maxlength="6" style="width:50px;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td style="padding-top:2px"><font class="extext">������ �Ⱦ�����<br />�������� �μ���</td>
			</tr>
			</table>
			<!---------- ������� �� ------------>
			</td>
		</tr>
		</table>
		</div>
		<!---------------------------- ���麻��/������� �� --------------------------->

		</td>		

		<!---------- �����ʶ��� ���� ------------>
		<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="18"><img src="../img/back_side_rightline.gif" /></td>
			<td>�����ʶ��λ���</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:5px"><input type="text" name="shopLineColorR" class="line" value="<?=$cfg['shopLineColorR']?>" maxlength="6" style="width:48px;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a></td>
		</tr>
		<tr><td colspan="2" style="padding:2px 0px 0px 5px"><font class="extext">������ �Ⱦ�����<br />�������� �μ���</td></tr>
		</table>
		</td>
		<!---------- �����ʶ��� �� ------------>

		<td></td>

	</tr>
	</table>
	<!-------------- ����/���� ���� �� --------------->

	<div style="padding-top:15px;"></div>

	<!-------------- ȭ�� ���� ���� --------------->
	<table width="690" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="noline" align="center">
			<div><img src="../img/shop_left_align.gif" /></div>
			<input type="radio" name="shopAlign" value="left" <?=( $cfg['shopAlign'] == 'left' ? 'checked' : '' )?> required label='���Ĺ��'>ȭ�� �������� �����ϱ�</td>
			<td width=40></td>
			<td class="noline" align="center">
			<div><img src="../img/shop_center_align.gif" /></div>
			<input type="radio" name="shopAlign" value="center" <?=( $cfg['shopAlign'] == 'center' ? 'checked' : '' )?> required label='���Ĺ��'>ȭ�� ����� �����ϱ�</td></tr>
		</table>
		</td>
	</tr>
	</table>
	<!-------------- ȭ�� ���� �� --------------->

	<div class="title">���λ�ǰ ���� ����<span>���ο� ����Ǵ� ��ǰ�����ϴ� ����� ������ �� �ֽ��ϴ�.</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>���λ�ǰ ���� ����</td>
		<td class="noline">
		<input type="radio" name="shopMainGoodsConf" value="E" <?=$checked['shopMainGoodsConf']['E']?>> ��Ų�� ����
		<input type="radio" name="shopMainGoodsConf" value="T" <?=$checked['shopMainGoodsConf']['T']?>> ���� ����
		<div style="padding:6px 0px 0px 25px"><font class="extext">��Ų�� ���� : ��Ų���� ������ ��ǰ������ ������������ ����˴ϴ�.</font></div>
		<div style="padding:3px 0px 0px 25px"><font class="extext">���� ���� : ��Ų�� ������� ����</font></div>
		<div style="padding:3px 0px 0px 25px"><font class="extext">�� ������ ��ǰ�� ��ǰ���� > ��ǰ�������� > <a href="../goods/disp_main.php" class="extext">[���������� ��ǰ����]</a> ���� Ȯ���ϰ� ������ �� �ֽ��ϴ�.</font></div>
		</td>
	</tr>
	</table>

	<div class="title">ī�װ� �޴����̾� ����<span>ī�װ� �޴����̾� Ÿ���� �����ϼ���</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�޴����̾� ����</td>
		<td class="noline">
		<input type="radio" name="subCategory" value="0" <?=$checked['subCategory'][0]?>> �޴����̾� ������
		<input type="radio" name="subCategory" value="1" <?=$checked['subCategory'][1]?>> �޴����̾� �����
		<input type="radio" name="subCategory" value="2" <?=$checked['subCategory'][2]?>> 1��/2�� ī�װ��� ��� ���
		<div style="padding:6px 0px 0px 25px"><font class="extext">ī�װ� �޴����̾�� 1�� ī�װ� �޴��� ���콺�� �ø��� ������ ���̾ �������� ����Դϴ�</font></div>
		</td>
	</tr>
	</table>

	<!--<div class="title">���콺 ������ ��ư ����<span>����Ʈ���� ���콺�� ������ ��ư�� ���� �������� ���� ���� (�������)</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>���콺 ������ ����</td>
		<td class="noline">
		<input type="radio" name="copyProtect" value="0" <?=$checked['copyProtect'][0]?>> ���콺 ������ ��ư ���Ѿ���
		<input type="radio" name="copyProtect" value="1" <?=$checked['copyProtect'][1]?>> ���콺 ������ ��ư ����
		</td>
	</tr>
	</table>-->

	<table width="690" cellpadding="0" cellspacing="0" border="0">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center" class="noline"><input type="image" src="../img/btn_register.gif"></td>
	</tr>
	<tr><td height="20"></td></tr>
	</table>

	</form>
	</td>
</tr>
<tr>
	<td height="4"><img src="../img/codi/bg_work_skin_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />

<script>
	table_design_load();
	setHeight_ifrmCodi();
</script>
<?
if ($popupWin !== true){
	include "../_footer.php";
}
?>
