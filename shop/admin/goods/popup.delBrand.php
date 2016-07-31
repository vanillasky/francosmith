<?

include "../_header.popup.php";
$data = $db->fetch("select *, sno as brand from ".GD_GOODS_BRAND." where sno='$_GET[brand]'",1);
list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS." where brandno = '$_GET[brand]'");

?>

<script>
function chkForm2(obj){
	return chkForm(obj);
	parent.saveHistory(parent.form);
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="del_brand">
<input type=hidden name=brand value="<?=$_GET[brand]?>">

<div class="title title_top">브랜드 삭제</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>삭제 브랜드</td>
	<td>TOP > <?=$data[brandnm]?></td>
</tr>
<tr>
	<td>연결상품수</td>
	<td><b><?=$cntGoods?></b>개</td>
</tr>
<tr>
	<td>주의사항</td>
	<td class=small1 style="color:#5B5B5B;padding:5px;">
		상단HTML에 쓰인 이미지는 다른 곳에서도 사용하고 있을 수 있으므로 자동 삭제되지 않습니다.<br>
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