<?php
include '../lib.php';
@include "../../conf/area.delivery.php";
@include "../../conf/config.pay.php";

$_totalArea = @array_filter(@explode(",", $r_area[deliveryArea]));
$totalArea = '(' . count($_totalArea) . '��)';

$_totalZipcode = @array_filter(@explode("|", $set['delivery']['areaZip1']));
$totalZipcode = '(' . count($_totalZipcode) . '��)';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<title>++ GODOMALL NEWAREA DELIVERY ++</title>
	<link rel="styleSheet" href="../style.css">
	<script type="text/javascript" src="../prototype.js"></script>
	<script type="text/javascript" src="../godo.loading.indicator.js"></script>
</head>

<style type="text/css">
html, body				{ height: 100%; margin:0px; overflow: hidden; }
.newAreaButton			{ background-image:url("../img/btn_newArea_bg.gif"); height: 65px; width: 205px; text-align: center; display: inline-block; line-height: 65px; font-size:14px; color: #ffffff; font-weight: bold; font-family: Dotum; float: left; margin: 30px 0px 0px 0px;}
.newAreaButtonMargin1	{ margin-left: 40px;}
.newAreaButtonMargin2	{ margin-left: 5px; }
.newAreaPaddingLt10		{ padding-left: 10px; }
.newAreaFontSize11		{ font-size: 11px; }
.newAreaAlignCenter		{ text-align: center; }
.newAreaPaddingTp		{ padding-top: 20px; }
.newAreaPaddingTp5		{ padding-top: 5px; }
.newAreaMarginLt		{ margin-left: 7px; }
.newAreaCursorPointer	{ cursor: pointer; }
.newAreaMigrationNum	{ font-family: Dotum; font-weight: bold; color: #00beff; font-size: 14px; }
.newAreaGuideMsg		{ font-family: Dotum; color: #444444; font-size: 11px; }
</style>

<script type="text/javascript">
function formSubmit(migrationType)
{
	if(confirm("�����͸� ��ȯ�Ͻðڽ��ϱ�?")){
		var f = document.migrationForm;

		f.migrationType.value = migrationType;
		f.type.value = 'migration';
		f.submit();

		nsGodoLoadingIndicator.init({
			psObject : document.getElementById('ifrmHidden')
		});
		nsGodoLoadingIndicator.show();
		return true;
	}
}
</script>

<body topmargin=5 margintop=5 leftmargin=10 rightmargin=10 marginwidth=10 marginheight=5>
<div class="title title_top newAreaMarginLt">������ ��ȯ (����Ʈ)</div>
<div class="newAreaPaddingLt10 newAreaFontSize11">- �Ʒ� ���� ������� ��ϵ� ������ �߰� ��ۺ� �����Ͱ� �ֽ��ϴ�.</div>
<div class="newAreaPaddingLt10 newAreaFontSize11">&nbsp;&nbsp;��ȯ�ϰ��� �ϴ� ���� �����͸� Ŭ���Ͽ� <strong>���ο� ��������/���θ� ������ �����ͷ� ��ȯ</strong>�Ͻñ�</div>
<div class="newAreaPaddingLt10 newAreaFontSize11">&nbsp;&nbsp;�ٶ��ϴ�</div>
<div class="newAreaPaddingLt10 newAreaPaddingTp5 newAreaFontSize11">- ���θ�/���� �ּ� ���� ���� <strong>1,000</strong>������ ��ϰ����մϴ�.</div>

<form name="migrationForm" id="migrationForm" method="POST" action="popup.newAreaDeliveryIndb.php" target="ifrmHidden">
<input type="hidden" name="type" value="" />
<input type="hidden" name="migrationType" value="" />
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="newAreaDeliveryTable">
<tr>
	<td class="newAreaAlignCenter">
		<div class="newAreaButton newAreaCursorPointer newAreaButtonMargin1" onclick="javascript:formSubmit('area');">������<span class="newAreaMigrationNum"><?php echo $totalArea; ?></span></div>
		<div class="newAreaButton newAreaCursorPointer newAreaButtonMargin2" onclick="javascript:formSubmit('zipcode');">�����ȣ<span class="newAreaMigrationNum"><?php echo $totalZipcode; ?></span></div>
	</td>
</tr>
<tr>
	<td class="newAreaAlignCenter newAreaPaddingTp"><img src="../img/btn_pass.gif" class="newAreaCursorPointer newAreaBorder0" onclick="javascript:parent.addNewAreaDelivery('normal');" /></td>
</tr>
</table>

</form>

<iframe name="ifrmHidden" id="ifrmHidden" src="../../blank.txt" style="display:none;width:100%;height:500px;"></iframe>
</body>
</html>