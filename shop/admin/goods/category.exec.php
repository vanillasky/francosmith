<?

include "../../lib/library.php";

if (empty($_GET['category']) === false) {
	$data = $db->fetch("select * from ".GD_CATEGORY." where category='".$_GET['category']."'",1);

	// 상품분류 연결방식 전환 여부에 따른 처리
	$whereArr	= getCategoryLinkQuery('category', $_GET['category']);
	list($cntGoods) = $db->fetch("select count(".$whereArr['distinct']." goodsno) from ".GD_GOODS_LINK." where ".$whereArr['where']);

	@include "../../conf/category/".$data['category'].".php";
}

?>

<script>

var form = parent.document.form;
<? if ($_GET[category]){ ?>
parent.document.getElementById('currPosition').innerHTML = "<?=currPosition($data[category],1)?>";
form.catnm.style.display = "block";
form.catnm.disabled = false;
<? } else { ?>
parent.document.getElementById('currPosition').innerHTML = "전체분류";
form.catnm.style.display = "none";
form.catnm.disabled = true;
<? } ?>

parent.document.getElementById('cntGoods').innerHTML = "<?=$cntGoods?>";

form.catnm.value = "<?=$data[catnm]?>";
form.category.value = "<?=$data[category]?>";

for (i=0;i<form['lstcfg[tpl]'].length;i++){
	if (form['lstcfg[tpl]'][i].value=="<?=$lstcfg[tpl]?>") form['lstcfg[tpl]'][i].checked = true;
}
form['lstcfg[size]'].value = "<?=$lstcfg[size]?>";
form['lstcfg[page_num]'].value = "<?=@implode(',',$lstcfg[page_num])?>";
form['lstcfg[cols]'].value = "<?=$lstcfg[cols]?>";

</script>