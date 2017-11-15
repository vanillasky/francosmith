<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';

$hashtag = Core::loader('hashtag');
$hashtagIframeWidgetCode = $hashtag->getIframeWidgetCode($_GET);
$hashtagIframeWidgetCodeHtml = str_replace(">", "&gt;", str_replace("<", "&lt;", $hashtagIframeWidgetCode));
?>
<style type="text/css">
.hashtag_previewLayout { width: 1000px; min-height: 400px; }
.hashtag_previewSourceArea { width: 1000px; min-height: 200px; border: 1px #ACACAC solid; word-break: break-all; padding: 10px; font-size: 13px; background-color: #F6F6F6; }
</style>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>

<div class="title title_top">�ؽ��±� ��ǰ����Ʈ �̸����� <span>������ ��ǰ����Ʈ�� �Ʒ��� ���� �������� ���Ե˴ϴ�.</span></div>
<div class="hashtag_previewLayout" id="hashtag_previewLayout"><?php echo $hashtagIframeWidgetCode; ?></div>


<div class="title title_top">�ؽ��±� ��ǰ����Ʈ �ҽ��ڵ� <span>�ϴ��� �ҽ��� �����Ͽ� ���θ��� �������ּ���.</span></div>
<div class="hashtag_previewSourceArea" id="hashtag_previewSourceArea"><?php echo $hashtagIframeWidgetCodeHtml; ?></div>

<div class="center pdv10">
	<img src="../img/btn_copySource.jpg" class="hand" id="sourceCopy" border="0" />
	<img src="../img/btn_closePopup.jpg" class="hand" id="popupClose" border="0" />
</div>

<script type="text/javascript">
jQuery(document).ready(HashtagPopupCreateCodeController);
</script>

<?php include '../_footer.popup.php'; ?>