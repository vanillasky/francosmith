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
$pg->setQuery($db_table,$where,'b.goodsno desc');
$pg->exec();

$res = $db->query($pg->query);

?>

<link rel="styleSheet" href="../style.css">
<script>

function move(idx)
{
	var tb0 = document.getElementById('tb0');
	var tb = parent.document.getElementById('tb_<?=$_GET[name]?>');

	oTr = tb.insertRow(0);
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = tb0.rows[idx].cells[0].innerHTML;
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = tb0.rows[idx].cells[1].innerHTML;

	tb.rows[0].className = "hand";
	parent.moveEvent(tb.rows[0], '<?=$_GET[name]?>');
	parent.react_goods('<?=$_GET[name]?>');
}

</script>

<body onselectstart="return false" scroll=no style="margin:0">

<div id=register_goods style="padding:3px">
<div class=boxTitle>- 상품리스트 <font class=small color=#F2F2F2>(등록하려면 더블클릭)</font></div>

<table id=tb0 class=tb>
<?
while ($data=$db->fetch($res)){
	$bgcolor = ($idx++%2) ? "#f7f7f7" : "#ffffff";
?>
<tr bgcolor="<?=$bgcolor?>" ondblclick="move(this.rowIndex)" class=hand>
	<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],"40,40",'',1)?></a></td>
	<td width=100% nowrap>
	<div style="overflow:hidden;"><?=strip_tags($data[goodsnm])?></div>
	<b><?=number_format($data[price])?></b>
	<input type=hidden name=e_<?=$_GET[name]?>[] value="<?=$data[goodsno]?>">
	</td>
</tr>
<? } ?>
</table>

<div align=center class=eng><?=$pg->page[navi]?></div>
</div>