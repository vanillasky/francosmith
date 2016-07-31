<?
include "../lib.php";

/*
	통계 페이지내 그래프는 open flash chart (http://teethgrinder.co.uk/open-flash-chart )를 사용함
	LGPL 라이센스임.
*/
include '../../lib/ofc/php-ofc-library/open-flash-chart.php';

$where = array();

$pchno = $_POST['pchno'];

// 검색 조건
	$_POST['regdt'][0] = $sdate_s = ($_POST['regdt'][0]) ? $_POST['regdt'][0] : date('Ymd',strtotime('-7 day'));
	$_POST['regdt'][1] = $sdate_e = ($_POST['regdt'][1]) ? $_POST['regdt'][1] : date('Ymd');

	$sword = isset($_POST['sword']) ? iconv('utf-8','euc-kr',$_POST['sword']) : '';
	if ($sword) {
		$where[] = $_POST['skey']." like '%$sword%'";
	}

	$brandnm = isset($_POST['brandnm']) ? iconv('utf-8','euc-kr',$_POST['brandnm']) : '';
	if ($brandnm) {
		$where[] = " OI.brandnm = '$brandnm'";
	}
	$category = false;
	if ($_POST['cate']){
		$category = array_notnull($_POST['cate']);
		$category = $category[count($category)-1];
	}

	if (sizeof($_POST['settlekind']) < 1 || $_POST['settlekind']['all']) {
		$_POST['settlekind'] = array();
		$_POST['settlekind']['all'] = 1;
	}
	elseif (sizeof($_POST['settlekind']) === 6) {
		$_POST['settlekind'] = array();
		$_POST['settlekind']['all'] = 1;
	}
	else {
		$_tmp = array();
		foreach($_POST['settlekind'] as $k => $v) {
			if (!$v || $k == 'all') continue;

			$_tmp[] = " O.settlekind = '".$k."'";
		}

		if (!empty($_tmp)) $where[] = ' ('.implode(' OR ',$_tmp).') ';
	}

// sql
	if (empty($_POST['dtkind'])) $_POST['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_POST['dtkind'];

	$sub_query = "
	SELECT
		PCH.comnm, PCH.pchsno,
		DATE_FORMAT(O.$dtkind,'%Y-%m-%d') AS `date`,
		O.ordno,					/* 주문건수 */
		O.emoney,			/* 적립금 사용 금액 */
		(O.coupon + O.memberdc) AS dc,	/* 쿠폰, 회원할인 금액 */
		SUM(OI.price * OI.ea) AS goodsprice,			/* 상품가격 */
		O.delivery,		/* 결제금액中 배송비 */
		SUM(OI.supply * OI.ea) AS sub_supply	/* 매입금액 */

	FROM ".GD_ORDER." AS O

	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno

	INNER JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno

	LEFT JOIN ".GD_PURCHASE_GOODS." AS PCHG
	ON G.goodsno = PCHG.goodsno

	LEFT JOIN ".GD_PURCHASE." AS PCH
	ON PCHG.pchsno = PCH.pchsno
	";

	if ($category) {
		$sub_query .= "
		INNER JOIN ".GD_GOODS_LINK." AS LNK
		ON OI.goodsno=LNK.goodsno
		";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
	}

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);
	$where[] = "O.step2 < 40 AND O.step > 0";

	if (!is_null($pchno)) {
		if ((int)$pchno === 0) $where[] = "PCH.pchsno IS NULL";
		else $where[] = "PCH.pchsno = '$pchno'";

		$sub_query .= ' WHERE '.implode(' AND ', $where);
		$sub_query .= ' GROUP BY `date` ';
	}
	else {
		//$where[] = "PCH.pchsno IS NOT NULL ";
		$sub_query .= ' WHERE '.implode(' AND ', $where);
		$sub_query .= ' GROUP BY O.ordno, PCH.pchsno ';
	}

	$query = "
	SELECT
		IFNULL(SUB.comnm, '이름없음') AS comnm,
		IFNULL(SUB.pchsno, 0) AS pchsno,
		SUB.`date` AS `date`,
		COUNT(SUB.ordno) AS cnt,
		SUM(SUB.emoney) AS tot_emoney,			/* 적립금 사용 금액 */
		SUM(SUB.dc) AS tot_dc,	/* 쿠폰, 회원할인 금액 */
		SUM(SUB.goodsprice) AS tot_price,			/* 상품가격 */
		SUM(SUB.delivery) AS tot_delivery,		/* 결제금액中 배송비 */
		SUM(SUB.sub_supply) AS tot_supply	/* 매입금액 */

	FROM
	(
		".$sub_query."

	) AS SUB
	";

	if ($pchno) {
		$query .= ' GROUP BY SUB.`date` ';
		$query .= ' ORDER BY SUB.`date` ';
	}
	else {
		$query .= ' GROUP BY SUB.pchsno ';
		$query .= ' ORDER BY SUB.comnm ';
	}


