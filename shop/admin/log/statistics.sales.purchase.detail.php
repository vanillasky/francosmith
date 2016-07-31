<?
include "../_header.popup.php";



$where = array();

$pchno = $_GET['pchno'];

// �˻� ����
	$_GET['regdt'][0] = $sdate_s = ($_GET['regdt'][0]) ? $_GET['regdt'][0] : date('Ymd',strtotime('-7 day'));
	$_GET['regdt'][1] = $sdate_e = ($_GET['regdt'][1]) ? $_GET['regdt'][1] : date('Ymd');

	$sword = isset($_GET['sword']) ? $_GET['sword'] : '';
	if ($sword) {
		$where[] = $_GET['skey']." like '%$sword%'";
	}

	$brandnm = isset($_GET['brandnm']) ? $_GET['brandnm'] : '';
	if ($brandnm) {
		$where[] = " OI.brandnm = '$brandnm'";
	}
	$category = false;
	if ($_GET['cate']){
		$category = array_notnull($_GET['cate']);
		$category = $category[count($category)-1];
	}

	if (sizeof($_GET['settlekind']) < 1 || $_GET['settlekind']['all']) {
		$_GET['settlekind'] = array();
		$_GET['settlekind']['all'] = 1;
	}
	elseif (sizeof($_GET['settlekind']) === 6) {
		$_GET['settlekind'] = array();
		$_GET['settlekind']['all'] = 1;
	}
	else {
		$_tmp = array();
		foreach($_GET['settlekind'] as $k => $v) {
			if (!$v || $k == 'all') continue;

			$_tmp[] = " O.settlekind = '".$k."'";
		}

		if (!empty($_tmp)) $where[] = ' ('.implode(' OR ',$_tmp).') ';
	}

// sql
	if (empty($_GET['dtkind'])) $_GET['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_GET['dtkind'];

	$query = "
	SELECT
		OI.goodsno, OI.goodsnm, G.img_s,
		COUNT(O.ordno) AS cnt,					/* �ֹ��Ǽ� */
		SUM(OI.ea) AS tot_ea,			/* ������ ��� �ݾ� */
		SUM(O.emoney) AS tot_emoney,			/* ������ ��� �ݾ� */
		SUM(O.coupon + O.memberdc) AS tot_dc,	/* ����, ȸ������ �ݾ� */
		SUM(OI.price * OI.ea) AS tot_price,			/* ��ǰ���� */
		SUM(O.delivery) AS tot_delivery,		/* �����ݾ��� ��ۺ� */

		SUM(OI.supply * OI.ea) AS tot_supply	/* ���Աݾ� */

	FROM ".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno

	LEFT JOIN ".GD_PURCHASE_GOODS." AS PCHG
	ON G.goodsno = PCHG.goodsno

	LEFT JOIN ".GD_PURCHASE." AS PCH
	ON PCHG.pchsno = PCH.pchsno
	";

	if ($category){
		$query .= "
		LEFT JOIN ".GD_GOODS_LINK." AS LNK
		ON OI.goodsno=LNK.goodsno
		";

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
	}

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);
	$where[] = "O.step2 < 40 AND O.step > 0 AND OI.istep < 40";

	if ((int)$pchno === 0) $where[] = "PCH.pchsno IS NULL";
	else $where[] = "PCH.pchsno = '$pchno'";

	$query .= ' WHERE '.implode(' AND ', $where);
	$query .= ' GROUP BY G.goodsno ';
	//$query .= ' ORDER BY G.goodsno ';

// ����
$rs = $db->query($query);
$rs_max = $db->count_($rs);

$total = $arRow = $chart = $sort = array();

$multi = floor($rs_max / 10);

while ($_row = $db->fetch($rs,1)) {

	$row['goodsnm']		= $_row['goodsnm'];
	$row['goodsno']	= $_row['goodsno'];
	$row['img_s']		= $_row['img_s'];

	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_ea']	= $_row['tot_ea'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_dc']		= $_row['tot_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];

	$row['tot_sales']	= $row['tot_price'] - $row['tot_dc'] - $row['tot_emoney'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$sort[] = $row['tot_earn'];
	$arRow[] = $row;
}
$db->free($rs);

@array_multisort($sort, SORT_DESC, $arRow);

// ����ó��
$purchase = $db->fetch("SELECT comnm FROM ".GD_PURCHASE." WHERE pchsno = $pchno",1);
?>
<div class="title title_top"><?=$purchase['comnm']?> ��ǰ�� �����̷�</div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th>����</th>
	<th></th>
	<th></th>
	<th>��ǰ��</th>
	<th>�����ڼ�</th>
	<th>�Ǹż���</th>

	<th>����ݾ�</th>
	<th>���Աݾ�</th>
	<th>�Ǹ�����</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>

<?
$rank=0;
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class="rank"><font class=ver8 color=616161><?=++$rank?></font></td>
	<td class="goods-image"><a href="../../goods/goods_view.php?goodsno=<?=$row['goodsno']?>" target=_blank><?=goodsimg($row['img_s'],40,'',1)?></a></td>
	<td width="10"></td>
	<td class="goods-name al"><?=($row['goodsnm'])?></td>
	<td class="numeric ar"><?=number_format($row['payment_cnt'])?></td>
	<td class="numeric ar"><?=number_format($row['tot_ea'])?></td>

	<td class="numeric highlight  ar"><?=number_format($row['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_supply'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_earn'])?></td>
</tr>
<tr><td colspan=10 class=rndline></td></tr>
<? } ?>
<tr><td colspan=10 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th colspan="4">�հ�</th>

	<td class="numeric ar"><?=number_format($total['payment_cnt'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_ea'])?></td>

	<td class="numeric highlight  ar"><?=number_format($total['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_supply'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_earn'])?></td>

</tr>
</tfoot>
<tr><td colspan=10 class=rndline></td></tr>
</table>
