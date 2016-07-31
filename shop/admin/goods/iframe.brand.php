<?
include "../_header.popup.php";
if ($data = $db->fetch("select *, sno as brand from ".GD_GOODS_BRAND." where sno='$_GET[brand]'",1)) {
	list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS." where brandno = '$data[brand]'");
}
else {
	list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS." where brandno > 0");
}
@include "../../conf/brand/$data[brand].php";

$checked[tpl][$lstcfg[tpl]] = "checked";
$checked[rtpl][$lstcfg[rtpl]] = "checked";

?>

<style>
body {margin:0}
</style>
<script type="text/javascript">
function fnDeleteBrand() {

	var brand = document.form.brand.value;
	var cntGoods = <?=(int)$cntGoods?>;

	if (brand) {

		if (cntGoods > 0) {
			alert('등록된 상품이 있는 브랜드는 삭제할 수 없습니다.');
		}
		else {
			popupLayer('popup.delBrand.php?brand='+brand);
		}
	}
	else {
		alert('전체브랜드는 삭제대상이 아닙니다.');
	}

}
</script>
<form name=form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="mod_brand">
<input type=hidden name=brand value="<?=$_GET[brand]?>">

<div class="title_sub" style="margin:0">브랜드명 생성/수정/삭제<span>브랜드명을 추가하고 관리합니다</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<tr>
	<td>현재브랜드</td>
	<td>
	<?=($_GET[brand])?'TOP > ' . $data[brandnm]:"전체브랜드";?>
<? if ($_GET[brand]){ ?>
	<a href="../../goods/goods_brand.php?brand=<?=$data[brand]?>" target=_blank><img src="../img/i_nowview.gif" border=0 align=absmiddle hspace=10></a>
<? } ?>
	</td>
</tr>
<tr>
	<td>이 브랜드의 상품수</td>
	<td><b><?=number_format($cntGoods)?></b>개</td>
</tr>
<? if ($_GET[brand]){ ?>
<tr>
	<td>현재 브랜드명</td>
	<td>
	<input type=text name=brandnm class=lline required value="<?=$data[brandnm]?>">
	&nbsp; 브랜드코드 : <b><?=$data[brand]?></b>
	</td>
</tr>
<? } ?>
<? if ($_GET[brand] == ''){ ?>
<tr>
	<td>하위브랜드 생성</td>
	<td><input type=text name=sub class=lline></td>
</tr>
<? } ?>
<? if ($_GET[brand]){ ?>
<tr>
	<td>브랜드 삭제</td>
	<td><a href="javascript:void(0);" onClick="fnDeleteBrand();"><img src="../img/i_del.gif" border=0 align=absmiddle></a></td>
</tr>
<? } ?>
</table>

<? if ($_GET[brand]){ ?>
<div class="title_sub">브랜드페이지 상단부분 꾸미기<span>브랜드페이지 상단부분을 디자인합니다</span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>상단HTML</td>
	<td>
	<textarea name=lstcfg[body] style="width:100%;height:300px" type=editor><?=stripslashes($lstcfg[body])?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>
	</td>
</tr>
</table>

<div class="title_sub">브랜드페이지 리스트부분 꾸미기<span>브랜드페이지 하단의 리스트부분을 디자인합니다</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>디스플레이유형</td>
	<td>

	<table>
	<col align=center span=3>
	<tr>
		<td><img src="../img/goodalign_style_01.gif"></td>
		<td><img src="../img/goodalign_style_02.gif"></td>
		<td><img src="../img/goodalign_style_03.gif"></td>
	</tr>
	<tr class=noline>
		<td><input type=radio name=lstcfg[tpl] value="tpl_01" <?=$checked[tpl][tpl_01]?>></td>
		<td><input type=radio name=lstcfg[tpl] value="tpl_02" <?=$checked[tpl][tpl_02]?>></td>
		<td><input type=radio name=lstcfg[tpl] value="tpl_03" <?=$checked[tpl][tpl_03]?>></td>
	</tr>
	</table>

	</td>
</tr>
<!-- <tr>
	<td>이미지사이즈</td>
	<td><input type=text name=lstcfg[size] value="<?=$lstcfg[size]?>"> <font class=ver8>pixel</font></td>
</tr> -->
<tr>
	<td>상품개수</td>
	<td><input type=text name=lstcfg[page_num] value="<?=@implode(",",$lstcfg[page_num])?>" class=lline></td>
</tr>
<tr>
	<td>라인당 상품수</td>
	<td><input type=text name=lstcfg[cols] value="<?=$lstcfg[cols]?>"> 개</td>
</tr>
</table>
<? } ?>

<div class="button"><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01 >
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>상품브랜드탐색기에서 TOP (최상위)를 누르면 브랜드를 생성 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>브랜드페이지상단에 특성에 맞는 이벤트나 배너를 배치하여 차별화될 수 있게 디자인 해보세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>브랜드 순서변경은 해당브랜드를 선택후 키보드의 상하이동키↓↑로 조정하고 수정을 눌러 저장합니다.</td></tr></table>
</div>
<script>cssRound('MSG01')</script>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmbrand').style.height = document.body.scrollHeight;
}
<? if ($_GET[brand]=='' && $_GET[focus]=="sub"){ ?>
document.form.sub.focus();
<? } ?>
</script>