<?php
/**
 * 멀티 팝업 등록 페이지
 * @author cjb3333 , artherot @ godosoft development team.
 */

$scriptLoad='<script src="./codi/_codi.js"></script>';
include "../_header.popup.php";

// 멀티 팝업 Class
$multipopup = Core::loader('MultiPopup');

// 수정 및 등록 에 따른 데이터 처리
if($_GET['code']){
	$mode		= 'popupModifiy';
	$data		= $multipopup->getPopupData($_GET['code']);
	$popupData	= gd_json_decode(stripslashes($data['value']));
}else{
	$mode		= 'popupRegister';
	$newcode	= $multipopup->getNewCode();
}

// 기본값
if(empty($popupData['popup_use']) === true) {
	$popupData['popup_use']				= 'N';				// 멀티 팝업 출력 여부
}
if(empty($popupData['displaySet']) === true) {
	$popupData['displaySet']			= '2_1';			// 이미지 개수
}
if (empty($popupData['popup_dt2tm']) === true) {
	$popupData['popup_dt2tm']			= 'N';				// 기간별 노출 설정
}
else {
	// 시간 설정
	$popupData['popup_stime_h']			= substr($popupData['popup_stime'],0,2);
	$popupData['popup_stime_m']			= substr($popupData['popup_stime'],2,2);
	$popupData['popup_etime_h']			= substr($popupData['popup_etime'],0,2);
	$popupData['popup_etime_m']			= substr($popupData['popup_etime'],2,2);
}
if(empty($popupData['popup_invisible']) === true) {
	$popupData['popup_invisible']		= 'Y';				// 오늘 하루 보이지 않음 여부
}
if(empty($popupData['invisible_bgcolor']) === true) {
	$popupData['invisible_bgcolor']		= 'A8A8A8';			// 오늘 하루 보이지 않음 배경 색상값
}
if(empty($popupData['invisible_fontcolor']) === true) {
	$popupData['invisible_fontcolor']	= 'ffffff';			// 오늘 하루 보이지 않음 폰트 색상값
}
if(empty($popupData['popup_type']) === true) {
	$popupData['popup_type']			= 'window';			// 팝업창 종류
}
if(empty($popupData['outlinePadding']) === true) {
	$popupData['outlinePadding']		= '6';				// 이미지 여백
}
if(empty($popupData['isActType']) === true) {
	$popupData['isActType']				= 'left';			// 큰이미지 이동 방법
}

// 큰이미지 최대값
$maxBigImageSize	= 600;

// 이미지 개수에 따른 출력 값 설정
$_displaySet		= explode('_',$popupData['displaySet']);
?>
<form method="post" name="fm" action="./indb.multipopup.php" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="mode" value="<?php echo $mode;?>" />

<div class="title title_top">멀티 팝업 등록<span>메인에 멀티 팝업에 대한 설정을 추가 / 변경하실 수 있습니다</span> <a href="javascript:manual('<?php echo $guideUrl;?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?php echo $workSkinStr;?>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td width=130>팝업제목</td>
	<td><input type="text" name="text" size="60" value="<?php echo $popupData['text'];?>" class="line" required /></td>
</tr>
<tr>
	<td>팝업코드 </td>
	<td>
		<?php if($_GET['code']){ ?>
		<input type="hidden" name="code" value="<?php echo $_GET['code'];?>" /> <font class="ver8 blue"><b><?php echo $_GET['code'];?></b></font>
		<?php } else { ?>
		<input type="hidden" name="code" value="<?php echo $newcode;?>" /> <font class="ver8 blue"><b><?php echo $newcode;?></b></font>
		<?php } ?>
	</td>
</tr>
<tr>
	<td>출력여부</td>
	<td class="noline">
		<input type="radio" name="popup_use" value="Y" <?php if ($popupData['popup_use'] == 'Y') { echo 'checked="checked"'; }?> />출력
		<input type="radio" name="popup_use" value="N" <?php if ($popupData['popup_use'] != 'Y') { echo 'checked="checked"'; }?> />미출력
	</td>
