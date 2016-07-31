<?php
$location = 'SNS ���� > �ν������� ����';
$SET_HTML_DEFINE = true;
include '../_header.php';

$sampleInsgoWidget_user = 'godomall';
$sampleInsgoWidget_tag = '����';
?>
<style type="text/css">
.insgoWidget_layoutArea { width: 800px; }
.insgoWidget_layoutArea .inputText { width: 150px; height: 25px; }
.insgoWidget_layoutArea .inputText2 { width: 50px; height: 25px; }
.insgoWidget_layoutArea .sampleColor { color: #ACACAC; }
.insgoWidget_layoutArea .buttonArea { width: 100%; text-align: center; margin: 30px 0px 50px 0px; }
.insgoWidget_infoArea {
	font-family: dotum;
	font-size: 13px;
	width: 800px;
	height: 157px;
	background-image: url('../img/bg_dormant.jpg');
	background-repeat:no-repeat;
	background-size: auto;
	padding-bottom: 40px;
}
.insgoWidget_infoArea .insgoWidget_infoArea_subject {
	font-weight: bold;
	color: red;
	font-size: 16px;
	padding: 60px 0px 0px 100px;
	float: left;
}
.insgoWidget_infoArea .insgoWidget_infoArea_content {
	padding: 30px 50px 0px 0px;
	float: right;
}
</style>

<div class="insgoWidget_layoutArea">
	<form name="insgoWidgetForm" id="insgoWidgetForm" method="post">
	<div class="title title_top">�ν������� �ȳ�</div>

	<div class="insgoWidget_infoArea">
		<div class="insgoWidget_infoArea_subject">
			<div>�ν���������</div>
			<div>����Ϸ���?</div>
		</div>
		<div class="insgoWidget_infoArea_content">
			<div style="font-weight: bold;">1. ���θ��� <span style="color: blue;">�ν�Ÿ�׷� ������ ����</span>�ϰ� �������� ������ּ���.</div>
			<div style="margin-left: 20px;">�������� ����� �� ������� ���� ����ϴ� �ؽ��±׸�</div>
			<div style="margin-left: 20px;">�Բ� ����� �ֽø� ȿ�����Դϴ�.</div>
			<div style="font-weight: bold;">2. �ν������� �������� ���θ��� ���Ե� <span style="color: blue;">������ ����</span>���ּ���.</div>
			<div style="font-weight: bold;">3. �̸����⸦ ���� ������ Ȯ���ϰ� <span style="color: blue;">�ҽ��� ����</span>���ּ���.</div>
			<div style="font-weight: bold;">4. �����ΰ������� ������ ����� �������� <span style="color: blue;">����� �ҽ��� ����</span>���ּ���.</div>
		</div>
	</div>

	<div class="title title_top">�ν������� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=24')"><img src="../img/btn_q.gif"   align=absmiddle hspace=2/></a> <span>���θ��� ���Ե� �ν������� ���� or �ؽ��±� �� ���� �����Դϴ�</span></div>

	<table class="tb" border="0" width="100%">
	<colgroup>
		<col class="cellC" />
		<col class="cellL" />
	</colgroup>
	<tr>
		<td>���� or �ؽ��±�</td>
		<td>
			<div style="margin-bottom: 5px;">
				<input type="radio" name="insgoWidget_type" value="user" checked="checked" style="border: 0px;"/> ���� @ <input type="text" class="inputText sampleColor" name="insgoWidget_user" id="insgoWidget_user" value="<?php echo $sampleInsgoWidget_user; ?>" />
				<span class="extext">�Է��� ������ ���ε�� �������� �ҷ��ɴϴ�. ��) @Godomall</span>
			</div>
			<div>
				<input type="radio" name="insgoWidget_type" value="tag" style="border: 0px;" /> �ؽ��±� # <input type="text" class="inputText sampleColor" name="insgoWidget_tag" id="insgoWidget_tag" value="<?php echo $sampleInsgoWidget_tag; ?>" />
				<span class="extext">�Է��� �ؽ��±װ� ���Ե� �������� �ҷ��ɴϴ�. ��) #����</span>
			</div>
		</td>
	</tr>
	<tr>
		<td>����Ÿ��</td>
		<td>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="center" width="100"><img src="../img/insgoWidgetType_grid.jpg" border="0"/></td>
			</tr>
			<tr>
				<td class="center"><input type="radio" name="insgoWidget_displayType" value="grid" checked="checked" style="border: 0px;" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>���̾ƿ�</td>
		<td>
			<input type="text" class="inputText2" name="insgoWidget_WidthCount" id="insgoWidget_WidthCount" maxlength="2" value="6" /> X <input type="text" class="inputText2" name="insgoWidget_HeightCount" id="insgoWidget_HeightCount" maxlength="2" value="4" />
		</td>
	</tr>
	<tr>
		<td>����� ������</td>
		<td>
			<div style="margin-bottom: 5px;">
				<input type="radio" name="insgoWidget_thumbnailSize" value="auto" checked="checked" style="border: 0px;" /> �������� �ڵ�����
				&nbsp;
				<input type="radio" name="insgoWidget_thumbnailSize" value="hand" style="border: 0px;" /> ��������
				&nbsp;
				<input type="text" class="inputText2" name="insgoWidget_thumbnailSizePx" id="insgoWidget_thumbnailSizePx" maxlength="3" /> PX
			</div>
			<div><span class="extext">�������� �ڵ��������� ���� �� ������ ���Ե� �������� �°� ����� �̹��� ����� �ڵ����� �˴ϴ�.</span></div>
		</td>
	</tr>
	<tr>
		<td>�̹��� �׵θ�</td>
		<td>
			<input type="radio" name="insgoWidget_thumbnailBorder" value="n" checked="checked" style="border: 0px;" /> ǥ�þ���
			&nbsp;
			<input type="radio" name="insgoWidget_thumbnailBorder" value="y" style="border: 0px;" /> ǥ����
		</td>
	</tr>
	<tr>
		<td>���� ����</td>
		<td>
			#<input type="text" class="inputText" name="insgoWidget_backgroundColor" id="insgoWidget_backgroundColor" maxlength="6" />
			<img src="../img/codi/btn_colortable_s.gif" border="0" alt="����ǥ ����" align="absmiddle" id="insgoWidget_palette" class="hand" />
		</td>
	</tr>
	<tr>
		<td>�̹��� ����</td>
		<td><input type="text" class="inputText2" name="insgoWidget_imageMargin" maxlength="3" value="1" /> PX</td>
	</tr>
	<tr>
		<td>���콺������ ȿ��</td>
		<td>
			<input type="radio" name="insgoWidget_overEffect" value="n" checked="checked" style="border: 0px;" /> ȿ������
			&nbsp;
			<input type="radio" name="insgoWidget_overEffect" value="blurPoint" style="border: 0px;" /> ������ ��ǰ�� �帮��
			&nbsp;
			<input type="radio" name="insgoWidget_overEffect" value="blurException" style="border: 0px;" /> ������ ������ ��ǰ �帮��
		</td>
	</tr>
	</table>

	<div class="buttonArea"><img src="../img/btn_preview_createSource.jpg" class="hand" id="insgoWidgetMakeWidget" border="0" /></div>

	</form>
