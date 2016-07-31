<?php
/**
 * ��Ƽ �˾� ��� ������
 * @author cjb3333 , artherot @ godosoft development team.
 */

$scriptLoad='<script src="./codi/_codi.js"></script>';
include "../_header.popup.php";

// ��Ƽ �˾� Class
$multipopup = Core::loader('MultiPopup');

// ���� �� ��� �� ���� ������ ó��
if($_GET['code']){
	$mode		= 'popupModifiy';
	$data		= $multipopup->getPopupData($_GET['code']);
	$popupData	= gd_json_decode(stripslashes($data['value']));
}else{
	$mode		= 'popupRegister';
	$newcode	= $multipopup->getNewCode();
}

// �⺻��
if(empty($popupData['popup_use']) === true) {
	$popupData['popup_use']				= 'N';				// ��Ƽ �˾� ��� ����
}
if(empty($popupData['displaySet']) === true) {
	$popupData['displaySet']			= '2_1';			// �̹��� ����
}
if (empty($popupData['popup_dt2tm']) === true) {
	$popupData['popup_dt2tm']			= 'N';				// �Ⱓ�� ���� ����
}
else {
	// �ð� ����
	$popupData['popup_stime_h']			= substr($popupData['popup_stime'],0,2);
	$popupData['popup_stime_m']			= substr($popupData['popup_stime'],2,2);
	$popupData['popup_etime_h']			= substr($popupData['popup_etime'],0,2);
	$popupData['popup_etime_m']			= substr($popupData['popup_etime'],2,2);
}
if(empty($popupData['popup_invisible']) === true) {
	$popupData['popup_invisible']		= 'Y';				// ���� �Ϸ� ������ ���� ����
}
if(empty($popupData['invisible_bgcolor']) === true) {
	$popupData['invisible_bgcolor']		= 'A8A8A8';			// ���� �Ϸ� ������ ���� ��� ����
}
if(empty($popupData['invisible_fontcolor']) === true) {
	$popupData['invisible_fontcolor']	= 'ffffff';			// ���� �Ϸ� ������ ���� ��Ʈ ����
}
if(empty($popupData['popup_type']) === true) {
	$popupData['popup_type']			= 'window';			// �˾�â ����
}
if(empty($popupData['outlinePadding']) === true) {
	$popupData['outlinePadding']		= '6';				// �̹��� ����
}
if(empty($popupData['isActType']) === true) {
	$popupData['isActType']				= 'left';			// ū�̹��� �̵� ���
}

// ū�̹��� �ִ밪
$maxBigImageSize	= 600;

// �̹��� ������ ���� ��� �� ����
$_displaySet		= explode('_',$popupData['displaySet']);
?>
<form method="post" name="fm" action="./indb.multipopup.php" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="mode" value="<?php echo $mode;?>" />

<div class="title title_top">��Ƽ �˾� ���<span>���ο� ��Ƽ �˾��� ���� ������ �߰� / �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?php echo $guideUrl;?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?php echo $workSkinStr;?>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td width=130>�˾�����</td>
	<td><input type="text" name="text" size="60" value="<?php echo $popupData['text'];?>" class="line" required /></td>
</tr>
<tr>
	<td>�˾��ڵ� </td>
	<td>
		<?php if($_GET['code']){ ?>
		<input type="hidden" name="code" value="<?php echo $_GET['code'];?>" /> <font class="ver8 blue"><b><?php echo $_GET['code'];?></b></font>
		<?php } else { ?>
		<input type="hidden" name="code" value="<?php echo $newcode;?>" /> <font class="ver8 blue"><b><?php echo $newcode;?></b></font>
		<?php } ?>
	</td>
</tr>
<tr>
	<td>��¿���</td>
	<td class="noline">
		<input type="radio" name="popup_use" value="Y" <?php if ($popupData['popup_use'] == 'Y') { echo 'checked="checked"'; }?> />���
		<input type="radio" name="popup_use" value="N" <?php if ($popupData['popup_use'] != 'Y') { echo 'checked="checked"'; }?> />�����
	</td>
