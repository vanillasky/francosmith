<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';
@include '../../conf/config.mobileShop.php';
$guidedSelling = Core::loader('guidedSelling');
?>
<style type="text/css">
.guidedSellingWidget-Area { width: 100%; height: 100%; }
.guidedSellingWidget-Area .guidedSellingWidget-configLayout { width: 100%; margin-bottom: 40px; }
.guidedSellingWidget-Area .guidedSellingWidget-configLayout table { width: 100%; }
.guidedSellingWidget-Area .guidedSellingWidget-configLayout .guidedSellingWidget-creteWidgetArea { width: 100%; text-align: center; margin-top: 20px; }

.guidedSellingWidget-Area .guidedSellingWidget-previewArea {
	border: 1px #ACACAC solid;
	margin: 10px;
	min-height: 200px;
	margin-bottom: 40px;
}
.guidedSellingWidget-Area .guidedSellingWidget-sourceCodeArea {
	border: 1px #ACACAC solid;
	word-break: break-all;
	padding: 10px;
	margin: 10px;
	font-size: 13px;
	background-color: #F6F6F6;
	min-height: 100px;
}
</style>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/guidedSelling/guidedSellingControl.js?actTime=<?php echo time(); ?>"></script>

<input type="hidden" name="shopRootDir" id="shopRootDir" value="<?php echo $cfg['rootDir']; ?>" />
<input type="hidden" name="mobileRootDir" id="mobileRootDir" value="<?php echo $cfgMobileShop['mobileShopRootDir']; ?>" />
<input type="hidden" name="guided_no" id="guided_no" value="<?php echo $_GET['guided_no']; ?>" />

<div class="guidedSellingWidget-Area">
	<!-- ���� ���� -->
	<div class="title title_top">�ؽ��±� ���̵� ���� ���� ���� <span>������ ���Ե� �������� �µ��� ����� ���� �� ������ �������ּ���.</span></div>

	<div class="guidedSellingWidget-configLayout">
		<table class="tb">
		<colgroup>
			<col class="cellC" />
			<col class="cellL" />
		</colgroup>
		<tbody>
		<tr>
			<td>���� Ÿ��</td>
			<td class="noline">
				<input type="radio" name="widgetType" value="pc" checked="checked" /> PC
				&nbsp;
				<input type="radio" name="widgetType" value="mobile" /> MOBILE
			</td>
		</tr>
		<tr id="guidedSelling_widgetSizeArea">
			<td>���� ������ ����</td>
			<td>
				<input type="text" name="widgetSize" id="widgetSize" class="line" value="1000" /> px
			</td>
		</tr>
		</tbody>
		</table>

		<div class="guidedSellingWidget-creteWidgetArea"><img src="../img/btn_create_widget.png" border="0" class="hand" id="guidedSelling_createWidget" /> </div>
	</div>
	<!-- ���� ���� -->

	<!-- �̸����� -->
	<div class="title title_top">�ؽ��±� ���̵� ���� ���� �̸�����</div>

	<div class="guidedSellingWidget-previewArea" id="guidedSellingWidget-previewArea"></div>
	<!-- �̸����� -->

	<!-- �ҽ��ڵ� -->
	<div class="title title_top">�ؽ��±� ���̵� ���� �ҽ��ڵ� <span>�ϴ��� �ҽ��� �����Ͽ� ���ϴ� �������� �������ּ���.</span></div>

	<div class="guidedSellingWidget-sourceCodeArea" id="guidedSellingWidget-sourceCodeArea"><?php echo $guidedSelling_iframeCode; ?></div>
	<!-- �ҽ��ڵ� -->

	<div class="center pdv10">
		<img src="../img/btn_copySource.jpg" class="hand" id="guidedSelling_sourceCopy" border="0" />
		<img src="../img/btn_closePopup.jpg" class="hand" id="guidedSelling_popupClose" border="0" />
	</div>
</div>



<script type="text/javascript">
jQuery(document).ready(GuidedSellingWidgetPopup);
</script>

<?php include '../_footer.popup.php'; ?>