</tr>
<tr>
	<td>기간별 노출 설정</td>
	<td>
		<div class="noline"><input type="radio" name="popup_dt2tm" value="N" onclick="dt2tm_toggle('N')" <?php if ($popupData['popup_dt2tm'] == 'N') { echo 'checked="checked"'; }?> /> 항상 팝업창이 열립니다.</div>

		<div class="noline" style="margin-top:10px;"><input type="radio" name="popup_dt2tm" value="Y" onclick="dt2tm_toggle('Y')" <?php if ($popupData['popup_dt2tm'] == 'Y') { echo 'checked="checked"'; }?> /> 특정기간동안 팝업창이 열립니다.</div>
		<div id="popup_stime_tg">
			<div style="margin:3px 0px 3px 0px;">
				시작일 : <input type="text" name="popup_sdt_tg" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_sdt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" />
				<select name="popup_stime_tg_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_stime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>시</option>
					<?php } ?>
				</select>
				<select name="popup_stime_tg_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_stime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>분</option>
					<?php } ?>
				</select>
			</div>
			<div style="margin:3px 0px 3px 0px;">
				종료일 : <input type="text" name="popup_edt_tg" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_edt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" />
				<select name="popup_etime_tg_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_etime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>시</option>
					<?php } ?>
				</select>
				<select name="popup_etime_tg_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_etime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>분</option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="noline" style="margin-top:10px;"><input type="radio" name="popup_dt2tm" value="T" onclick="dt2tm_toggle('T')" <?php if ($popupData['popup_dt2tm'] == 'T') { echo 'checked="checked"'; }?> /> 특정기간동안 특정한 시간에만 팝업창이 열립니다.</div>
		<div id="popup_stime">
			<div style="margin:3px 0px 3px 0px;">
				기간 :
				<input type="text" name="popup_sdt" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_sdt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" /> ~
				<input type="text" name="popup_edt" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_edt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" />
			</div>

			<div style="margin:3px 0px 3px 0px;">
				시간 :
				<select name="popup_stime_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_stime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>시</option>
					<?php } ?>
				</select>
				<select name="popup_stime_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_stime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>분</option>
					<?php } ?>
				</select>
				~
				<select name="popup_etime_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_etime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>시</option>
					<?php } ?>
				</select>
				<select name="popup_etime_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_etime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>분</option>
					<?php } ?>
				</select>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>오늘 하루 보이지 않음</td>
	<td>
		<div class="noline">
			<input type="radio" name="popup_invisible" value="Y" onclick="invisible_toggle('Y')" <?php if ($popupData['popup_invisible'] == 'Y') { echo 'checked="checked"'; }?> />출력
			<input type="radio" name="popup_invisible" value="N" onclick="invisible_toggle('N')" <?php if ($popupData['popup_invisible'] == 'N') { echo 'checked="checked"'; }?> />미출력
		</div>
		<div id="invisible_color">
			배경 색상값 입력 #<input type="text" name="invisible_bgcolor" value="<?php echo $popupData['invisible_bgcolor'];?>" size="8" maxlength="6" class="tline"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="색상표 보기" align="absmiddle" /></a>&nbsp;&nbsp;&nbsp;
			폰트 색상값 입력 #<input type="text" name="invisible_fontcolor" value="<?php echo $popupData['invisible_fontcolor'];?>" size="8" maxlength="6" class="tline"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="색상표 보기" align="absmiddle" /></a>
		</div>
	</td>
	</td>