</tr>
<tr>
	<td>�Ⱓ�� ���� ����</td>
	<td>
		<div class="noline"><input type="radio" name="popup_dt2tm" value="N" onclick="dt2tm_toggle('N')" <?php if ($popupData['popup_dt2tm'] == 'N') { echo 'checked="checked"'; }?> /> �׻� �˾�â�� �����ϴ�.</div>

		<div class="noline" style="margin-top:10px;"><input type="radio" name="popup_dt2tm" value="Y" onclick="dt2tm_toggle('Y')" <?php if ($popupData['popup_dt2tm'] == 'Y') { echo 'checked="checked"'; }?> /> Ư���Ⱓ���� �˾�â�� �����ϴ�.</div>
		<div id="popup_stime_tg">
			<div style="margin:3px 0px 3px 0px;">
				������ : <input type="text" name="popup_sdt_tg" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_sdt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" />
				<select name="popup_stime_tg_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_stime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>��</option>
					<?php } ?>
				</select>
				<select name="popup_stime_tg_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_stime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>��</option>
					<?php } ?>
				</select>
			</div>
			<div style="margin:3px 0px 3px 0px;">
				������ : <input type="text" name="popup_edt_tg" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_edt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" />
				<select name="popup_etime_tg_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_etime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>��</option>
					<?php } ?>
				</select>
				<select name="popup_etime_tg_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_etime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>��</option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="noline" style="margin-top:10px;"><input type="radio" name="popup_dt2tm" value="T" onclick="dt2tm_toggle('T')" <?php if ($popupData['popup_dt2tm'] == 'T') { echo 'checked="checked"'; }?> /> Ư���Ⱓ���� Ư���� �ð����� �˾�â�� �����ϴ�.</div>
		<div id="popup_stime">
			<div style="margin:3px 0px 3px 0px;">
				�Ⱓ :
				<input type="text" name="popup_sdt" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_sdt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" /> ~
				<input type="text" name="popup_edt" size="10" maxlength="8" class="tline center" value="<?php echo $popupData['popup_edt'];?>" onkeydown="onlynumber();" onclick="calendar(event);" readonly="readonly" />
			</div>

			<div style="margin:3px 0px 3px 0px;">
				�ð� :
				<select name="popup_stime_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_stime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>��</option>
					<?php } ?>
				</select>
				<select name="popup_stime_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_stime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>��</option>
					<?php } ?>
				</select>
				~
				<select name="popup_etime_h">
					<?php for ($h = 0; $h < 24; $h++) { ?>
					<option value="<?php echo sprintf('%02d',$h);?>" <?php if ($popupData['popup_etime_h'] == $h) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$h);?>��</option>
					<?php } ?>
				</select>
				<select name="popup_etime_m">
					<?php for ($m = 0; $m <= 59; $m++) { ?>
					<option value="<?php echo sprintf('%02d',$m);?>" <?php if ($popupData['popup_etime_m'] == $m) { echo 'selected="selected"';}?>><?php echo sprintf('%02d',$m);?>��</option>
					<?php } ?>
				</select>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>���� �Ϸ� ������ ����</td>
	<td>
		<div class="noline">
			<input type="radio" name="popup_invisible" value="Y" onclick="invisible_toggle('Y')" <?php if ($popupData['popup_invisible'] == 'Y') { echo 'checked="checked"'; }?> />���
			<input type="radio" name="popup_invisible" value="N" onclick="invisible_toggle('N')" <?php if ($popupData['popup_invisible'] == 'N') { echo 'checked="checked"'; }?> />�����
		</div>
		<div id="invisible_color">
			��� ���� �Է� #<input type="text" name="invisible_bgcolor" value="<?php echo $popupData['invisible_bgcolor'];?>" size="8" maxlength="6" class="tline"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a>&nbsp;&nbsp;&nbsp;
			��Ʈ ���� �Է� #<input type="text" name="invisible_fontcolor" value="<?php echo $popupData['invisible_fontcolor'];?>" size="8" maxlength="6" class="tline"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a>
		</div>
	</td>
	</td>
