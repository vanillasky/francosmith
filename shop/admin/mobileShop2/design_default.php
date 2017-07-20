<?
if (basename($_SERVER['PHP_SELF']) == 'iframe.default.php'){
	include "../_header.popup.php";
	$popupWin = true;
} else {
	$location = "기본관리 > 디자인 스킨설정";
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

{ // 스킨 디렉토리 정의

	$baseSkin = array( 'default');
	$tmp = array( 'b' => array(), 'u' => array() );

	$skinDir = dirname(__FILE__) . "/../../data/skin_mobileV2/";
	$odir = @opendir( $skinDir );

	while (false !== ($rdir = readdir($odir))) {
		// 디렉토리인지를 체크
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
	if(confirm(tplSkin + "스킨을 정말로 삭제 하시겠습니까? 삭제시 복구가 불가능합니다.")){
		location.href="./indb.skin.php?mode=skinDel&tplSkin="+tplSkin;
	}
}

function selectSkinCopy(tplSkin){
	if(confirm("스킨이름이 " + tplSkin + "_C 로 설정되어 복사가 진행 됩니다. 확인버튼을 누르시면 복사가 진행 됩니다.")){
		location.href="./indb.skin.php?mode=skinCopy&tplSkin="+tplSkin;
	}
}

function selectSkinChange(tplSkin,useWork) {
	var modeStr = '';
	if (useWork == 'workSkin') {
		modeStr = '[작업스킨]';
		mode = 'skinChangeWork';
	}
	else {
		modeStr = '[사용스킨]';
		mode = 'skinChange';
	}

	if (confirm(tplSkin + " 스킨을 "+modeStr+"으로 변경 하시겠습니까?\n\n※스킨 내에 PHP태그가 존재할 경우,일부 함수 사용이 제한될 수 있습니다.\n기본설정>기타관리>디자인스킨 보안설정에서 사용 중인 PHP태그가 있는지 확인해주세요.")) {
		location.href="./indb.skin.php?mode="+mode+"&"+useWork+"="+tplSkin;
	}
}
//-->
</script>

<div id="mobileshop"><script>panel('mobileshop', 'design');</script></div>

<div class="title title_top">디자인 스킨설정<span>디자인 기본사항을 설정하세요</span></div>

<div style="padding-top:5px"></div>

<!-------------- 스킨선택 시작 --------------->
<table cellpadding="0" cellspacing="0" border="0" background="../img/codi/bg_skin_form_center.gif">
<tr>
	<td height="16" colspan="2"><img src="../img/codi/bg_skin_form_top.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td colspan="2" style="padding:5px 25px 5px 25px;vertical-align:top;">

	<!-- 보유하고 있는 스킨 -->
	<div style="padding-top:3px"><img src="../img/codi/bar_get_skin.gif" align="absmiddle" /></div>
	<table class="tb" width="100%">
	<tr>
		<td height="20">

		<div id="skinBoxScroll" class="scroll">
		<table width="96%" cellpadding="0" cellspacing="0" border="0">
<?
	foreach ( $skins as $sKey => $sVal ){
		echo"<tr height=\"22\">".chr(10);

		/* 스킨명 */
		echo"<td style='text-align:left;'>";
		if($sVal == $cfg['tplSkinMobileWork']) echo"<b style='color:#F54D01;'>";
		if($sVal == $cfg['tplSkinMobile']) echo"<b style='color:#5F8F1A;'>";
		if( in_array( $sVal, $baseSkin ) ){
			echo"기본스킨";
		}else{
			echo"사용자스킨";
		}
		echo" ( ".$sVal." )";
		if($sVal == $cfg['tplSkinMobile']) echo"</b>";
		if($sVal == $cfg['tplSkinMobileWork']) echo"</b>";
		echo"</td>".chr(10);

		/* 작업스킨 */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">";
		if($sVal == $cfg['tplSkinMobileWork']){
			echo"<img src=\"../img/codi/btn_work_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else if ($cfg['skinSecurityMode'] == 'y'){
			echo"<a href=\"javascript:selectSkinChange('".$sVal."','workSkin')\"/><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChangeWork&workSkin=".$sVal."\"><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* 사용스킨 */
		echo"<td width=\"65\" style=\"padding:0px 20px 0px 3px\">";
		if($sVal == $cfg['tplSkinMobile']){
			echo"<img src=\"../img/codi/btn_use_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else if ($cfg['skinSecurityMode'] == 'y'){
			echo"<a href=\"javascript:selectSkinChange('".$sVal."','useSkin')\"/><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChange&useSkin=".$sVal."\"><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* 미리보기 */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">".chr(10);
		echo"<a href=\"/m/?tplSkin=".$sVal."\" target=\"_blank\"><img src=\"../img/codi/btn_preview.gif\" border=\"0\" align=\"absmiddle\" /></a>".chr(10);
		echo"</td>".chr(10);

		/* 다운로드 */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"./indb.skin.php?mode=skinDown&tplSkin=".$sVal."\"><img src=\"../img/codi/btn_down.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* 복사 */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"javascript:selectSkinCopy('".$sVal."');\"><img src=\"../img/codi/btn_copy.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* 삭제 */
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
	<!-- 보유하고 있는 스킨 끝 -->

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
			<font color="red">디자인스킨 보안 설정 안내</font><br><br>
			쇼핑몰을보다 안전하게 운영할 수 있도록 디자인스킨의 보안을 강화할 수 있는 디자인스킨 보안모드 사용을 권장하고있습니다.<br>
			<a href="../basic/adm_etc_design_security.php" target="_blank">[디자인스킨 보안설정 바로가기]</a>
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
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#c8ec50;font-weight:bold;">사용스킨 :</span> 선택된 스킨이 실제 쇼핑몰 화면에 보여집니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#fec6ac;font-weight:bold;">작업스킨 :</span> 선택된 스킨으로 디자인 작업을 하게 됩니다. 관리자의 선택에 따라 사용스킨과 작업스킨은 다르거나 동일할 수 있습니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">화면보기 :</span> 해당 스킨의 쇼핑몰 화면을 새창으로 보여 드립니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">다운 :</span> 해당 스킨을 다운로드 받아서 백업할 수 있습니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">복사 :</span> 해당 스킨이 복사되어 스킨이 추가됩니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">삭제 :</span> 해당 스킨이 삭제되어 집니다. (기본 스킨, 사용중인 스킨, 작업중인 스킨은 삭제되지 않습니다.)</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />주의 : 정식으로 구매하지 않았거나 저작권에 저촉되는 스킨을 업로드 또는 사용해서는 안되며, 그에 대한 책임은 쇼핑몰 운영자에게 있습니다.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>
	</td>
</tr>
<tr>
	<td height="17" colspan="2"><img src="../img/codi/bg_skin_form_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />
<!-------------- 스킨선택 끝 --------------->

<script>
	table_design_load();
	setHeight_ifrmCodi();
</script>
<?
if ($popupWin !== true){
	include "../_footer.php";
}
?>
