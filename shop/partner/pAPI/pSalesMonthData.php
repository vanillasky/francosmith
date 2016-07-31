<?
/*********************************************************
* 파일명     :  pSalesMonthData.php
* 프로그램명 :	월별 매출 data
* 작성자     :  dn
* 생성일     :  2012.01.25
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### 인증키 Check (실제로는 아이디와 비번 임) 시작 ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### 인증키 Check 끝 ###

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		$tmp_key = str_replace('search_', '', $key);
		${$tmp_key} = $val;
	}
}

if(!$list) $list = 20;
if(!$page) $page = 1;


$where = Array();
if($sword) {
	$tmp_sword = '%'.$sword.'%';
	$where[] = $db->_query_print(' '.$skey.'like [s]', $tmp_sword);
}

if($settlekind != 'all') {
	$where[] = $db->_query_print('O.settlekind=[s]', $settlekind);
}

	$sub_query = "
	SELECT
		DATE_FORMAT(O.$dtkind,'%Y-%m') AS `date`,
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
		$where[] =sprintf(" LNK.category like '%s%%'", $category);
	}

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
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



// 쿼리
$page_res = $db->_select_page($list, $page, $query);


$total = $page_res['page']['totalcount'];

$res_data = Array();

$res = $page_res['record'];

$res_data = Array();
if(!empty($res) && is_array($res)) {
	foreach($res as $row) {
		$row['tot_sales']	= $row['tot_settle'] - $row['tot_delivery'];
		$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

		$res_data[] = $row;
	}
}


echo $json->encode($res_data);

exit;
?>