</tr>
<tr>
	<td>â��ġ</td>
	<td>
		��ܿ��� : <input type="text" name="popup_spotw" size="6" class="rline" value="<?php echo $popupData['popup_spotw'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		�������� : <input type="text" name="popup_spoth" size="6" class="rline" value="<?php echo $popupData['popup_spoth'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>
	</td>
</tr>
<tr style="display:none">
	<td>âũ��</td>
	<td>
		����ũ�� : <input type="text" name="popup_sizew" size="6" class="rline" value="<?php echo $popupData['popup_sizew'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		����ũ�� : <input type="text" name="popup_sizeh" size="6" class="rline" value="<?php echo $popupData['popup_sizeh'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>
	</td>
</tr>
<tr>
	<td>�˾�â ����</td>
	<td class="noline">
		<input type="radio" name="popup_type" value="window" <?php if ($popupData['popup_type'] == 'window') { echo 'checked="checked"'; }?> />�Ϲ� �������˾�â
		<input type="radio" name="popup_type" value="layerMove" <?php if ($popupData['popup_type'] == 'layerMove') { echo 'checked="checked"'; }?> />�̵����̾�
		<input type="radio" name="popup_type" value="layer" <?php if ($popupData['popup_type'] == 'layer') { echo 'checked="checked"'; }?> />�������̾�
	</td>
</tr>
<tr>
	<td>ū�̹��� �̵� ���</td>
	<td class="noline">
		<select name="isActType">
			<option value="none" <?php if ($popupData['isActType'] == 'none') { echo 'selected="selected"';}?>>�̵����� ���� (����)</option>
			<option value="left" <?php if ($popupData['isActType'] == 'left') { echo 'selected="selected"';}?>>�����ʿ��� �������� �̵�</option>
			<option value="right" <?php if ($popupData['isActType'] == 'right') { echo 'selected="selected"';}?>>���ʿ��� ���������� �̵�</option>
			<option value="up" <?php if ($popupData['isActType'] == 'up') { echo 'selected="selected"';}?>>�Ʒ��ʿ��� �������� �̵�</option>
			<option value="down" <?php if ($popupData['isActType'] == 'down') { echo 'selected="selected"';}?>>���ʿ��� �Ʒ������� �̵�</option>
			<!--<option value="fade" <?php if ($popupData['isActType'] == 'fade') { echo 'selected="selected"';}?>>������ ������� ����</option>-->
		</select>
	</td>
</tr>
<tr>
	<td>�̵��� �ӵ� ����</td>
	<td class="noline">
		<select name="nDelay">
		<?php for ($i = 2; $i <= 6; $i++) {?>
			<option value="<?php echo ($i * 1000);?>" <?php if ($popupData['nDelay'] == ($i * 1000)) { echo 'selected="selected"';}?>><?php echo $i;?> ��</option>
		<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<td>�̹��� ����</td>
	<td class="noline">
		<select name="outlinePadding" onchange="setImgSize();" id="outlinePadding">
		<?php for($i = 0; $i <= 10; $i++) {?>
			<option value="<?php echo $i;?>" <?php if ($popupData['outlinePadding'] == $i) { echo 'selected="selected"';}?>><?php echo $i;?> pixel</option>
		<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<td>�̹��� ����</td>
	<td class="noline">
		<input type="radio" name="displaySet" value="2_1" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '2_1') { echo 'checked="checked"'; }?> /> 2 �� 1&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="2_2" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '2_2') { echo 'checked="checked"'; }?> /> 2 �� 2&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="3_1" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '3_1') { echo 'checked="checked"'; }?> /> 3 �� 1&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="3_2" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '3_2') { echo 'checked="checked"'; }?> /> 3 �� 2&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="4_1" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '4_1') { echo 'checked="checked"'; }?> /> 4 �� 1&nbsp;&nbsp;&nbsp;
		<input type="radio" name="displaySet" value="4_2" onclick="thumbnail_image_display();" <?php if ($popupData['displaySet'] == '4_2') { echo 'checked="checked"'; }?> /> 4 �� 2&nbsp;&nbsp;&nbsp;
		<font class="ver8 blue">* ���ΰ��� X ���ΰ��� �Դϴ�.</font>
	</td>
