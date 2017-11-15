<?php
$location = "��ǰ���� > �ؽ��±� ���̵� ���� �����";
include '../_header.php';

$guidedSelling = Core::loader('guidedSelling');
if(!$_GET['mode']) $_GET['mode'] = 'write';
if($_GET['mode'] === 'modify'){
	$guidedSellingData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
}
if(!$guidedSellingData['guided_backgroundColor']) $guidedSellingData['guided_backgroundColor'] = 'ffffff';
?>
<link href="./css/adm_goods_guidedSelling.css?v=20161124" rel="stylesheet" type="text/css"/>
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/guidedSelling/guidedSellingControl.js?actTime=<?php echo time(); ?>"></script>

<div class="guidedSelling-layout">
<form name="guidedSelling_form" id="guidedSelling_form" target="ifrmHidden" enctype="multipart/form-data" action="./adm_goods_hashtag_guidedSelling_indb.php" method="post">
<input type="hidden" name="mode" id="guidedSellingMode" value="<?php echo $_GET['mode']; ?>" />
<input type="hidden" name="guided_no" id="guided_no" value="<?php echo $_GET['guided_no']; ?>" />

	<div class="title title_top">
		�ؽ��±� ���̵� ���� �����
		<span>�ؽ��±׸� �̿��� ���̵� ���� ����� ������ּ���.</span>
		<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=55')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a>
	</div>

	<!-- ��� ������ -->
	<div class="guidedSelling-top">
		<table class="tb">
		<colgroup>
			<col class="cellC" />
			<col class="cellL" />
		</colgroup>
		<tbody>
		<tr>
			<td>���̵� ���� �̸�</td>
			<td>
				<input type="text" name="guided_subject" id="guided_subject" class="line" required="required" value="<?php echo $guidedSellingData['guided_subject']; ?>" />
				&nbsp;
				<span class="extext">������ �� �ִ� �̸��� �Է��� �ּ���.</span>
			</td>
		</tr>
		<tr>
			<td>��� ����</td>
			<td>
				��� �������� ����
				#<input type="text" name="guided_backgroundColor" id="guided_backgroundColor" value="<?php echo $guidedSellingData['guided_backgroundColor']; ?>" class="line" maxlength="6" />
				&nbsp;
				<img src="../img/codi/btn_colortable_s.gif" border="0" alt="����ǥ ����" align="absmiddle" id="guidedSelling_palette" class="hand" />
				&nbsp;
				<img src="../img/btn_preview_backgroundColor.png" border="0" id="guided_preview_backgroundColor" alt="���� �̸�����" align="absmiddle" class="hand" />
			</td>
		</tr>
		</tbody>
		</table>
	</div>
	<!-- ��� ������ -->

	<!-- ������ -->
	<div class="guidedSelling-contents" id="guidedSelling-contents"></div>
	<!-- ������ -->

	<!-- �ϴ� ���� -->
	<div class="guidedSelling-addQuestion">
		<img src="../img/btn_guidedSelling_question_add.png" border="0" id="guidedSelling_addQuestion" class="hand" alt="���̵� ���� ������ �亯 �߰�" />
	</div>
	<!-- �ϴ� ���� -->

	<!-- �ϴ� ���� -->
	<div class="guidedSelling-bottom">
		<img src="../img/btn_save.gif" border="0" class="hand" id="guidedSelling_save" />
		&nbsp;
		<a href="./adm_goods_hashtag_guidedSelling_list.php"><img src="../img/btn_cancel.gif" border="0" /></a>
	</div>
	<!-- �ϴ� ���� -->

</form>
</div>

<script type="text/javascript">
var palettePopup;
function openPallete()
{
	palettePopup = popup_return('../proc/help_colortable.php?btnCallback=adjustPallete', 'colorTable', 400, 400, 600, 200, 0 );
	if(palettePopup){
		palettePopup.focus();
	}
}
function adjustPallete(colorValue)
{
	jQuery("#guided_backgroundColor").val(colorValue);
	if(palettePopup){
		palettePopup.close();
	}
}
function adjustLiveImage(mode, uniqueKey, index, imagePath)
{
	if(mode === 'saveTempBackgroundImage'){
		var targetObj = jQuery("input[name='answer_no["+uniqueKey+"][]']").closest(".guidedSelling-itemArea");
		if(targetObj.length > 0){
			targetObj.css("background-image", "url('"+imagePath+"?v="+jQuery.now()+"')");
		}
	}
	else if(mode === 'saveTempImage'){
		var guidedSellingItemSelector = jQuery("input[name='answer_no["+uniqueKey+"][]']").closest(".guidedSellingItemSelector").eq(index);
		var targetObj = guidedSellingItemSelector.find(".guidedSelling_imageArea");

		//�̹��� ���ε� ���θ� üũ�ϱ� ���� value ��
		guidedSellingItemSelector.find("input[name='existCheckImageInput[]']").val('y');

		if(targetObj.length > 0){
			targetObj.css("background-image", "url('"+imagePath+"?v="+jQuery.now()+"')");
		}
	}
	else { }
}
function endProgress()
{
	if(jQuery("#guidedSellingPrograssbar").length > 0){
		jQuery("#guidedSellingPrograssbar").remove();
	}
}
jQuery(document).ready(GuidedSellingController);
</script>
<?php include '../_footer.php'; ?>