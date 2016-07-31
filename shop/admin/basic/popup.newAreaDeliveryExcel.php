<?php
include '../lib.php';
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<title>++ GODOMALL NEWAREA DELIVERY EXCEL ++</title>
	<script src="../common.js"></script>
	<script type="text/javascript" src="../prototype.js"></script>
	<script type="text/javascript" src="../godo.loading.indicator.js"></script>
	<link rel="styleSheet" href="../style.css">
</head>

<style type="text/css">
.newAreaInputFile		{ width: 98%; height:25px; }
.newAreaTrHeight50		{ height: 40px; }
.newAreaBgColorGray1	{ background-color:#A6A6A6; }
.newAreaBgColorGray2	{ background-color:#EAEAEA; }
.newAreaBgColorWhite	{ background-color: white; }
.newAreaAlignLeft		{ text-align: left; }
.newAreaAlignCenter		{ text-align: center; }
.newAreaPaddingTp7		{ padding-top: 7px; }
.newAreaPaddingTp30		{ padding-top: 30px; }
.newAreaPaddingLf		{ padding-left: 5px; }
#areaGuide tr			{ background-color: white; text-align: left; }
#areaGuide tr td		{ padding-left: 3px;}
.newAreaFontBold		{ font-weight: bold; }
</style>

<script type="text/javascript">
function chkForm2(f) {
	if (!chkForm(f)) return false;

	nsGodoLoadingIndicator.init({
		psObject : document.getElementById('ifrmHidden')
	});
	nsGodoLoadingIndicator.show();
	return true;
}
</script>

<body topmargin=5 margintop=5 leftmargin=10 rightmargin=10 marginwidth=10 marginheight=5>
<div class="title title_top">���� CSV ����ϱ� <span>����CSV������ �̿��Ͽ� �ϰ� ����� �� �ֽ��ϴ�.</span></div>

<form name="newAreaExcel" id="newAreaExcel" method="post" action="./popup.newAreaDeliveryIndb.php" enctype="multipart/form-data" onsubmit="return chkForm2(this);" target="ifrmHidden">
<input type="hidden" name="type" value="excel">
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="newAreaBgColorGray1" summary="�������� ���">
<colgroup>
	<col width="120px" />
	<col width="*" />
</colgroup>
<tr class="newAreaTrHeight50">
	<td class="newAreaBgColorGray2 newAreaAlignCenter newAreaFontBold">����CSV���� ���ε�</td>
	<td class="newAreaBgColorWhite newAreaPaddingLf"><input type="file" name="newAreaCsvFile" class="newAreaInputFile" required fld_esssential /></td>
</tr>
</table>


<table cellpadding="0" cellspacing="0" width="100%" border="0" class="newAreaPaddingTp7">
<tr>
	<td class="newAreaAlignLeft"><a href="../data/csv_newAreaDelivery.csv"><img src="../img/btn_popup_exceldown.gif" border="0" /></a></td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- ��������ġ��� �ٿ�ε��Ͽ� ���� ������ Ȯ�� �� ��Ŀ� �°� �ۼ��Ͽ� ����� �ּ���. (���� ���ּҸ��� ������ ���ݾס��� �����˴ϴ�.)</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- ���θ�/���� �ּ� ���� ���� <strong>1,000</strong>�� ���� �����մϴ�</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- �ּ��� ���õ����� �κ��� �������� ���� ���õ������� ����Ͽ� �ּ���.</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t">
		<table cellpadding="0" cellspacing="1" border="0" class="newAreaBgColorGray1" width="590" id="areaGuide">
		<colgroup>
			<col width="95px" />
			<col width="95px" />
			<col width="95px" />
			<col width="100px" />
			<col width="115px" />
			<col width="90px" />
		</colgroup>
		<tr>
			<td class="extext_t">����&nbsp;��&nbsp;������</td>
			<td class="extext_t">���&nbsp;��&nbsp;��⵵</td>
			<td class="extext_t">�泲&nbsp;��&nbsp;��󳲵�</td>
			<td class="extext_t">���&nbsp;��&nbsp;���ϵ�</td>
			<td class="extext_t">����&nbsp;��&nbsp;���ֱ�����</td>
			<td class="extext_t">�泲&nbsp;��&nbsp;��û����</td>
		</tr>
		<tr>
			<td class="extext_t">�뱸&nbsp;��&nbsp;�뱸������</td>
			<td class="extext_t">����&nbsp;��&nbsp;����������</td>
			<td class="extext_t">�λ�&nbsp;��&nbsp;�λ걤����</td>
			<td class="extext_t">����&nbsp;��&nbsp;����Ư����</td>
			<td class="extext_t">����&nbsp;��&nbsp;����Ư����ġ��</td>
			<td class="extext_t">���&nbsp;��&nbsp;��û�ϵ�</td>
		</tr>
		<tr>
			<td class="extext_t">���&nbsp;��&nbsp;��걤����</td>
			<td class="extext_t">��õ&nbsp;��&nbsp;��õ������</td>
			<td class="extext_t">����&nbsp;��&nbsp;���󳲵�</td>
			<td class="extext_t">����&nbsp;��&nbsp;����ϵ�</td>
			<td class="extext_t">����&nbsp;��&nbsp;����Ư����ġ��</td>
			<td class="extext_t"></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- ���õ���,�������� �� �ּҴ� ����������/���θ��� ���⸦ �Ͽ��ּ���. <br />ex) ����Ư���� ������ ������� 77��<br /><strong>����Ư����</strong> (����) <strong>������</strong> (����) <strong>�������</strong> (����) <strong>77��</strong></td>
</tr>
<tr>
	<td class="newAreaAlignCenter newAreaPaddingTp30"><input type="image" src="../img/btn_register.gif" border="0" style="border: 0px;" /></td>
</tr>
</table>
</form>

<iframe name="ifrmHidden" id="ifrmHidden" src="../../blank.txt" style="display:none;width:100%;height:500px;"></iframe>
</body>
</html>