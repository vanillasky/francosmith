<?
	$location = "����ó ���� > ���� ���� ��ǰ";
	include "../_header.php";
	include "../../lib/page.class.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[����ó ���� ��� ����] > [��ǰ ����ó ����]�� ���� �ϼ���.", -1);

	$pchsno			= isset($_GET['pchsno'])		? $_GET['pchsno']		: "";
	$cate			= isset($_GET['cate'])			? $_GET['cate']			: array();
	$skey			= isset($_GET['skey'])			? $_GET['skey']			: "";
	$sword			= isset($_GET['sword'])			? $_GET['sword']		: "";
	$page_num		= isset($_GET['page_num'])		? $_GET['page_num']		: 10;
	$sort			= isset($_GET['sort'])			? $_GET['sort']			: "O.stock ASC";
	$page			= isset($_GET['page'])			? $_GET['page']			: 1;

	list($total) = $db->fetch("SELECT COUNT(*) FROM gd_goods_option WHERE stock <= '".$purchaseSet['popStock']."' and go_is_deleted <> '1'");

	### �����Ҵ�
		$selected['page_num'][$page_num] = "selected";
		$selected['sort'][$sort] = "selected";

	### ���
		$db_table = " gd_goods_option AS O
		LEFT JOIN gd_goods AS G
		ON O.goodsno = G.goodsno
		LEFT JOIN ".GD_PURCHASE." AS P
		ON O.pchsno = P.pchsno";

	### �˻�
	$where[] = " O.stock <= '".$purchaseSet['popStock']."'";
	if($pchsno) {
		list($thisCode) = $db->fetch("SELECT comcd FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
		$where[] = "O.pchsno = '$pchsno'";
	}
	if($sword) $where[] = "$skey LIKE '%$sword%'";
	if(!empty($cate)) {
		$category = array_notnull($cate);
		$category = $category[count($category) - 1];

		/// ī�װ��� �ִ� ��� ��� ���̺� ������
		if($category) {
			$addField .= ", L.category ";
			$db_table .= " LEFT JOIN ".GD_GOODS_LINK." AS L ON O.goodsno = L.goodsno";

			// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
			$whereArr	= getCategoryLinkQuery('L.category', $category, null, 'O.sno');
			$where[]	= $whereArr['where'];
			$groupby	= $whereArr['group'];
		}
	}

	$where[] = "go_is_deleted <> '1'";

	$pg = new Page($page, $page_num);
	$pg->field = " O.sno, O.opt1, O.opt2, O.price, O.stock,
	G.goodsno, G.goodsnm, G.img_s,
	P.pchsno, P.comnm, P.phone1 ".$addField;
	$pg->setQuery($db_table, $where, $sort, $groupby);

	$pg->setTotal();
	$res = $db->query($pg->query);
	$pg->exec();

	$qstr = "pchsno=".$pchsno."&sort=".$sort."&page_num=".$page_num."&skey=".$skey."&sword=".$sword;
	if(count($cate)) {
		foreach($cate as $k => $v) {
			$qstr .= "&cate[]=".$v;
		}
	}

	$pchs = $db->fetch("SELECT * FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
?>

<script>
	function iciSelect(obj) {
		var row = obj.parentNode.parentNode;
		row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
	}
</script>

<form>
<div class="title title_top">���� ���� ��ǰ<span><b>[��ǰ ���� �˸� ��� ����]</b>���� ������ ��� ������ ��ǰ ����Ʈ�� ��� �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=27')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb">
<col class="cellC" /><col style="padding-left:10px;" />
<tr>
	<td>����ó �˻�</td>
	<td>
		<select name="pchsno" id="pchsno" onchange="location.href='<?=$_SERVER['PHP_SELF']?>?pchsno=' + this.value;">
			<option value="">����ó����</option>
<?
	$sql_pchs = "SELECT * FROM ".GD_PURCHASE." ORDER BY ordgrade DESC, comnm ASC";
	$rs_pchs = $db->query($sql_pchs);
	for($i = 0; $row_pchs = $db->fetch($rs_pchs); $i++) {
?>
			<option value="<?=$row_pchs['pchsno']?>"<?=($row_pchs['pchsno'] == $pchsno) ? "selected" : ""?>><?=$row_pchs['comnm']?></option>
<?
	}
?>
		</select>
		<a href="javascript:;" onclick="window.open('../goods/popup.purchase_find.php?ctrlType=url', 'purchaseSearchPop', 'width=640,height=450');"><img src="../img/purchase_find.gif" title="����ó �˻�" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>�з�����</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name="skey">
<? foreach ( array('G.goodsnm'=>'��ǰ��','G.goodsno'=>'������ȣ','G.goodscd'=>'��ǰ�ڵ�','G.keyword'=>'����˻���') as $k => $v) { ?>
		<option value="<?=$k?>" <?=($k == $skey) ? 'selected' : ''?>><?=$v?></option>
<? } ?>
	</select>
	<input type=text name="sword" class="lline" value="<?=$sword?>" class="line">
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
<table width="100%">
<tr>
	<td class="pageInfo">
		�� <font class="ver8"><b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
		<select name="sort" onchange="this.form.submit();">
			<option value="O.stock ASC" <?=$selected['sort']['O.stock ASC']?>>��� ���ġ�</option>
			<option value="O.stock DESC" <?=$selected['sort']['O.stock DESC']?>>��� ���ġ�</option>
			<option value="G.goodsnm ASC" <?=$selected['sort']['G.goodsnm ASC']?>>��ǰ�� ���ġ�</option>
			<option value="G.goodsnm DESC" <?=$selected['sort']['G.goodsnm DESC']?>>��ǰ�� ���ġ�</option>
			<option value="P.comnm ASC" <?=$selected['sort']['P.comnm ASC']?>>����ó�� ���ġ�</option>
			<option value="P.comnm DESC" <?=$selected['sort']['P.comnm DESC']?>>����ó�� ���ġ�</option>
		</select>&nbsp;
		<select name="page_num" onchange="this.form.submit();">
<?
	$r_pagenum = array(10, 20, 40, 60, 100);
	foreach ($r_pagenum as $v) {
?>
			<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���</option>
<? } ?>
		</select>
	</td>
</tr>
</table>
</form>

<form name="pList" method="post" action="../goods/indb.purchase.php">
<input type="hidden" name="mode" value="pchs_manager" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="page" value="<?=$page?>" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="14"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th width="50"></th>
	<th>��ǰ��</th>
	<th>�ɼ�1</th>
	<th>�ɼ�2</th>
	<th>���</th>
	<th>����ó</th>
	<th>����ó</th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<?
	while($data=$db->fetch($res)) {
		$pchsData = $db->fetch("SELECT * FROM ".GD_PURCHASE_GOODS." WHERE goodsno = '".$data['goodsno']."' AND opt1 = '".$data['opt1']."' AND opt2 = '".$data['opt2']."' ORDER BY pchsdt DESC LIMIT 0, 1");
?>
<tr height="50" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data['img_s'], 40, '', 1)?></a></td>
	<td align="left" title="<?=$data['goodsnm']?>"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600)"><?=strcut($data['goodsnm'], 50)?></a></td>
	<td><?=$data['opt1']?></td>
	<td><?=$data['opt2']?></td>
	<td><?=$data['stock']?></td>
	<td><a href="../goods/purchase_info.php?mode=pchs_mod&pchsno=<?=$data['pchsno']?>"><?=$data['comnm']?></a></td>
	<td><?=$data['phone1'].((str_replace("-", "", $data['phone1'])) ? " <a href=\"javascript:popup('../member/popup.sms.php?mobile=".str_replace("-", "", $data['phone1'])."',780,600)\"><img src=\"../img/btn_smsmailsend.gif\" align=\"absmiddle\"></a>" : "")?></td>
</tr>
<input type="hidden" name="pchsno[]" id="pgno_<?=$data['sno']?>" value="<?=$data['pchsno']?>" />
<input type="hidden" name="pgno[]" value="<?=$pchsData['pgno']?>" />
<input type="hidden" name="sno[]" value="<?=$data['sno']?>" />
<tr><td colspan="14" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr height="60">
	<td align="left" width="154"><a href="javascript:popup('../goods/dnXls_purchase.php?<?=$qstr?>');"><img src="../img/btn_gooddown.gif" style="border:0px;" /></a></td>
	<td align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
	<td align="left" width="154"></td>
</tr>
</table>

</form>
<script>window.onload = function(){ UNM.inner();};</script>
<? include "../_footer.php"; ?>