</tr>
<tr>
	<td>ū�̹���������</td>
	<td>
		����ũ�� : <input type="text" name="mainImgSizew" size="6" class="rline" value="<?php echo $popupData['mainImgSizew'];?>" onkeydown="onlynumber();" onchange="setImgSize();" required /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		����ũ�� : <input type="text" name="mainImgSizeh" size="6" class="rline" value="<?php echo $popupData['mainImgSizeh'];?>" onkeydown="onlynumber();" onchange="setImgSize();" required /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		<font class="ver8 blue">* �ִ� �̹��� ������� <?php echo $maxBigImageSize;?> X <?php echo $maxBigImageSize;?> pixel �Դϴ�.</font>
	</td>
</tr>
<tr>
	<td>�����̹���������</td>
	<td>
		����ũ�� : <input type=text size="6" class="rline" id='__mouseImgSizew' value="<?php echo $popupData['mouseImgSizew'];?>" disabled="disabled" />
		<input type="hidden" name="mouseImgSizew" value="<?php echo $popupData['mouseImgSizew'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		����ũ�� : <input type=text size="6" class="rline" id='__mouseImgSizeh' value="<?php echo $popupData['mouseImgSizeh'];?>" disabled="disabled" />
		<input type="hidden" name="mouseImgSizeh" value="<?php echo $popupData['mouseImgSizeh'];?>" onkeydown="onlynumber();" /> <font class="ver8 blue">pixel</font>&nbsp;&nbsp;&nbsp;
		<font class="ver8 blue">* ū �̹��� ����� �Է��ϸ� �ڵ� ���˴ϴ�.</font>
	</td>