</tr>
<tr>
	<td>창위치</td>
	<td>
		상단에서 : <input type="text" name="popup_spotw" size="6" class="rline" value="<?php echo $popupData['popup_spotw'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		좌측에서 : <input type="text" name="popup_spoth" size="6" class="rline" value="<?php echo $popupData['popup_spoth'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>
	</td>
</tr>
<tr style="display:none">
	<td>창크기</td>
	<td>
		가로크기 : <input type="text" name="popup_sizew" size="6" class="rline" value="<?php echo $popupData['popup_sizew'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		세로크기 : <input type="text" name="popup_sizeh" size="6" class="rline" value="<?php echo $popupData['popup_sizeh'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>
	</td>
</tr>
<tr>
	<td>팝업창 종류</td>
	<td class="noline">
		<input type="radio" name="popup_type" value="window" <?php if ($popupData['popup_type'] == 'window') { echo 'checked="checked"'; }?> />일반 윈도우팝업창
		<input type="radio" name="popup_type" value="layerMove" <?php if ($popupData['popup_type'] == 'layerMove') { echo 'checked="checked"'; }?> />이동레이어
		<input type="radio" name="popup_type" value="layer" <?php if ($popupData['popup_type'] == 'layer') { echo 'checked="checked"'; }?> />고정레이어
	</td>
</tr>
<tr>
	<td>큰이미지 이동 방법</td>
	<td class="noline">
		<select name="isActType">
			<option value="none" <?php if ($popupData['isActType'] == 'none') { echo 'selected="selected"';}?>>이동하지 않음 (고정)</option>
			<option value="left" <?php if ($popupData['isActType'] == 'left') { echo 'selected="selected"';}?>>오른쪽에서 왼쪽으로 이동</option>
			<option value="right" <?php if ($popupData['isActType'] == 'right') { echo 'selected="selected"';}?>>왼쪽에서 오른쪽으로 이동</option>
			<option value="up" <?php if ($popupData['isActType'] == 'up') { echo 'selected="selected"';}?>>아래쪽에서 위쪽으로 이동</option>
			<option value="down" <?php if ($popupData['isActType'] == 'down') { echo 'selected="selected"';}?>>위쪽에서 아래쪽으로 이동</option>
			<!--<option value="fade" <?php if ($popupData['isActType'] == 'fade') { echo 'selected="selected"';}?>>서서히 사라졌다 나옴</option>-->
		</select>
	</td>
</tr>
<tr>
	<td>이동시 속도 선택</td>
	<td class="noline">
		<select name="nDelay">
		<?php for ($i = 2; $i <= 6; $i++) {?>
			<option value="<?php echo ($i * 1000);?>" <?php if ($popupData['nDelay'] == ($i * 1000)) { echo 'selected="selected"';}?>><?php echo $i;?> 초</option>
		<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<td>이미지 여백</td>
	<td class="noline">
		<select name="outlinePadding" onchange="setImgSize();" id="outlinePadding">
		<?php for($i = 0; $i <= 10; $i++) {?>
			<option value="<?php echo $i;?>" <?php if ($popupData['outlinePadding'] == $i) { echo 'selected="selected"';}?>><?php echo $i;?> pixel</option>
		<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<td>이미지 개수</td>
	<td class="noline">
		<input type="radio" name="displaySet" value="2_1" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '2_1') { echo 'checked="checked"'; }?> /> 2 × 1&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="2_2" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '2_2') { echo 'checked="checked"'; }?> /> 2 × 2&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="3_1" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '3_1') { echo 'checked="checked"'; }?> /> 3 × 1&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="3_2" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '3_2') { echo 'checked="checked"'; }?> /> 3 × 2&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="4_1" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '4_1') { echo 'checked="checked"'; }?> /> 4 × 1&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="4_2" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '4_2') { echo 'checked="checked"'; }?> /> 4 × 2&nbsp;&nbsp;&nbsp;
		<font class="ver8 blue">* 가로개수 X 세로개수 입니다.</font>
	</td>
</tr>
<tr>
	<td>큰이미지사이즈</td>
	<td>
		가로크기 : <input type="text" name="mainImgSizew" size="6" class="rline" value="<?php echo $popupData['mainImgSizew'];?>" onkeydown="onlynumber();" onchange="setImgSize();" required /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		세로크기 : <input type="text" name="mainImgSizeh" size="6" class="rline" value="<?php echo $popupData['mainImgSizeh'];?>" onkeydown="onlynumber();" onchange="setImgSize();" required /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		<font class="ver8 blue">* 최대 이미지 사이즈는 <?php echo $maxBigImageSize;?> X <?php echo $maxBigImageSize;?> pixel 입니다.</font>
	</td>
</tr>
<tr>
	<td>작은이미지사이즈</td>
	<td>
		가로크기 : <input type=text size="6" class="rline" id='__mouseImgSizew' value="<?php echo $popupData['mouseImgSizew'];?>" disabled="disabled" />
		<input type="hidden" name="mouseImgSizew" value="<?php echo $popupData['mouseImgSizew'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		세로크기 : <input type=text size="6" class="rline" id='__mouseImgSizeh' value="<?php echo $popupData['mouseImgSizeh'];?>" disabled="disabled" />
		<input type="hidden" name="mouseImgSizeh" value="<?php echo $popupData['mouseImgSizeh'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		<font class="ver8 blue">* 큰 이미지 사이즈를 입력하면 자동 계산됩니다.</font>
	</td>
