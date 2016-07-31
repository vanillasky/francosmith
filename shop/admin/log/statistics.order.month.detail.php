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

	if ((int)$_GET['amount'][0] > 0) $where[] = " O.prn_settleprice >= ".(int)$_GET['amount'][0];
	if ((int)$_GET['amount'][1] > 0) $where[] = " O.prn_settleprice <= ".(int)$_GET['amount'][1];

// sql
	if (empty($_GET['dtkind'])) $_GET['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_GET['dtkind'];

	$sub_query = "
	SELECT

		DATE_FORMAT(O.$dtkind,'%Y-%m-%d') AS `date`,
		O.ordno,O.step2,O.step,	O.prn_settleprice

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

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno ';



	$query = "
	SELECT
		SUB.`date`,

		COUNT( IF(SUB.step2 >= 40 AND SUB.step2 <= 49,1,null) )				  AS `cnt_step_cancel`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '0',1,null) ) AS `cnt_step_0`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '1',1,null) ) AS `cnt_step_1`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '2',1,null) ) AS `cnt_step_2`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '3',1,null) ) AS `cnt_step_3`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '4',1,null) ) AS `cnt_step_4`,

		SUM( IF(SUB.step2 >= 40 AND SUB.step2 <= 49, SUB.prn_settleprice ,0) )				 AS `amount_step_cancel`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '0',SUB.prn_settleprice,0) ) AS `amount_step_0`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '1',SUB.prn_settleprice,0) ) AS `amount_step_1`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '2',SUB.prn_settleprice,0) ) AS `amount_step_2`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '3',SUB.prn_settleprice,0) ) AS `amount_step_3`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '4',SUB.prn_settleprice,0) ) AS `amount_step_4`

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

$total = $arRow = array();

while ($row = $db->fetch($rs,1)) {
	$total = get_total($total, $row);
	$arRow[] = $row;
}
$db->free($rs);
?>
<div class="title title_top">일별 주문통계</div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=15></td></tr>
<tr class=rndbg>
	<th>날짜</th>
	<th colspan="2">총주문건</th>
	<th colspan="2">주문접수</th>
	<th colspan="2">입금확인</th>
	<th colspan="2">배송준비</th>
	<th colspan="2">배송중</th>
	<th colspan="2">배송완료</th>
	<th colspan="2">주문취소</th>

</tr>
<tr><td class=rnd colspan=15></td></tr>
<tr height=25 align="center">
	<th>일별</th>
	<th>건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th class="cell1">금액</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>
<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class="cell1"><?=$row['date']?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_0'] + $row['cnt_step_1'] + $row['cnt_step_2'] + $row['cnt_step_3'] + $row['cnt_step_4'] + $row['cnt_step_cancel'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_0'] + $row['amount_step_1'] + $row['amount_step_2'] + $row['amount_step_3'] + $row['amount_step_4'] + $row['amount_step_cancel'])?></td>

	<td class="numeric ar"><?=number_format($row['cnt_step_0'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_0'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_1'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_1'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_2'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_2'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_3'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_3'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_4'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_4'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_cancel'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['amount_step_cancel'])?></td>
</tr>
<tr><td colspan=15 class=rndline></td></tr>
<? } ?>
<tr><td colspan=15 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th>합계</th>
	<td class="numeric ar"><?=number_format($total['cnt_step_0'] + $total['cnt_step_1'] + $total['cnt_step_2'] + $total['cnt_step_3'] + $total['cnt_step_4'] + $total['cnt_step_cancel'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_0'] + $total['amount_step_1'] + $total['amount_step_2'] + $total['amount_step_3'] + $total['amount_step_4'] + $total['amount_step_cancel'])?></td>

	<td class="numeric ar"><?=number_format($total['cnt_step_0'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_0'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_1'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_1'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_2'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_2'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_3'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_3'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_4'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_4'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_cancel'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_cancel'])?></td>
</tr>
</tfoot>
<tr><td colspan=10 class=rndline></td></tr>
</table>