// 쿼리
$rs = $db->query($query);
$rs_max = mysql_num_rows($rs);
$total = $arRow = $chart = array();
$multi = floor($rs_max / 10);

while ($_row = $db->fetch($rs,1)) {

	$row['date']		= $_row['date'];

	$row['comnm']		= $_row['comnm'];
	$row['pchsno']	= $_row['pchsno'];
	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_dc']		= $_row['tot_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];

	$row['tot_sales']	= $row['tot_price'] - $row['tot_dc'] - $row['tot_emoney'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$arRow[] = $row;

	// 차트 데이터
	$chart['data'][1][] = (int)$row['tot_sales'];
	$chart['data'][2][] = (int)$row['tot_supply'];
	$chart['data'][3][] = (int)$row['tot_earn'];

	$_m = max( array($row['tot_sales'],$row['tot_supply'],$row['tot_earn']));
	$chart['y_max'] = ($chart['y_max'] > $_m) ? (int)$chart['y_max'] : $_m;

	$chart['x_label'][] = $pchno ? $row['date'] : iconv('euc-kr','utf-8',$row['comnm']);

}

//  그래프

	// 일별
		$step = 1;
		$chart['color'][1] = '#A6A6A6';
		$chart['color'][2] = '#92D050';
		$chart['color'][3] = '#FF0000';

		$chart['Key'][1] = '매출금액';
		$chart['Key'][2] = '매입금액';
		$chart['Key'][3] = '판매이익';

		$ofc = new open_flash_chart();

		foreach($chart['data'] as $k => $data) {
			${'data'} = $data;
			${'bar'.$k} = new bar();
			${'bar'.$k}->colour( $chart['color'][$k] );
			${'bar'.$k}->set_values( $data );
			${'bar'.$k}->key( iconv('euc-kr','utf-8',$chart['Key'][$k]) , 12);

			$ofc->add_element( ${'bar'.$k} );
		}

		$tmp = pow(10,strlen($chart['y_max']) - 1);
		$chart['y_max'] =  ceil($chart['y_max'] / $tmp) * $tmp;

		$y = new y_axis();
		$y->set_range(0, $chart['y_max']);
		$y->set_colours( '#595D63', '#DEDEDE');

		$x = new x_axis();
		$x->set_colours( '#595D63', '#DEDEDE');

		$x_labels = new x_axis_labels();
		$x_labels->set_colour( '#595D63' );
		$x_labels->set_steps( ceil($rs_max / 11) );
		$x_labels->set_labels( $chart['x_label'] );
		$x_labels->set_size( 12 );

		$x->set_labels( $x_labels );

		$ofc->set_x_axis( $x );

		$ofc->set_y_axis( $y );

		$ofc->set_bg_colour( '#FFFFFF' );

		echo $ofc->toPrettyString();
?>
