<?
	include "../_header.popup.php";
	$popupWin = true;


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

	$baseSkin = array( 'today' , 'goodday');	// 2011-07-11 goodday 추가
	$tmp = array( 'b' => array(), 'u' => array() );

	$skinDir = dirname(__FILE__) . "/../../data/skin_today/";
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
function shopSize(){

	var FObj = document.fm;

	{ // 라인색상 사이즈
		var shopLineSize = 0;

		if ( FObj.shopLineColorL.value != '' ) shopLineSize++;
		//if ( FObj.shopLineColorC.value != '' ) shopLineSize++;	// 2011-07-11 측면이 제외되면서 불필요한 부분
		if ( FObj.shopLineColorR.value != '' ) shopLineSize++;

		document.getElementById('shopLineSize').innerHTML = shopLineSize;
	}

	{ // 전체 사이즈
		document.getElementById('shopSize').innerHTML = eval( FObj.shopOuterSize.value ) + shopLineSize;
	}

	{ // 본문 사이즈
		document.getElementById('shopBodySize').innerHTML = eval( FObj.shopOuterSize.value ) /*- eval( FObj.shopSideSize.value )*/;	// 2011-07-11 측면이 제외되면서 불필요한 부분
	}
}

function selectSkinDelete(tplSkinToday){
	if(confirm(tplSkinToday + "스킨을 정말로 삭제 하시겠습니까? 삭제시 복구가 불가능합니다.")){
		location.href="./indb.skin.php?mode=skinDel&tplSkinToday="+tplSkinToday;
	}
}

function selectSkinCopy(tplSkinToday){
	if(confirm("스킨이름이 " + tplSkinToday + "_C 로 설정되어 복사가 진행 됩니다. 확인버튼을 누르시면 복사가 진행 됩니다.")){
		location.href="./indb.skin.php?mode=skinCopy&tplSkinToday="+tplSkinToday;
	}
}

window.onload = shopSize;
//-->
</script>

<div class="title title_top">디자인 스킨설정<span>디자인 기본사항을 설정하세요</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=2')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>

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
	<table class="tb">
	<tr>
		<td height="20">

		<div id="skinBoxScroll" class="scroll">
		<table width="96%" cellpadding="0" cellspacing="0" border="0">
