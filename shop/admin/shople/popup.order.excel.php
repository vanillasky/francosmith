<?
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$mode = $_GET['m'];

$shople = Core::loader('shople');
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<!-- * -->
<script type="text/javascript">
function _fnDownload() {
	opener.nsShople.order.download();
}

</script>

<form name="frmClaim" id="frmClaim" method="post" enctype="multipart/form-data" action="./ax.indb.order.php">
<input type="hidden" name="mode" value="excel">

<div class="title title_top" style="margin-top:10px;">송장번호 일괄등록<span>&nbsp;</span></div>
<p class="gd_notice">
<span>엑셀리스트를 다운받아 택배사코드와 송장번호를 입력하신 후 다시 업로드하시면 일괄등록처리가 됩니다.</span>
<span>부분발송처리 주문건은 개별발송처리 바랍니다. (다운받은 엑셀파일에서도 부분발송 주문건은 데이터 제외)</span>
<span>파일 업로드 후 반드시 적용버튼을 통해 일괄등록을 완료를 해 주십시오.</span>
<span>날짜 입력, 송장번호는 하이픈(-) 없이 입력해 주십시오. (예: 20071215)</span>
<span>한번에 최대 1000개까지 등록이 가능합니다.</span>
<span>옵션값, 옵션가격은 콤마(,)로 구분하여 입력해 주십시오. (옵션가격은 숫자로만 입력해 주십시오.)</span>
<span>옵션별 리스트엑셀로 송장등록 하실 경우 묶음배송단위(배송번호 단위)로 처리됩니다.</span>
<span>예) 배송번호 12345에 두개의 상품이 배송처리될 경우 1개의 주문처리됩니다.</span>
<span class="red">주의!! 위와 같은 경우 처리결과에 1개 정상처리,1개 미처리항목이라고 메시지가 나오나 정상처리된것입니다. 유의 하시기 바랍니다</span>
</p>


<div class="title title_top" style="margin-top:10px;">엑셀파일 다운로드<span>&nbsp;</span></div>
	<p class="gd_notice">
	<a href="javascript:_fnDownload();"><img src="../img/btn_excel_download.gif" alt="엑셀다운로드"></a> 엑셀파일 다운로드 후 택배사, 송장번호를 입력하십시오.
	</p>


<div class="title title_top" style="margin-top:10px;">택배사 코드<span>(판매자님이 사용하시는 택배사 코드입니다. 택배사 이름대신 코드를 입력해주십시오.)</span></div>
	<table class="tb" width="100%">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">
	<tr>
		<th>동부익스프레스</th>
		<td>00001</td>
		<th>로젠택배</th>
		<td>00002</td>
		<th>옐로우캡</th>
		<td>00006</td>
	</tr>
	<tr>
		<th>우체국택배</th>
		<td>00007</td>
		<th>우편등기</th>
		<td>00008</td>
		<th>한진택배</th>
		<td>00011</td>
	</tr>
	<tr>
		<th>현대택배</th>
		<td>00012</td>
		<th>CJ-GLS</th>
		<td>00013</td>
		<th>KGB택배</th>
		<td>00014</td>
	</tr>
	<tr>
		<th>대한통운</th>
		<td>00017</td>
		<th>이노지스택배</th>
		<td>00019</td>
		<th>대신택배</th>
		<td>00021</td>
	</tr>
	<tr>
		<th>일양로지스</th>
		<td>00022</td>
		<th>ACI</th>
		<td>00023</td>
		<th>WIZWA</th>
		<td>00025</td>
	</tr>
	<tr>
		<th>경동택배</th>
		<td>00026</td>
		<th>천일택배</th>
		<td>00027</td>
		<th>KGL</th>
		<td>00028</td>
	</tr>
	<tr>
		<th>기타</th>
		<td>00099</td>
	</table>

<div class="title title_top" style="margin-top:10px;">엑셀파일 업로드<span>&nbsp;</span></div>
	<p class="gd_notice">
	<input type="file" name="excel" value=""> * 편집한 엑셀 파일(CSV)을 업로드 합니다.

	</p>

<div class="button">
	<input type="image" src="../img/btn_save.gif">
	<img src="../img/btn_cancel.gif" class="hand" onClick="self.close();">
</div>

	</form>

<!-- eof * -->
<script type="text/javascript">
linecss();
table_design_load();
</script>
</body>
</html>