</tr>
<tr>
	<td>팝업 이미지</td>
	<td>

		<div id="layBottom">
			<table border="1">
			<tr>
				<td width="340" height="340" class="mimgView" align="center">
					<?php echo $multipopup->popupimg($popupData['mainBannerImg'][1],'300');?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table id="contentTable" width="100%" class="tb">
<?php
			// 이미지 갯수
			$row		= $_displaySet[0];
			$col		= $_displaySet[1];
			$indexKey	= 1;

			// 등록된 이미지 출력
			for( $i =1; $i<= $col; $i++){
				echo '<tr>';
				for( $j=1; $j<=$row; $j++){
?>
						<td align="center">
							<img src="../img/btn_delinum_confirm.gif" onclick="selupload('<?php echo $indexKey;?>');" class="hand" alt="이미지 등록하기" />
							<div class="simgView_<?php echo $indexKey;?>"><?php echo $multipopup->popupimg($popupData['mouseOutImg'][$indexKey],'80');?></div>
							<input type="hidden" name="image_attach_method[<?php echo $indexKey;?>]" id="image_attach_method<?php echo $indexKey;?>" value="<?php echo $popupData['image_attach_method'][$indexKey];?>" />
							<input type="hidden" name="mouseOnImg[<?php echo $indexKey;?>]" id="mouseOnImg<?php echo $indexKey;?>" value="<?php echo $popupData['mouseOnImg'][$indexKey];?>" />
							<input type="hidden" name="mouseOutImg[<?php echo $indexKey;?>]" id="mouseOutImg<?php echo $indexKey;?>" value="<?php echo $popupData['mouseOutImg'][$indexKey];?>" />
							<input type="hidden" name="mainBannerImg[<?php echo $indexKey;?>]" id="mainBannerImg<?php echo $indexKey;?>" value="<?php echo $popupData['mainBannerImg'][$indexKey];?>" />
							<input type="hidden" name="linkUrl[<?php echo $indexKey;?>]" id="linkUrl<?php echo $indexKey;?>" value="<?php echo $popupData['linkUrl'][$indexKey];?>" />
							<input type="hidden" name="linkTarget[<?php echo $indexKey;?>]" id="linkTarget<?php echo $indexKey;?>" value="<?php echo $popupData['linkTarget'][$indexKey];?>" />

							<input type="hidden" name="prev_mouseOnImg[<?php echo $indexKey;?>]"  value="<?php echo $popupData['mouseOnImg'][$indexKey];?>">
							<input type="hidden" name="prev_mouseOutImg[<?php echo $indexKey;?>]"  value="<?php echo $popupData['mouseOutImg'][$indexKey];?>">
							<input type="hidden" name="prev_mainBannerImg[<?php echo $indexKey;?>]"  value="<?php echo $popupData['mainBannerImg'][$indexKey];?>">
						</td>
<?php
					$indexKey++;
				}
				echo '</tr>';
			}
?>

			</table>
		</td>
	</tr>
	</table>
	</div>

	<div style="padding-top:10px;font-weight:bold;" class="ver9 blue"> * 쇼핑몰에 실제 노출되는 팝업이미지는 저장 후 화면보기 버튼을 클릭하여 확인하세요. </div>

	</td>
</tr>
</table>

<div style="padding:20px 0px 50px 0px" align="center" class="noline">
	<input type="image" src="../img/btn_save.gif" alt="저장" />
	<?php if ($_GET['code']) {?><a href="javascript:popup2('../../proc/multipopup_content.php?code=<?php echo $popupData['code'];?>','<?php echo $popupData['popup_sizew'];?>','<?php echo $popupData['popup_sizeh'];?>')"><img src="../img/btn_html_page_view.gif" alt="미리보기" /></a><?}?>
	<a href="./iframe.multi_popup_list.php"><img src="../img/btn_list.gif" border="0" alt="목록" /></a>
</div>

</form>

