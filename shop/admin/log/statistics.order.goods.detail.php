<?
include "../_header.popup.php";



$where = array();


// 검색 조건
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

	if ((int)$_GET['amount'][0] > 0) $where[] = " O.prn_settleprice >= ".(int)$_GET['amount'][0];
	if ((int)$_GET['amount'][1] > 0) $where[] = " O.prn_settleprice <= ".(int)$_GET['amount'][1];

// sql
	if (empty($_GET['dtkind'])) $_GET['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_GET['dtkind'];

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);

// sql
	$query = "
	SELECT

		OI.goodsno, OI.goodsnm, OI.price, OI.opt1, OI.opt2, G.img_s,

		COUNT( IF(O.step2 >= 40 AND O.step2 <= 49,1,null) )				  AS `cnt_step_cancel`,
		COUNT( IF(O.step2 < 40 AND O.step = '0',1,null) ) AS `cnt_step_0`,
		COUNT( IF(O.step2 < 40 AND O.step = '1',1,null) ) AS `cnt_step_1`,
		COUNT( IF(O.step2 < 40 AND O.step = '2',1,null) ) AS `cnt_step_2`,
		COUNT( IF(O.step2 < 40 AND O.step = '3',1,null) ) AS `cnt_step_3`,
		COUNT( IF(O.step2 < 40 AND O.step = '4',1,null) ) AS `cnt_step_4`

	FROM ".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno
	";

	if ($category){
		$query .= "
		LEFT JOIN ".GD_GOODS_LINK." AS LNK
		ON OI.goodsno=LNK.goodsno
		";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
	}

	$where[] = "OI.goodsno = '$_GET[goodsno]'";

	$query .= ' WHERE '.implode(' AND ', $where);
	$query .= ' GROUP BY OI.goodsno, OI.opt1, OI.opt2';

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
<div class="title title_top">상품 옵션별 주문통계</div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=15></td></tr>
<tr class=rndbg>
	<th>번호</th>
	<th></th>
	<th></th>
	<th>상품명</th>
	<th>가격</th>

	<th>옵션1</th>
	<th>옵션2</th>

	<th>총주문건</th>
	<th>주문접수</th>
	<th>입금확인</th>
	<th>배송준비</th>
	<th>배송중</th>
	<th>배송완료</th>
	<th>주문취소</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>

<?
$idx = 0;
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25 align="center">
	<td><?=++$idx?></td>
	<td class="goods-image"><a href="javascript:void(0);" onClick="fnDetailStatistic('<?=$row['goodsno']?>');"><?=goodsimg($row['img_s'],40,'',1)?></a></td>
	<td width="10"></td>
	<td class="goods-name al"><?=($row['goodsnm'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['price'])?></td>

	<td><?=$row['opt1']?></td>
	<td><?=$row['opt2']?></td>

	<td class="cell1 numeric highlight "><?=number_format($row['cnt_step_0'] + $row['cnt_step_1'] + $row['cnt_step_2'] + $row['cnt_step_3'] + $row['cnt_step_4'] + $row['cnt_step_cancel'])?></td>

	<td><?=number_format($row['cnt_step_0'])?></td>
	<td><?=number_format($row['cnt_step_1'])?></td>
	<td><?=number_format($row['cnt_step_2'])?></td>
	<td><?=number_format($row['cnt_step_3'])?></td>
	<td><?=number_format($row['cnt_step_4'])?></td>
	<td><?=number_format($row['cnt_step_cancel'])?></td>
</tr>
<tr><td colspan=15 class=rndline></td></tr>
<? } ?>
<tr><td colspan=15 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th colspan="7">합계</th>

	<td><?=number_format($total['cnt_step_0'] + $total['cnt_step_1'] + $total['cnt_step_2'] + $total['cnt_step_3'] + $total['cnt_step_4'] + $total['cnt_step_cancel'])?></td>

	<td><?=number_format($total['cnt_step_0'])?></td>
	<td><?=number_format($total['cnt_step_1'])?></td>
	<td><?=number_format($total['cnt_step_2'])?></td>
	<td><?=number_format($total['cnt_step_3'])?></td>
	<td><?=number_format($total['cnt_step_4'])?></td>
	<td><?=number_format($total['cnt_step_cancel'])?></td>
</tr>
</tfoot>
<tr><td colspan=15 class=rndline></td></tr>
</table>
