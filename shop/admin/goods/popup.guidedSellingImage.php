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
	<td>백그라운드 이미지</td>
	<td>
		<div class="extext">가이디드 셀링에 사용될 배경 이미지를 등록합니다.</div>
		<div class="infoMessage"><input type="file" name="unit_backgroundImage" required="required" msgR="배경 이미지를 등록하여 주세요." />&nbsp;권장 이미지 사이즈 가로 950px</div>
	</td>
</tr>
<?php } else { ?>
<tr>
	<td>PC 쇼핑몰 이미지</td>
	<td>
		<div class="extext">PC쇼핑몰 가이디드 셀링에 사용될 이미지를 등록합니다.</div>
		<div class="infoMessage"><input type="file" name="detail_pcImage" id="detail_pcImage" required="required" msgR="PC 이미지를 등록하여 주세요." />&nbsp;권장 이미지 사이즈 230px*230px</div>
	</td>
</tr>
<tr>
	<td>모바일샵 이미지<br />(선택)</td>
	<td>
		<div class="extext">모바일샵 가이디드 셀링에 사용될 이미지를 등록합니다.</div>
		<div class="infoMessage"><input type="file" name="detail_mobileImage" id="detail_mobileImage" />&nbsp;권장 이미지 사이즈 1075px*645px</div>
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
	<div>※모바일샵 이미지 미동록 시 모바일샵에서 이미지품질이 낮게 출력되오니,</div>
	<div>모바일샵을 사용하시는 경우 모바일샵 이미지도 함께 등록하는 것을 권장합니다.</div>
</div>
<?php } else { ?>
<div class="sampleImageArea">
	<div><img src="../img/guidedSelling_background_info.png" border="0" /></div>
</div>

<div class="infoMessage pdv5">
	<div>※이미지 사이즈가 클 경우 왼쪽위부터 가로사이즈 기준으로 잘려서 노출됩니다.</div>
	<div>가급적 권장 이미지 사이즈 (950px)로 이미지를 등록해 주세요.</div>
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