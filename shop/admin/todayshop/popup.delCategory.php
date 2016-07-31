<?

include "../_header.popup.php";
list($cntGoods) = $db->fetch("select count(distinct tgsno) from ".GD_TODAYSHOP_LINK." where category like '$_GET[category]%'");

?>

<script>
function chkForm2(obj){
	return chkForm(obj);
	parent.saveHistory(parent.form);
}
</script>

<form name=form method=post action="indb.category.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="del_category">
<input type=hidden name=category value="<?=$_GET[category]?>">

<div class="title title_top">카테고리 삭제<span>하위카테고리도 자동 삭제됩니다</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>삭제 카테고리</td>
	<td><?=currPositionTS($_GET[category],1)?></td>
</tr>
<tr>
	<td>연결상품수</td>
	<td><b><?=$cntGoods?></b>개</td>
</tr>
<tr>
	<td>주의사항</td>
	<td class=small1 style="color:#5B5B5B;padding:5px;">
		상단꾸미기에 쓰인 이미지는 다른 곳에서도 사용하고 있을 수 있으므로 자동 삭제되지 않습니다.<br>
		'디자인관리 > webFTP이미지관리 > data > editor'에서 이미지체크 후 삭제관리하세요.
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>