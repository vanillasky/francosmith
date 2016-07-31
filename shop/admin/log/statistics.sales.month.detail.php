<?
include "../_header.popup.php";



$where = array();


// 검색 조건
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
		O.ordno,					/* 주문건수 */
		O.emoney,			/* 적립금 사용 금액 */
		(O.coupon + O.memberdc) AS dc,	/* 쿠폰, 회원할인 금액 */
		SUM(OI.price * OI.ea) AS goodsprice,			/* 상품가격 */
		O.prn_settleprice,		/* 결제금액 */
		O.delivery,		/* 결제금액中 배송비 */
		SUM(OI.supply * OI.ea) AS sub_supply	/* 매입금액 */

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

		// 상품분류 연결방식 전환 여부에 따른 처리
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
		SUM(SUB.emoney) AS tot_emoney,			/* 적립금 사용 금액 */
		SUM(SUB.dc) AS tot_dc,	/* 쿠폰, 회원할인 금액 */
		SUM(SUB.goodsprice) AS tot_price,			/* 상품가격 */
		SUM(SUB.prn_settleprice) AS tot_settle,		/* 결제금액 */
		SUM(SUB.delivery) AS tot_delivery,		/* 결제금액中 배송비 */
		SUM(SUB.sub_supply) AS tot_supply	/* 매입금액 */

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY `date` ';
	$query .= ' ORDER BY `date` ';

// 쿼리
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
<div class="title title_top">일별 매출 통계</div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th>날짜(일별)</th>
	<th>건수</th>
	<th>적립금적용</th>
	<th>할인</th>
	<th>주문금액</th>
	<th>결제금액 (배송비포함)</th>
	<th>매출금액 (배송비제외)</th>
	<th>매입금액</th>
	<th>판매이익</th>
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
	<th>합계</th>
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
