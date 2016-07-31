<?php
include "../_header.popup.php";
include "../../lib/page.class.php";

### ���� ����
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." WHERE todaygoods='n' AND goodsno <> '$_GET[goodsno]'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";

$orderby = "-a.goodsno";
$div = explode(" ",$orderby);

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
left join ".GD_GOODS_RELATED." c on c.r_goodsno = a.goodsno and c.goodsno = '$_GET[goodsno]'

";
$where[] = "a.todaygoods='n'";

if ($_GET[goodsno]) $where[] = "a.goodsno <> '$_GET[goodsno]'";

if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." d on a.goodsno=d.goodsno";

	// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
	$where[]	= getCategoryLinkQuery('d.category', $category, 'where');
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,
b.price,b.reserve,a.use_emoney,
IF (c.goodsno,1,0) as related
";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);
?>
<script>
function _chkForm(f) {

	if ($$('input[name="chk[]"]:checked').size() < 1)
	{
		alert('���û�ǰ���� ����� ��ǰ�� ������ �ּ���.');
		return false;
	}

	return true;
}
</script>

<div class="title title_top">���û�ǰ ����ϱ�</div>


<form name=frmList>

<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td colspan=3>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -
	<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
	</td>
	<td>��ǰ��¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>

</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>

<form name="frmRelatedGoods" method="post" action="indb.related.php" target="ifrmHidden" onsubmit="return _chkForm(this);">
<input type="hidden" name="mode" value="register">
<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width="40">
<col width="40">
<col width="">
<col width="80">
<col width="80">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>����</a></th>
	<th></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<tr><td height=4 colspan=12></td></tr>
<?
while ($data=$db->fetch($res)){
	if ($data[usestock] && $data[totstock] < 1) $data[runout] = 1;
?>
<tr align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data[goodsno]?>" <?=$data[related] ? 'checked disabled' : ''?>></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],"40,40",'',1)?></a></td>
	<td align="left" style="padding-left:5px;">
		<?=$data[goodsnm]?>
		<p style="margin:0;"><b><?=number_format($data[price])?></b></p>
		<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td><?=array_shift(explode(' ',$data[regdt]))?></td>
	<td><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div class="button_top">
<input type="image" src="../img/btn_register.gif">
</div>

</form>

<script>
linecss();
table_design_load();
</script>