<script>
table_design_load();		// 설정 화면 갱신
setHeight_ifrmCodi();		// 설정 화면 높이 갱신

var jq = jQuery.noConflict();

// 위치나 갯수 선택시 기존 데이터 보존을 위해 저장 (최대 8개까지 저장)
var imgDataTemp		= new Array();
for(var i = 1; i <= 8; i++) {
	imgDataTemp[i]	= new Array();
}

/**
 * 기간별 노출 설정 출력 여부
 * @param string thisCode 여부
 */
function dt2tm_toggle(thisCode)
{
	if (thisCode == 'N') {
		jq('#popup_stime_tg').hide();
		jq('#popup_stime').hide();
	} else if (thisCode == 'Y') {
		jq('#popup_stime_tg').show();
		jq('#popup_stime').hide();
	} else if (thisCode == 'T') {
		jq('#popup_stime_tg').hide();
		jq('#popup_stime').show();
	}
}

/**
 * 오늘 하루 보이지 않음 출력 여부
 * @param string thisCode 여부
 */
function invisible_toggle(thisCode)
{
	if (thisCode == 'N') {
		jq('#invisible_color').hide();
	} else if (thisCode == 'Y') {
		jq('#invisible_color').show();
	}
}

function setImgDataTemp()
{
	var tmp				= jq('input[name=displaySet]:checked').val().split('_');
	var setImgCount		= tmp[0] * tmp[1];

	for(var i=1; i <= setImgCount; i++)
	{
		imgDataTemp[i][0]	= jq('#image_attach_method'+i).val();
		imgDataTemp[i][1]	= jq('#mouseOnImg'+i).val();
		imgDataTemp[i][2]	= jq('#mouseOutImg'+i).val();
		imgDataTemp[i][3]	= jq('#mainBannerImg'+i).val();
		imgDataTemp[i][4]	= jq('#linkUrl'+i).val();
		imgDataTemp[i][5]	= jq('#linkTarget'+i).val();
	}
}

/**
 * 작은 이미지 갯수에 따른 등록 테이블 만들기
 */
function thumbnail_image_display()
{
	var tmp			= jq('input[name=displaySet]:checked').val().split('_');
	var row			= parseInt(tmp[0]);		// 작은 이미지 가로 갯수
	var col			= parseInt(tmp[1]);		// 작은 이미지 세로 갯수
	var setImgCount	= row * col;			// 작은 이미지 갯수
	var html		= '';
	var indexKey	= 1;

	// 작은 이미지 갯수에 따른 등록 테이블 만들기
	for(var i =1; i<= col; i++)
	{
		html +='<tr>';
		for(var j=1; j<=row; j++)
		{
			html +=	'<td height="61" align="center">';
			html += '<img src="../img/btn_delinum_confirm.gif" onclick="selupload(\''+indexKey+'\');" class="hand" alt="이미지 등록하기" />';
			html += '<div class="simgView_'+indexKey+'"></div>';
			html +=	'<input type="hidden" name="image_attach_method['+indexKey+']" id="image_attach_method'+indexKey+'" />';
			html +=	'<input type="hidden" name="mouseOnImg['+indexKey+']" id="mouseOnImg'+indexKey+'" />';
			html +=	'<input type="hidden" name="mouseOutImg['+indexKey+']" id="mouseOutImg'+indexKey+'" />';
			html +=	'<input type="hidden" name="mainBannerImg['+indexKey+']" id="mainBannerImg'+indexKey+'" />';
			html +=	'<input type="hidden" name="linkUrl['+indexKey+']" id="linkUrl'+indexKey+'" />';
			html +=	'<input type="hidden" name="linkTarget['+indexKey+']" id="linkTarget'+indexKey+'" />';
			html += '</td>';
			indexKey++;
		}
		html +='</tr>';
	}

	jq("#contentTable").html(html);

	// 기존에 등록된 이미지가 있는 경우 저장된 값으로 갱신
	for(i = 1; i <= setImgCount; i++)
	{
		jq('#image_attach_method'+i).val(imgDataTemp[i][0]);
		jq('#mouseOnImg'+i).val(imgDataTemp[i][1]);
		jq('#mouseOutImg'+i).val(imgDataTemp[i][2]);
		jq('#mainBannerImg'+i).val(imgDataTemp[i][3]);
		jq('#linkUrl'+i).val(imgDataTemp[i][4]);
		jq('#linkTarget'+i).val(imgDataTemp[i][5]);

		imgTableView(jq(".simgView_"+i),imgDataTemp[i][2],80);
	}

	imgTableView(jq(".mimgView"),imgDataTemp[1][3],300);
	setImgSize();
	setImgDataTemp();
	setHeight_ifrmCodi();		// 설정 화면 높이 갱신
}

