<?

header("location: adm_goods_sort.php");

$location = "상품진열 > 분류페이지 상품진열";
include "../_header.php";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

if (!$_GET[page_num]) $_GET[page_num] = 50;
$selected[page_num][$_GET[page_num]] = "selected";

$where[] = "open";
$where[] = sprintf("category like '%s%%'", $category);

if ($where) $where = "where ".implode(" and ",$where);

if ($category){

	list($limited) = $db->fetch("select count(distinct a.goodsno) from ".GD_GOODS_LINK." a,".GD_GOODS." b $where and a.goodsno=b.goodsno");
	if ($limited > $_GET[page_num]) $limited = $_GET[page_num];

	$query = "
	select
		a.goodsno,a.sort,a.sno,b.goodsnm,b.img_s,b.icon,c.price
	from
		".GD_GOODS_LINK." a
		left join ".GD_GOODS." b on a.goodsno=b.goodsno
		left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link
	$where
	group by a.goodsno
	order by sort
	limit $limited
	";

	$res = $db->query($query);

}

?>

<script>

var iciRow, preRow;

function spoit(obj)
{
	iciRow = obj;
	iciHighlight();
}

function iciHighlight()
{
	if (preRow) preRow.style.backgroundColor = "";
	iciRow.style.backgroundColor = "#FFF4E6";
	preRow = iciRow;
}

function moveTree(idx)
{
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	objTop.moveRow(iciRow.rowIndex,nextPos);
}

function keydnTree()
{
	if (iciRow==null) return;
	switch (event.keyCode){
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}

document.onkeydown = keydnTree;

</script>

<div class="title title_top">분류페이지 상품진열 <span>각 분류페이지의 상품진열순서를 정하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>','multiple');</script></td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table cellpadding=0 cellspacing=0 class=small_tip bgcolor=F7F7F7 width=100%>
<tr><td height=10></td></tr>
<tr><td style="padding-left:20px"><img src="../img/arrow_downorg.gif" align=absmiddle> 상품진열 순서변경 도움말</font></td></tr>
<tr><td style="padding-left:20px"><img src="../img/sa_cate_change.gif" style="border:2px solid #D4D3D3;"></td></tr>
<tr><td height=10></td></tr>
</table><div style="padding-top:15px"></div>

<div align=right style="margin-bottom:3px;">
<img src="../img/sname_output.gif" align=absmiddle>
<select name=page_num onchange="this.form.submit()">
<?
$r_pagenum = array(50,100,200,300);
foreach ($r_pagenum as $v){
?>
<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
<? } ?>
</select>
</div>
</form>

<form method=post action="indb.php">
<input type=hidden name=mode value="sortGoods">
<table width=100% border=1 bordercolor=#dfdfdf style="border-collapse:collapse" frame=hsides rules=rows>
<? if (is_resource($res)){ while ($data=$db->fetch($res)){ ?>
<tr onclick="spoit(this)">
	<td align=center bgcolor=#f7f7f7 width=40 nowrap><font class=small1 color=444444><?=++$idx?></font></td>
	<td width=100% style="padding-left:5px">
	<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],25,'align=absmiddle',1)?></a> &nbsp;<a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><?=$data[goodsnm]?></a>
	<td align=right style="padding-right:10px" width=100 nowrap><font class=ver8 color=444444><?=number_format($data[price])?>원</td>
	<td align=center width=100 nowrap><font class=ver8 color=444444>
	<?=$data[sort]?>
	<input type=hidden name=sno[] value="<?=$data[sno]?>">
	<input type=hidden name=sort[] value="<?=$data['sort']?>">
	</td>
</tr>
<? }} ?>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="list.php"><img src='../img/btn_list.gif'></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">분류별페이지마다 구매자에게 어필하는 상품을 효과적으로 순서를 정해 진열하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">구매자들은 보통 특정분류에서 상품을 조회하고 구매의욕을 갖게 되는데 이때 상품의 진열은 중요합니다.<td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">각 분류별로 최대 300개의 상품순서를 변경하실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
