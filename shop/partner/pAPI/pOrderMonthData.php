<?
/*********************************************************
* 파일명     :  pOrderMonthData.php
* 프로그램명 :	월별 주문 data
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
	$where[] = sprintf(" LNK.category like '%s%%'", $category);
}

$_param = array(
	$dtkind,
	Core::helper('Date')->min($sdate_s),
	Core::helper('Date')->max($sdate_e)
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
$page_res = $db->_select_page($list, $page, $query);


$total = $page_res['page']['totalcount'];

$res_data = Array();

$res = $page_res['record'];

$res_data = Array();
if(!empty($res) && is_array($res)) {
	foreach($res as $row) {
		$res_data[] = $row;
	}
}

echo $json->encode($res_data);
exit;
?>
