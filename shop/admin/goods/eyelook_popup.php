<?
include "../_header.popup.php";
include "../../lib/page.class.php";

// ���� �ޱ� �� �⺻�� ����
if (!$_GET['page_num']) $_GET['page_num'] = 10;
$_GET['cate'] = isset($_GET['cate']) ? $_GET['cate'] : array();
$selected['skey'][$_GET['skey']]			= "selected";
$selected['page_num'][$_GET['page_num']]	= "selected";

if (!empty($_GET[cate])) {
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];

	/// ī�װ��� �ִ� ��� ��� ���̺� ������
	if ($category) {
		$cate_db_table = " left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
}

if($_GET['sword']) {
	if($_GET['skey'] == "all") {
		$where[] = "CONCAT(a.goodsnm, a.goodsno, a.goodscd) LIKE '%".$_GET['sword']."%'";
	} else {
		$where[] = "a.".$_GET['skey']." LIKE '%".$_GET['sword']."%'";
	}
}

$where[] = "a.open = 1";
$where[] = "a.runout = 0";

$db_table = GD_GOODS." a left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'".$cate_db_table;

$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt DESC"; # ���� ����

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "a.goodsno, a.goodsnm, a.img_s, a.regdt, b.price, a.totstock, a.open";
$pg->setQuery($db_table, $where, $orderby);
$pg->exec();
$res = $db->query($pg->query);
$total = $pg->recode['total'];

?>
<script>
function goods_send() {
	var rdo = document.getElementsByName('chk');

	for(i = 0; i < rdo.length; i++) {
		if(rdo[i].checked) {
			opener.document.getElementById('goodsno').value = rdo[i].value;
			opener.document.getElementById('goodsnm').innerHTML = document.getElementById('goodsnm_'+rdo[i].value).outerHTML;
			self.close();
			return true;
		}
	}
}
</script>
<div class="title title_top">��ǰ ����</div>

<form style="margin:0px; padding:0px;">
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr>
	<td>
		<script>new categoryBox('cate[]',4,'<?=$category?>');</script> <br />
		<select name="skey">
			<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
			<option value="goodsnm" <?=$selected['skey']['goodsnm']?>> ��ǰ�� </option>
			<option value="goodsno" <?=$selected['skey']['goodsno']?>> ������ȣ </option>
			<option value="goodscd" <?=$selected['skey']['goodscd']?>> ��ǰ�ڵ� </option>
		</select>
		<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
		<input type="image" align="absmiddle" style="border:0px;" src="../img/btn_search2.gif" />
	</td>
</tr>
</table>
</form>

<form name="gList" method="post">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th>����</th>
	<th></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>����</th>
	<th>���</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
while($data = $db->fetch($res)) {
?>
<tr height=40 align="center">
	<td width="40px;" class="noline"><input type="radio" name="chk" value="<?=$data['goodsno']?>" /></td>
	<td width="40px;" align="left"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td align="left" style="padding-left:5px;"><font class="small" color="#616161"><div id="goodsnm_<?=$data['goodsno']?>"><?=$data['goodsnm']?></div></font></td>
	<td width="100px;"><font class="small" color="#616161"><?=substr($data['regdt'], 0, 10)?></font></td>
	<td width="80px;"><font class="ver81" color="#616161"><?=number_format($data['price'])?></font></td>
	<td width="80px;"><font class="ver81" color="#616161"><?=$data['totstock']?></font></td>
	<td width="50px;"><font class="ver81" color="#616161"><img src="../img/icn_<?=$data[open]?>.gif"></font></td>
</tr>
<tr><td colspan="9" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
<tr>
	<td height="35" align="center"><a onclick="goods_send();" style="cursor:pointer;"><img src="../img/btn_cancelconfirm.gif" /></a></td>
</tr>
</table>

</form>
<body>
</html>