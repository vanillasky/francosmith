<?
include "../_header.popup.php";



$where = array();


// �˻� ����
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

	$sub_query = "
	SELECT
		DATE_FORMAT(O.$dtkind,'%Y-%m-%d') AS `date`,
		O.ordno,					/* �ֹ��Ǽ� */
		O.emoney,			/* ������ ��� �ݾ� */
		(O.coupon + O.memberdc) AS dc,	/* ����, ȸ������ �ݾ� */
		SUM(OI.price * OI.ea) AS goodsprice,			/* ��ǰ���� */
		O.prn_settleprice,		/* �����ݾ� */
		O.delivery,		/* �����ݾ��� ��ۺ� */
		SUM(OI.supply * OI.ea) AS sub_supply	/* ���Աݾ� */

	FROM ".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno
	";

	if ($category){
		$sub_query .= "
		LEFT JOIN ".GD_GOODS_LINK." AS LNK
		ON OI.goodsno=LNK.goodsno
		";

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
	}
	$_param = array(
		$dtkind,
		Core::helper('Date')->min($_GET['date']),
		Core::helper('Date')->max($_GET['date'])
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);
	$where[] = "O.step2 < 40 AND O.step > 0 AND OI.istep < 40";

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno ';
	$sub_query .= ' ORDER BY NULL ';

	$query = "
	SELECT
		SUB.`date`,
		COUNT(SUB.ordno) AS cnt,
		SUM(SUB.emoney) AS tot_emoney,			/* ������ ��� �ݾ� */
		SUM(SUB.dc) AS tot_dc,	/* ����, ȸ������ �ݾ� */
		SUM(SUB.goodsprice) AS tot_price,			/* ��ǰ���� */
		SUM(SUB.prn_settleprice) AS tot_settle,		/* �����ݾ� */
		SUM(SUB.delivery) AS tot_delivery,		/* �����ݾ��� ��ۺ� */
		SUM(SUB.sub_supply) AS tot_supply	/* ���Աݾ� */

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY `date` ';
	$query .= ' ORDER BY `date` ';

// ����
$rs = $db->query($query);
$rs_max = $db->count_($rs);

$total = $arRow = $chart = array();

$multi = floor($rs_max / 10);

while ($_row = $db->fetch($rs,1)) {

	$row['date']		= $_row['date'];
	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_dc']		= $_row['tot_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_settle']	= $_row['tot_settle'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];

	$row['tot_sales']	= $row['tot_settle'] - $row['tot_delivery'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$arRow[] = $row;
}
$db->free($rs);
?>
<div class="title title_top">�Ϻ� ���� ���</div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th>��¥(�Ϻ�)</th>
	<th>�Ǽ�</th>
	<th>����������</th>
	<th>����</th>
	<th>�ֹ��ݾ�</th>
	<th>�����ݾ� (��ۺ�����)</th>
	<th>����ݾ� (��ۺ�����)</th>
	<th>���Աݾ�</th>
	<th>�Ǹ�����</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class="cell1"><?=$row['date']?></td>
	<td class="numeric ar"><?=number_format($row['payment_cnt'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_dc'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_price'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_settle'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_supply'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_earn'])?></td>
</tr>
<tr><td colspan=10 class=rndline></td></tr>
<? } ?>
<tr><td colspan=10 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th>�հ�</th>
	<td class="numeric ar"><?=number_format($total['payment_cnt'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_price'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_settle'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_supply'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_earn'])?></td>
</tr>
</tfoot>
<tr><td colspan=10 class=rndline></td></tr>
</table>
