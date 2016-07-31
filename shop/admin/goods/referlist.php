<?

include "../lib.php";
include "../../conf/config.php";
include "../../lib/page.class.php";

// 상품분류 연결방식 전환 여부에 따른 처리
$whereArr	= getCategoryLinkQuery('a.category', $_GET['category']);

$db_table = "
".GD_GOODS_LINK." a,
".GD_GOODS." b,
".GD_GOODS_OPTION." c
";

$where[] = "a.goodsno=b.goodsno";
$where[] = "a.goodsno=c.goodsno and link and go_is_deleted <> '1'";
if ($_GET['category']) {
	$where[]	= $whereArr['where'];
	$distinct	= $whereArr['distinct'];
} else {
	$distinct	= 'distinct';
}
if ($_GET[goodsnm]) $where[] = "b.goodsnm like '%$_GET[goodsnm]%'";

$pg = new Page($_GET[page],7);
$pg->field = $distinct." a.goodsno,b.goodsnm,c.price,b.img_s";
$pg->setQuery($db_table,$where);
$pg->exec();

$res = $db->query($pg->query);

?>

<link rel="styleSheet" href="../style.admin.css">
<script>

function move(idx)
{
	var tb0 = document.getElementById('tb0');
	var tb = parent.document.getElementById('tbRefer');

	oTr = tb.insertRow();
	oTd = oTr.insertCell();
	oTd.innerHTML = tb0.rows[idx].cells[0].innerHTML;
	oTd = oTr.insertCell();
	oTd.innerHTML = tb0.rows[idx].cells[1].innerHTML;
	tb.moveRow(tb.rows.length-1,0);

	tb.rows[0].className = "hand";
	tb.rows[0].ondblclick = function(){ parent.remove(this); }
	parent.react_refer();
}

</script>

<body onselectstart="return false" scroll=no style="margin:0">

<div id=register_goods style="padding:3px">
<div class=referTitle>- 상품리스트 <? if ($_GET[category]){ ?>(<?=strip_tags(currPosition($_GET[category]))?>)<? } ?></div>

<table width=100% id=tb0 border=1 bordercolor='#E6E6E6' frame=hsides rules=rows style='border-collapse:collapse;'>
<?
while ($data=$db->fetch($res)){
	$bgcolor = ($idx++%2) ? "#f7f7f7" : "#ffffff";
?>
<tr bgcolor="<?=$bgcolor?>" ondblclick="move(this.rowIndex)" style="cursor:pointer">
	<td width=50 nowrap><?=goodsimg($data[img_s],40,'',1)?></td>
	<td width=100%>
	<div><?=$data[goodsnm]?></div>
	<b><?=number_format($data[price])?></b>
	<input type=hidden name=relation[] value="<?=$data[goodsno]?>">
	</td>
</tr>
<? } ?>
</table>

<div align=center class=eng style="padding:8px"><?=$pg->page[navi]?></div>
</div>