/**
 * 이미지 출력
 */
function imgTableView(obj,viewImg,w)
{
	if(!viewImg) return;

	var dir;
	if(viewImg.indexOf("tmp_") > -1) dir = 'tmp_skinCopy';
	else if(viewImg.indexOf("ori_") > -1) dir = 'multipopup';

	target = obj;

	if(/^http(s)?:\/\//.test(viewImg)){
		src = viewImg;
	}else{
		src = "../../data/" + dir + "/" + viewImg;
	}

	target.html("<img src='" + src + "' width="+w+" />");
}

/**
 * 이미지 등록
 */
function selupload(indexKey)
{
	parent.popupLayerNotice('멀티팝업이미지 등록','./popup.multi_popup_upload.php?indexKey='+indexKey,580,370);
}

/**
 * 멀티 팝업의 큰이미지 사이즈, 작은 이미지 사이즈, 팝업창 사이즈 설정
 */
function setImgSize()
{
	var mainImgSizew	= parseInt(jq('input[name=mainImgSizew]').val());	// 가로 사이즈
	var mainImgSizeh	= parseInt(jq('input[name=mainImgSizeh]').val());	// 세로 사이즈

	// 가로 사이즈가 최대값을 넘으면 최대값으로 수정
	if(mainImgSizew > <?php echo $maxBigImageSize;?> ){
		jq('input[name=mainImgSizew]').val(<?php echo $maxBigImageSize;?>);
		mainImgSizew	= <?php echo $maxBigImageSize;?>;
	}

	// 세로 사이즈가 최대값을 넘으면 최대값으로 수정
	if(mainImgSizeh > <?php echo $maxBigImageSize;?> ){
		jq('input[name=mainImgSizeh]').val(<?php echo $maxBigImageSize;?>);
		mainImgSizeh	= <?php echo $maxBigImageSize;?>;
	}

	var outlinePadding	= parseInt(jq('#outlinePadding').val());	// 이미지 여백
	var popupInvisibleHeight	= 20;

	var tmp		= jq('input[name=displaySet]:checked').val().split('_');	// 이미지 개수
	var row		= parseInt(tmp[0]);		// 작은 이미지 가로 갯수
	var col		= parseInt(tmp[1]);		// 작은 이미지 세로 갯수

	// 작은 이미지 사이즈
	var buttonImgSizew	= Math.floor(mainImgSizew/row);
	var buttonImgSizeh	= Math.floor(buttonImgSizew/(mainImgSizew/mainImgSizeh));

	jq('input[name=mouseImgSizew]').val(buttonImgSizew);
	jq('input[name=mouseImgSizeh]').val(buttonImgSizeh);

	if(buttonImgSizew) jq('#__mouseImgSizew').val(buttonImgSizew);
	if(buttonImgSizeh) jq('#__mouseImgSizeh').val(buttonImgSizeh);

	// 멀티 팝업 사이즈
	var popupSizew = jq('input[name=popup_sizew]').val(mainImgSizew+(outlinePadding*2));//메인배너가로 + 가로패팅
	var popupSizeh = jq('input[name=popup_sizeh]').val(Math.floor((mainImgSizeh+(buttonImgSizeh*col))+(popupInvisibleHeight)+(outlinePadding*2)+outlinePadding)); //메인배너세로 + 오버이미지높이 + 오늘하루보임높이 + 세로패팅
}

setImgDataTemp();
<?if(!$_GET['code']){?>thumbnail_image_display();<?}?>

// 기간별 노출 설정
dt2tm_toggle('<?php echo $popupData['popup_dt2tm'];?>');

// 오늘 하루 보이지 않음 출력 여부
invisible_toggle('<?php echo $popupData['popup_invisible'];?>')
</script>