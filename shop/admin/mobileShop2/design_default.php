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

	$baseSkin = array( 'default');
	$tmp = array( 'b' => array(), 'u' => array() );

	$skinDir = dirname(__FILE__) . "/../../data/skin_mobileV2/";
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
//-->
</script>

<div id="mobileshop"><script>panel('mobileshop', 'design');</script></div>

<div class="title title_top">������ ��Ų����<span>������ �⺻������ �����ϼ���</span></div>

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
	<table class="tb" width="100%">
	<tr>
		<td height="20">

		<div id="skinBoxScroll" class="scroll">
		<table width="96%" cellpadding="0" cellspacing="0" border="0">
<?
	foreach ( $skins as $sKey => $sVal ){
		echo"<tr height=\"22\">".chr(10);

		/* ��Ų�� */
		echo"<td style='text-align:left;'>";
		if($sVal == $cfg['tplSkinMobileWork']) echo"<b style='color:#F54D01;'>";
		if($sVal == $cfg['tplSkinMobile']) echo"<b style='color:#5F8F1A;'>";
		if( in_array( $sVal, $baseSkin ) ){
			echo"�⺻��Ų";
		}else{
			echo"����ڽ�Ų";
		}
		echo" ( ".$sVal." )";
		if($sVal == $cfg['tplSkinMobile']) echo"</b>";
		if($sVal == $cfg['tplSkinMobileWork']) echo"</b>";
		echo"</td>".chr(10);

		/* �۾���Ų */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">";
		if($sVal == $cfg['tplSkinMobileWork']){
			echo"<img src=\"../img/codi/btn_work_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else if ($cfg['skinSecurityMode'] == 'y'){
			echo"<a href=\"javascript:selectSkinChange('".$sVal."','workSkin')\"/><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChangeWork&workSkin=".$sVal."\"><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* ��뽺Ų */
		echo"<td width=\"65\" style=\"padding:0px 20px 0px 3px\">";
		if($sVal == $cfg['tplSkinMobile']){
			echo"<img src=\"../img/codi/btn_use_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else if ($cfg['skinSecurityMode'] == 'y'){
			echo"<a href=\"javascript:selectSkinChange('".$sVal."','useSkin')\"/><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChange&useSkin=".$sVal."\"><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* �̸����� */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">".chr(10);
		echo"<a href=\"/m/?tplSkin=".$sVal."\" target=\"_blank\"><img src=\"../img/codi/btn_preview.gif\" border=\"0\" align=\"absmiddle\" /></a>".chr(10);
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
		if($sVal != $cfg['tplSkinMobile'] && $sVal != $cfg['tplSkinMobileWork']){
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
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#c8ec50;font-weight:bold;">��뽺Ų :</span> ���õ� ��Ų�� ���� ���θ� ȭ�鿡 �������ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#fec6ac;font-weight:bold;">�۾���Ų :</span> ���õ� ��Ų���� ������ �۾��� �ϰ� �˴ϴ�. �������� ���ÿ� ���� ��뽺Ų�� �۾���Ų�� �ٸ��ų� ������ �� �ֽ��ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">ȭ�麸�� :</span> �ش� ��Ų�� ���θ� ȭ���� ��â���� ���� �帳�ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">�ٿ� :</span> �ش� ��Ų�� �ٿ�ε� �޾Ƽ� ����� �� �ֽ��ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">���� :</span> �ش� ��Ų�� ����Ǿ� ��Ų�� �߰��˴ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">���� :</span> �ش� ��Ų�� �����Ǿ� ���ϴ�. (�⺻ ��Ų, ������� ��Ų, �۾����� ��Ų�� �������� �ʽ��ϴ�.)</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���� : �������� �������� �ʾҰų� ���۱ǿ� ���˵Ǵ� ��Ų�� ���ε� �Ǵ� ����ؼ��� �ȵǸ�, �׿� ���� å���� ���θ� ��ڿ��� �ֽ��ϴ�.</td></tr>
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

<script>
	table_design_load();
	setHeight_ifrmCodi();
</script>
<?
if ($popupWin !== true){
	include "../_footer.php";
}
?>
