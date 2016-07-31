<?

include "../_header.popup.php";

$idx = $_GET[idx];
?>
<div class="title title_top">배송정책등록</div>
<form name="frm" style="margin:0px;">
<input type="hidden" name="idx" value="<?=$idx?>">
<table class=tb>
<col class=cellC width=20%><col class=cellL width=80%>
<tr>
	<td>배송정책명</td>
	<td>
	<input type=text name="deliveryTitle">
	</td>
</tr>
<tr>
	<td>정책</td>
	<td>
	총 구매액이 <input type=text name="price">원 이상일 때 배송비 무료, 미만일 때 
	<select name="methodtype">
	<option value="1">선불</option>
	<option value="2">착불</option>
	<select> <input type=text name="delivery">원 배송비 부과
	</td>
</tr>
</table>
<div style='font:0;padding-top:5'></div>
<div align="center"><input type="image" src="../img/btn_register.gif" class="null">
<a href="javascript:parent.closeLayer();"><img src="../img/btn_cancel.gif"></a></div>
</form>