</div>
<?php include '../_footer.php'; ?>
<script type="text/javascript">
var palettePopup;
var sampleInsgoWidget_user = '<?php echo $sampleInsgoWidget_user; ?>';
var sampleInsgoWidget_tag = '<?php echo $sampleInsgoWidget_tag; ?>';

function insgoWidgetValueCheck()
{
	var $ = jQuery;
	var patt = /\s/g;
	var insgoWidget_type = $('input:radio[name="insgoWidget_type"]:checked').val();
	var insgoWidget_thumbnailSize = $('input:radio[name="insgoWidget_thumbnailSize"]:checked').val();

	//���� or �ؽ��±� üũ
	if(insgoWidget_type == 'user'){
		var typeObj = $('#insgoWidget_user');
	}
	else if(insgoWidget_type == 'tag'){
		var typeObj = $('#insgoWidget_tag');
	}
	else {
		alert("���� �Ǵ� �ؽ��±׸� �Է��� �ּ���.");
		return;
	}

	if(typeObj.val() == ''){
		alert("���� �Ǵ� �ؽ��±׸� �Է��� �ּ���.");
		typeObj.trigger('click');
		typeObj.focus();
		return;
	}
	if(patt.test(typeObj.val()) === true){
		alert("����� ������ �ʽ��ϴ�.");
		typeObj.focus();
		return;
	}

	//���̾ƿ� üũ
	if($('#insgoWidget_WidthCount').val() == '' || $.isNumeric($('#insgoWidget_WidthCount').val()) == false){
		alert("���̾ƿ� ���� ������ ���ڷ� �Է��� �ּ���.");
		$('#insgoWidget_WidthCount').focus();
		return false;
	}
	if($('#insgoWidget_HeightCount').val() == '' || $.isNumeric($('#insgoWidget_HeightCount').val()) == false){
		alert("���̾ƿ� ���� ������ ���ڷ� �Է��� �ּ���.");
		$('#insgoWidget_HeightCount').focus();
		return false;
	}
	//����� ������ üũ
	if(insgoWidget_thumbnailSize == 'hand' && $('#insgoWidget_thumbnailSizePx').val() == '' &&  $.isNumeric($('#insgoWidget_thumbnailSizePx').val()) == false){
		alert("����� ����� ���ڷ� �Է��� �ּ���.");
		$('#insgoWidget_thumbnailSizePx').focus();
		return false;
	}
	return true;
}

jQuery(document).ready(function($){
	$('#insgoWidgetMakeWidget').click(function(){
		if(insgoWidgetValueCheck()){
			window.open('./popup.insgoWidgetPreview.php','insgoWidget', 'width=1050px, height=800px, scrollbars=yes');
		}
	});
	$('#insgoWidget_user, #insgoWidget_tag').click(function(){
		var thisValue = $(this).val();

		$(this).removeClass('sampleColor');
		if(thisValue == sampleInsgoWidget_user || thisValue == sampleInsgoWidget_tag){
			$(this).val('');
		}
	});
	$('#insgoWidget_palette').click(function(){
		palettePopup = popup_return('../proc/help_colortable.php?btnCallback=insertBackgroundColor', 'colorTable', 400, 400, 600, 200, 0 );
		if(palettePopup){
			palettePopup.focus();
		}
	});
});
function insertBackgroundColor(colorValue)
{
	document.getElementById("insgoWidget_backgroundColor").value = colorValue;
	palettePopup.close();
}
</script>