</tr>
<tr>
	<td>�˾� �̹���</td>
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
			// �̹��� ����
			$row		= $_displaySet[0];
			$col		= $_displaySet[1];
			$indexKey	= 1;

			// ��ϵ� �̹��� ���
			for( $i =1; $i<= $col; $i++){
				echo '<tr>';
				for( $j=1; $j<=$row; $j++){
?>
						<td align="center">
							<img src="../img/btn_delinum_confirm.gif" onclick="selupload('<?php echo $indexKey;?>');" class="hand" alt="�̹��� ����ϱ�" />
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

	<div style="padding-top:10px;font-weight:bold;" class="ver9 blue"> * ���θ��� ���� ����Ǵ� �˾��̹����� ���� �� ȭ�麸�� ��ư�� Ŭ���Ͽ� Ȯ���ϼ���. </div>

	</td>
</tr>
</table>

<div style="padding:20px 0px 50px 0px" align="center" class="noline">
	<input type="image" src="../img/btn_save.gif" alt="����" />
	<?php if ($_GET['code']) {?><a href="javascript:popup2('../../proc/multipopup_content.php?code=<?php echo $popupData['code'];?>','<?php echo $popupData['popup_sizew'];?>','<?php echo $popupData['popup_sizeh'];?>')"><img src="../img/btn_html_page_view.gif" alt="�̸�����" /></a><?}?>
	<a href="./iframe.multi_popup_list.php"><img src="../img/btn_list.gif" border="0" alt="���" /></a>
</div>

</form>

<script>
table_design_load();		// ���� ȭ�� ����
setHeight_ifrmCodi();		// ���� ȭ�� ���� ����

var jq = jQuery.noConflict();

// ��ġ�� ���� ���ý� ���� ������ ������ ���� ���� (�ִ� 8������ ����)
var imgDataTemp		= new Array();
for(var i = 1; i <= 8; i++) {
	imgDataTemp[i]	= new Array();
}

/**
 * �Ⱓ�� ���� ���� ��� ����
 * @param string thisCode ����
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
 * ���� �Ϸ� ������ ���� ��� ����
 * @param string thisCode ����
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
 * ���� �̹��� ������ ���� ��� ���̺� �����
 */
function thumbnail_image_display()
{
	var tmp			= jq('input[name=displaySet]:checked').val().split('_');
	var row			= parseInt(tmp[0]);		// ���� �̹��� ���� ����
	var col			= parseInt(tmp[1]);		// ���� �̹��� ���� ����
	var setImgCount	= row * col;			// ���� �̹��� ����
	var html		= '';
	var indexKey	= 1;

	// ���� �̹��� ������ ���� ��� ���̺� �����
	for(var i =1; i<= col; i++)
	{
		html +='<tr>';
		for(var j=1; j<=row; j++)
		{
			html +=	'<td height="61" align="center">';
			html += '<img src="../img/btn_delinum_confirm.gif" onclick="selupload(\''+indexKey+'\');" class="hand" alt="�̹��� ����ϱ�" />';
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

	// ������ ��ϵ� �̹����� �ִ� ��� ����� ������ ����
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
	setHeight_ifrmCodi();		// ���� ȭ�� ���� ����
}

/**
 * �̹��� ���
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
 * �̹��� ���
 */
function selupload(indexKey)
{
	parent.popupLayerNotice('��Ƽ�˾��̹��� ���','./popup.multi_popup_upload.php?indexKey='+indexKey,580,370);
}

/**
 * ��Ƽ �˾��� ū�̹��� ������, ���� �̹��� ������, �˾�â ������ ����
 */
function setImgSize()
{
	var mainImgSizew	= parseInt(jq('input[name=mainImgSizew]').val());	// ���� ������
	var mainImgSizeh	= parseInt(jq('input[name=mainImgSizeh]').val());	// ���� ������

	// ���� ����� �ִ밪�� ������ �ִ밪���� ����
	if(mainImgSizew > <?php echo $maxBigImageSize;?> ){
		jq('input[name=mainImgSizew]').val(<?php echo $maxBigImageSize;?>);
		mainImgSizew	= <?php echo $maxBigImageSize;?>;
	}

	// ���� ����� �ִ밪�� ������ �ִ밪���� ����
	if(mainImgSizeh > <?php echo $maxBigImageSize;?> ){
		jq('input[name=mainImgSizeh]').val(<?php echo $maxBigImageSize;?>);
		mainImgSizeh	= <?php echo $maxBigImageSize;?>;
	}

	var outlinePadding	= parseInt(jq('#outlinePadding').val());	// �̹��� ����
	var popupInvisibleHeight	= 20;

	var tmp		= jq('input[name=displaySet]:checked').val().split('_');	// �̹��� ����
	var row		= parseInt(tmp[0]);		// ���� �̹��� ���� ����
	var col		= parseInt(tmp[1]);		// ���� �̹��� ���� ����

	// ���� �̹��� ������
	var buttonImgSizew	= Math.floor(mainImgSizew/row);
	var buttonImgSizeh	= Math.floor(buttonImgSizew/(mainImgSizew/mainImgSizeh));

	jq('input[name=mouseImgSizew]').val(buttonImgSizew);
	jq('input[name=mouseImgSizeh]').val(buttonImgSizeh);

	if(buttonImgSizew) jq('#__mouseImgSizew').val(buttonImgSizew);
	if(buttonImgSizeh) jq('#__mouseImgSizeh').val(buttonImgSizeh);

	// ��Ƽ �˾� ������
	var popupSizew = jq('input[name=popup_sizew]').val(mainImgSizew+(outlinePadding*2));//���ι�ʰ��� + ��������
	var popupSizeh = jq('input[name=popup_sizeh]').val(Math.floor((mainImgSizeh+(buttonImgSizeh*col))+(popupInvisibleHeight)+(outlinePadding*2)+outlinePadding)); //���ι�ʼ��� + �����̹������� + �����Ϸ纸�ӳ��� + ��������
}

setImgDataTemp();
<?if(!$_GET['code']){?>thumbnail_image_display();<?}?>

// �Ⱓ�� ���� ����
dt2tm_toggle('<?php echo $popupData['popup_dt2tm'];?>');

// ���� �Ϸ� ������ ���� ��� ����
invisible_toggle('<?php echo $popupData['popup_invisible'];?>')
</script>