<?

	foreach ( $skins as $sKey => $sVal ){
		echo"<tr height=\"22\">".chr(10);

		/* 스킨명 */
		echo"<td>";
		if($sVal == $cfg['tplSkinTodayWork']) echo"<b style='color:F54D01;'>";
		if($sVal == $cfg['tplSkinToday']) echo"<b style='color:5F8F1A;'>";
		if( in_array( $sVal, $baseSkin ) ){
			echo"기본스킨";
		}else{
			echo"사용자스킨";
		}
		echo" ( ".$sVal." )";
		if($sVal == $cfg['tplSkinTodayWork']) echo"</b>";
		echo"</td>".chr(10);

		/* 작업스킨 */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">";
		if($sVal == $cfg['tplSkinTodayWork']){
			echo"<img src=\"../img/codi/btn_work_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChangeWork&workSkinToday=".$sVal."\"><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* 사용스킨 */
		echo"<td width=\"65\" style=\"padding:0px 20px 0px 3px\">";
		if($sVal == $cfg['tplSkinToday']){
			echo"<img src=\"../img/codi/btn_use_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChange&useSkinToday=".$sVal."\"><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* 미리보기 */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">".chr(10);
		echo"<a href=\"/?tplSkinToday=".$sVal."\" target=\"_blank\"><img src=\"../img/codi/btn_preview.gif\" border=\"0\" align=\"absmiddle\" /></a>".chr(10);
		echo"</td>".chr(10);

		/* 다운로드 */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"./indb.skin.php?mode=skinDown&tplSkinToday=".$sVal."\"><img src=\"../img/codi/btn_down.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* 복사 */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"javascript:selectSkinCopy('".$sVal."');\"><img src=\"../img/codi/btn_copy.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* 삭제 */
		echo"<td width=\"40\" style=\"padding:0px 0px 0px 3px\">";
		if($sVal != $cfg['tplSkinToday'] && $sVal != $cfg['tplSkinTodayWork']){
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
	<a href="javascript:popup2('codi.skin.upload.php',400,300,0);"><img src="../img/codi/btn_skin_upload.gif" align="absmiddle" /></a>
	</td>
</tr>
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

<!-- 현재 사용중인 스킨 -->
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
			<b style="color:5F8F1A;"><?=( in_array( $cfg['tplSkinToday'], $baseSkin ) ? "기본스킨" : "사용자스킨" )?> (<?=$cfg['tplSkinToday']?>)</b><br />
			<a href="/?tplSkinToday=<?=$cfg['tplSkinToday']?>" target="_blank"><img src="../img/codi/btn_preview.gif" align="absmiddle" /></a>
		</td>
		<td style="line-height:30px;"><a href="./indb.skin.php?mode=skinChangeWork&workSkinToday=<?=$cfg['tplSkinToday']?>"><img src="../img/codi/btn_work_skin.gif" align="absmiddle" /></a></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="4"><img src="../img/codi/bg_use_skin_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />
<!-- 현재 사용중인 스킨 끝 -->

<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td height="20"><img src="../img/codi/bar_work_skin.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #f64c01;border-right:1px solid #f64c01;">

	<!-------------- 현재 작업중인 스킨 시작 --------------->
	<table cellpadding="0" cellspacing="6" border="0">
	<tr>
		<td width="100"><img src="../img/codi/icon_work_skin.gif" align="absmiddle" /></td>
		<td width="300" style="line-height:30px;">
			<b style="color:F54D01;"><?=( in_array( $cfg['tplSkinTodayWork'], $baseSkin ) ? "기본스킨" : "사용자스킨" )?> (<?=$cfg['tplSkinTodayWork']?>)</b><br />
			<a href="/?tplSkinToday=<?=$cfg['tplSkinTodayWork']?>" target="_blank"><img src="../img/codi/btn_preview.gif" align="absmiddle" /></a>
		</td>
		<td style="line-height:30px;"><a href="./indb.skin.php?mode=skinChange&useSkinToday=<?=$cfg['tplSkinTodayWork']?>"><img src="../img/codi/btn_use_skin.gif" align="absmiddle" /></a></td>
	</tr>
	</table>
	<!-------------- 현재 작업중인 스킨 끝 --------------->

	</td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #f64c01;border-right:1px solid #f64c01; padding:0px 33px 0px 33px;">

	<div style="padding-top:20px"></div>

	<form name="fm" method="post" action="../todayshop/indb.skin.php" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>">
	<input type="hidden" name="tplSkinToday" value="<?=$cfg['tplSkinToday']?>">
	<input type="hidden" name="tplSkinTodayWork" value="<?=$cfg['tplSkinTodayWork']?>">

	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="96"></td>
		<td>
		<table width="500" height="56" cellpadding="0" cellspacing="0" background="../img/back_skin_allsize.gif" border="0">
		<tr>
			<td width="90"></td>
			<td align="center" valign="top">전체 <span id="shopSize" style="font:10pt 굴림;color:#ff4e00;font-weight:bold;"><b>0</b></font></span> 픽셀 = 외곽 <input type="text" name="shopOuterSize" style="width:50px" value="<?=$cfg['shopOuterSize']?>" class="cline" onkeyup="shopSize();" required label='외곽 사이즈'> 픽셀 + 라인 <span id="shopLineSize" style="font:10pt 굴림;color:#ff4e00;font-weight:bold;">0</span> 픽셀</td>
			<td width="90"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<!-------------- 전체사이즈 셋팅 끝 --------------->

	<!-------------- 라인/본문 셋팅 시작 --------------->
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<!---------- 왼쪽라인 시작 ------------>
		<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>왼쪽라인색상</td>
			<td align="right"><img src="../img/back_side_leftline.gif" /></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-right:3px"><input type="text" name=shopLineColorL class="line" value="<?=$cfg['shopLineColorL']?>" maxlength="6" style="width:55;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="색상표 보기" align="absmiddle" /></a></td>
		</tr>
		<tr><td colspan="2" style="padding-top:2px"><font class="extext">라인을 안쓰려면<br />공란으로 두세요</td></tr></table>
		</td>
		<!---------- 왼쪽라인 끝 ------------>

		<!------------------------- 측면/본문/가운데라인 시작 -------------------------->
		<td width="500" height="300" background="../img/back_skintodaysize_set.gif" valign="top">

		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td valign="top">
			<br>
			<!---------- 가운데라인 시작 ------------>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr><td height=100 colspan="2"></td></tr>
			<tr>
				<td width="10"></td>
				<td background="../img/back_centersize_todayshop.gif" width="480" height="7" align="center">본문 <span id="shopBodySize" style="font:10pt 굴림;color:#ff4e00;font-weight:bold;">0</span> 픽셀</td>
			</tr>
			</table>
			<!---------- 가운데라인 끝 ------------>
			</td>
		</tr>
		</table>

		</td>
		<!---------------------------- 측면본문/가운데라인 끝 --------------------------->

		<!---------- 오른쪽라인 시작 ------------>
		<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="18"><img src="../img/back_side_rightline.gif" /></td>
			<td>오른쪽라인색상</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:5px"><input type="text" name="shopLineColorR" class="line" value="<?=$cfg['shopLineColorR']?>" maxlength="6" style="width:55;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="색상표 보기" align="absmiddle" /></a></td>
		</tr>
		<tr><td colspan="2" style="padding:2px 0px 0px 5px"><font class="extext">라인을 안쓰려면<br />공란으로 두세요</td></tr>
		</table>
		</td>
		<!---------- 오른쪽라인 끝 ------------>

		<td></td>

	</tr>
	</table>
	<!-------------- 라인/본문 셋팅 끝 --------------->

	<div style="padding-top:15"></div>

	<!-------------- 화면 정렬 시작 --------------->
	<table width="690" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="noline" align="center">
			<div><img src="../img/todayshop_left_align.gif" /></div>
			<input type="radio" name="shopAlign" value="left" <?=( $cfg['shopAlign'] == 'left' ? 'checked' : '' )?> required label='정렬방식'>화면 왼쪽으로 정렬하기</td>
			<td width=40></td>
			<td class="noline" align="center">
			<div><img src="../img/todayshop_center_align.gif" /></div>
			<input type="radio" name="shopAlign" value="center" <?=( $cfg['shopAlign'] == 'center' ? 'checked' : '' )?> required label='정렬방식'>화면 가운데로 정렬하기</td></tr>
		</table>
		</td>
	</tr>
	</table>
	<!-------------- 화면 정렬 끝 --------------->

	<!--div class="title">메인상품 진열 설정<span>메인에 노출되는 상품진열하는 방법을 선택할 수 있습니다.</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>메인상품 진열 설정</td>
		<td class="noline">
		<input type="radio" name="shopMainGoodsConf" value="E" <?=$checked['shopMainGoodsConf']['E']?>> 스킨별 설정
		<input type="radio" name="shopMainGoodsConf" value="T" <?=$checked['shopMainGoodsConf']['T']?>> 통합 설정
		<div style="padding:6px 0px 0px 25px"><font class="extext">스킨별 설정 : 스킨마다 설정된 상품진열이 메인페이지에 노출됩니다.</font></div>
		<div style="padding:3px 0px 0px 25px"><font class="extext">통합 설정 : 스킨에 상관없이 통합</font></div>
		<div style="padding:3px 0px 0px 25px"><font class="extext">※ 진열된 상품을 상품관리 > 상품진열관리 > <a href="../goods/disp_main.php" class="extext">[메인페이지 상품진열]</a> 에서 확인하고 설정할 수 있습니다.</font></div>
		</td>
	</tr>
	</table-->

	<!--div class="title">카테고리 메뉴레이어 설정<span>카테고리 메뉴레이어 타입을 설정하세요</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>메뉴레이어 설정</td>
		<td class="noline">
		<input type="radio" name="subCategory" value="0" <?=$checked['subCategory'][0]?>> 메뉴레이어 사용안함
		<input type="radio" name="subCategory" value="1" <?=$checked['subCategory'][1]?>> 메뉴레이어 사용함
		<input type="radio" name="subCategory" value="2" <?=$checked['subCategory'][2]?>> 1차/2차 카테고리를 모두 출력
		<div style="padding:6px 0px 0px 25px"><font class="extext">카테고리 메뉴레이어란 1차 카테고리 메뉴에 마우스를 올리면 옆으로 레이어가 보여지는 기능입니다</font></div>
		</td>
	</tr>
	</table>

	<div class="title">마우스 오른쪽 버튼 설정<span>사이트에서 마우스의 오른쪽 버튼을 막을 것인지에 대한 설정 (복사방지)</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>마우스 오른쪽 설정</td>
		<td class="noline">
		<input type="radio" name="copyProtect" value="0" <?=$checked['copyProtect'][0]?>> 마우스 오른쪽 버튼 제한없음
		<input type="radio" name="copyProtect" value="1" <?=$checked['copyProtect'][1]?>> 마우스 오른쪽 버튼 제한
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

<!-------------- 디자인가이드 시작 --------------->
<table width="690" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td align="center"><a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_easy_design.php',750,800)"><img src="../img/btn_go_easydesign.gif" /></a>&nbsp;<a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_high_design.php',750,800)"><img src="../img/btn_go_highdesign.gif" /></a></td>
</tr>
</table>
<!-------------- 디자인가이드 끝 --------------->

<div style="padding-top:25px"></div>

<div style="padding-left:50px">
<!-------------- 스킨 기본설정 안내 시작 --------------->
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="7"></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" class="small">
	<tr><td><img src="../img/arrow_blue.gif" align="absmiddle" /><font color="000000"><b>디자인 기본설정 도움말</b></font></td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font color="555555">스킨을 선택한 후 원하는 디자인으로 수정작업을 진행하시면 됩니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font color="555555">라인없이 디자인하시려면 라인색상을 공란으로 두세요. 라인은 각각 0픽셀이 됩니다.</td><tr>
	<tr><td height=20></td></tr>
	<tr><td><img src="../img/arrow_blue.gif" align="absmiddle" /><font color="000000"><b>각 스킨별 기본사이즈 설정값</b></font></td></tr>
	</table>

	<div style="padding-top:13px"></div>

	<!-------------- season2 / easy 시작 --------------->
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="padding-left:8px">

		<? if ( array_search( 'today', $skins ) !== false ){ ?>
		<table cellpadding="0" cellspacing="0" class="small" width=260>
		<tr><td><font color="627dce"><b>today</b></font></td></tr>
		<tr><td><font color="555555">전체 900 픽셀 = 외곽900픽셀 + 라인색상 0 픽셀</td></tr>
		<tr><td><font color="555555">라인색상 =  왼쪽/가운데/오른쪽 모두 공백</font></div></td></tr>
		</table>
		<? } ?>
		</td>

		<td width=50></td>

		<td>
		<? if ( array_search( 'goodday', $skins ) !== false ){ ?>
		<table cellpadding="0" cellspacing="0" class="small" width=260>
		<tr><td><font color="627dce"><b>goodday</b></font></td></tr>
		<tr><td><font color="555555">전체 900 픽셀 = 외곽900픽셀 + 라인색상 0 픽셀</td></tr>
		<tr><td><font color="555555">라인색상 =  왼쪽/가운데/오른쪽 모두 공백</font></div></td></tr>
		</table>
		<? } ?>
		</td>
	</tr>
	</table>
	<!-------------- season2 / easy 끝 --------------->

	<div style="padding-top:13px"></div>

	</td>
</tr>
</table>

<!-------------- 스킨 기본설정 안내 끝 --------------->
</div>

<div style="padding-top:20px"></div>

<table cellpadding="0" cellspacing="0">
<tr><td bgcolor=e4e4e4 height=1 width=750></td></tr>
</table>

<div style="padding-top:20px"></div>

<?
if ($popupWin === true){
	echo '<script>table_design_load();setHeight_ifrmCodi();</script>';
}
else {
	include "../_footer.php";
}
?>