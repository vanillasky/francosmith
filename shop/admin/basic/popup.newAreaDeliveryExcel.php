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
<div class="title title_top">엑셀 CSV 등록하기 <span>엑셀CSV파일을 이용하여 일괄 등록할 수 있습니다.</span></div>

<form name="newAreaExcel" id="newAreaExcel" method="post" action="./popup.newAreaDeliveryIndb.php" enctype="multipart/form-data" onsubmit="return chkForm2(this);" target="ifrmHidden">
<input type="hidden" name="type" value="excel">
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="newAreaBgColorGray1" summary="엑셀파일 등록">
<colgroup>
	<col width="120px" />
	<col width="*" />
</colgroup>
<tr class="newAreaTrHeight50">
	<td class="newAreaBgColorGray2 newAreaAlignCenter newAreaFontBold">엑셀CSV파일 업로드</td>
	<td class="newAreaBgColorWhite newAreaPaddingLf"><input type="file" name="newAreaCsvFile" class="newAreaInputFile" required fld_esssential /></td>
</tr>
</table>


<table cellpadding="0" cellspacing="0" width="100%" border="0" class="newAreaPaddingTp7">
<tr>
	<td class="newAreaAlignLeft"><a href="../data/csv_newAreaDelivery.csv"><img src="../img/btn_popup_exceldown.gif" border="0" /></a></td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- ‘엑셀양식’을 다운로드하여 샘플 내용을 확인 후 양식에 맞게 작성하여 등록해 주세요. (동일 ’주소명’이 있을시 ’금액’만 수정됩니다.)</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- 도로명/지번 주소 구분 없이 <strong>1,000</strong>개 까지 가능합니다</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- 주소의 ’시도‘명 부분은 생략하지 않은 ’시도‘명을 사용하여 주세요.</td>
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
			<td class="extext_t">강원&nbsp;▷&nbsp;강원도</td>
			<td class="extext_t">경기&nbsp;▷&nbsp;경기도</td>
			<td class="extext_t">경남&nbsp;▷&nbsp;경상남도</td>
			<td class="extext_t">경북&nbsp;▷&nbsp;경상북도</td>
			<td class="extext_t">광주&nbsp;▷&nbsp;광주광역시</td>
			<td class="extext_t">충남&nbsp;▷&nbsp;충청남도</td>
		</tr>
		<tr>
			<td class="extext_t">대구&nbsp;▷&nbsp;대구광역시</td>
			<td class="extext_t">대전&nbsp;▷&nbsp;대전광역시</td>
			<td class="extext_t">부산&nbsp;▷&nbsp;부산광역시</td>
			<td class="extext_t">서울&nbsp;▷&nbsp;서울특별시</td>
			<td class="extext_t">세종&nbsp;▷&nbsp;세종특별자치시</td>
			<td class="extext_t">충북&nbsp;▷&nbsp;충청북도</td>
		</tr>
		<tr>
			<td class="extext_t">울산&nbsp;▷&nbsp;울산광역시</td>
			<td class="extext_t">인천&nbsp;▷&nbsp;인천광역시</td>
			<td class="extext_t">전남&nbsp;▷&nbsp;전라남도</td>
			<td class="extext_t">전북&nbsp;▷&nbsp;전라북도</td>
			<td class="extext_t">제주&nbsp;▷&nbsp;제주특별자치도</td>
			<td class="extext_t"></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="newAreaAlignLeft extext_t newAreaPaddingTp7">- ’시도’,’구군‘ 등 주소는 행정구역별/도로명별로 띄어쓰기를 하여주세요. <br />ex) 서울특별시 강남구 테헤란로 77길<br /><strong>서울특별시</strong> (띄어쓰기) <strong>강남구</strong> (띄어쓰기) <strong>테헤란로</strong> (띄어쓰기) <strong>77길</strong></td>
</tr>
<tr>
	<td class="newAreaAlignCenter newAreaPaddingTp30"><input type="image" src="../img/btn_register.gif" border="0" style="border: 0px;" /></td>
</tr>
</table>
</form>

<iframe name="ifrmHidden" id="ifrmHidden" src="../../blank.txt" style="display:none;width:100%;height:500px;"></iframe>
</body>
</html>