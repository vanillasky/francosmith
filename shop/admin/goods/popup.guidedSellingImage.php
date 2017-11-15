<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';
?>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/guidedSelling/guidedSellingControl.js?actTime=<?php echo time(); ?>"></script>
<style>
.infoMessage { color: red; font: 11px dotum; }
.sampleImageArea { height: 348px; width: 100%; margin-top: 10px; }
.saveBtnArea { text-align: center; margin-top: 20px; }
</style>
<form name="popupGuidedSellingImageForm" id="popupGuidedSellingImageForm" method="post" target="ifrmHidden" action="./adm_goods_hashtag_guidedSelling_indb.php" enctype="multipart/form-data" onsubmit="return checkGuidedSellingForm(this)" />
<input type="hidden" name="mode" value="<?php echo $_GET['mode']; ?>" />
<input type="hidden" name="uniqueKey" value="<?php echo $_GET['uniqueKey']; ?>" />
<input type="hidden" name="index" value="<?php echo $_GET['index']; ?>" />
<table class="tb" style="width: 100%;">
<colgroup>
	<col class="cellC" />
	<col class="cellL" />
</colgroup>
<tbody>
<?php if($_GET['mode'] === 'saveTempBackgroundImage'){ ?>
<tr>
	<td>��׶��� �̹���</td>
	<td>
		<div class="extext">���̵�� ������ ���� ��� �̹����� ����մϴ�.</div>
		<div class="infoMessage"><input type="file" name="unit_backgroundImage" required="required" msgR="��� �̹����� ����Ͽ� �ּ���." />&nbsp;���� �̹��� ������ ���� 950px</div>
	</td>
</tr>
<?php } else { ?>
<tr>
	<td>PC ���θ� �̹���</td>
	<td>
		<div class="extext">PC���θ� ���̵�� ������ ���� �̹����� ����մϴ�.</div>
		<div class="infoMessage"><input type="file" name="detail_pcImage" id="detail_pcImage" required="required" msgR="PC �̹����� ����Ͽ� �ּ���." />&nbsp;���� �̹��� ������ 230px*230px</div>
	</td>
</tr>
<tr>
	<td>����ϼ� �̹���<br />(����)</td>
	<td>
		<div class="extext">����ϼ� ���̵�� ������ ���� �̹����� ����մϴ�.</div>
		<div class="infoMessage"><input type="file" name="detail_mobileImage" id="detail_mobileImage" />&nbsp;���� �̹��� ������ 1075px*645px</div>
	</td>
</tr>
<?php } ?>
</tbody>
</table>

<?php if($_GET['mode'] === 'saveTempImage'){ ?>
<div class="sampleImageArea">
	<div><img src="../img/guidedSelling_imageSample.png" border="0" /></div>
</div>

<div class="infoMessage pdv5">
	<div>�ظ���ϼ� �̹��� �̵��� �� ����ϼ����� �̹���ǰ���� ���� ��µǿ���,</div>
	<div>����ϼ��� ����Ͻô� ��� ����ϼ� �̹����� �Բ� ����ϴ� ���� �����մϴ�.</div>
</div>
<?php } else { ?>
<div class="sampleImageArea">
	<div><img src="../img/guidedSelling_background_info.png" border="0" /></div>
</div>

<div class="infoMessage pdv5">
	<div>���̹��� ����� Ŭ ��� ���������� ���λ����� �������� �߷��� ����˴ϴ�.</div>
	<div>������ ���� �̹��� ������ (950px)�� �̹����� ����� �ּ���.</div>
</div>
<?php } ?>

<div class="saveBtnArea">
	<input type="image" src="../img/btn_save.gif" border="0" class="hand" style="border: none;" />
</div>
</form>

<script type="text/javascript">
jQuery(document).ready(function(){
	GuidedSellingCoreController = new GuidedSellingCoreController();
});

function checkGuidedSellingForm(f){
	GuidedSellingCoreController.showProgressBar();

	if(chkForm(f) === true){
		return true;
	}
	else {
		GuidedSellingCoreController.hiddenProgressBar();

		return false;
	}
}
</script>
<?php
include '../_footer.popup.php';
?>