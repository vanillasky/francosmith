<?
include "../lib.php";

$cutting = Clib_Application::iapi('number')->getCuttingConfigString(
	$_POST['goods_discount_by_term_use_cutting'],
	$_POST['goods_discount_by_term_cutting_unit'],
	$_POST['goods_discount_by_term_cutting_method']
);

$_discount = array(
	'gd_start_date' => 0,
	'gd_end_date' => 0,
	'gd_cutting' => $cutting,
	'gd_level' => array(),
	'gd_amount' => array(),
	'gd_unit' => array(),
);

if ($_POST['goods_discount_by_term_range_date'][0]) {
	$_discount['gd_start_date'] = Core::helper('date')->min($_POST['goods_discount_by_term_range_date'][0] . $_POST['goods_discount_by_term_range_hour'][0] . $_POST['goods_discount_by_term_range_min'][0], false);
}

if ($_POST['goods_discount_by_term_range_date'][1]) {
	$_discount['gd_end_date'] = Core::helper('date')->max($_POST['goods_discount_by_term_range_date'][1] . $_POST['goods_discount_by_term_range_hour'][1] . $_POST['goods_discount_by_term_range_min'][1], false);
}

// 회원 그룹 지정
if ($_POST['goods_discount_by_term_for_specify_member_group'] === '1') {

	foreach ($_POST['goods_discount_by_term_target'] as $k => $v) {
		$_discount['gd_level'][] = $_POST['goods_discount_by_term_target'][$k];
		$_discount['gd_amount'][] = preg_replace('/[^0-9\.]/','',$_POST['goods_discount_by_term_amount'][$k]);
		$_discount['gd_unit'][] = $_POST['goods_discount_by_term_amount_type'][$k];
	}

	$_discount['gd_level'] = implode(',', $_discount['gd_level']);
	$_discount['gd_amount'] = implode(',', $_discount['gd_amount']);
	$_discount['gd_unit'] = implode(',', $_discount['gd_unit']);

}
// 회원 및 비회원 전체
else if ($_POST['goods_discount_by_term_for_specify_member_group'] === '2') {
	$_discount['gd_level'] = '0';
	$_discount['gd_amount'] = $_POST['goods_discount_by_term_amount_for_nonmember_all'];
	$_discount['gd_unit'] = $_POST['goods_discount_by_term_amount_type_for_nonmember_all'];
}
// 회원 전체
else {
	$_discount['gd_level'] = '*';
	$_discount['gd_amount'] = $_POST['goods_discount_by_term_amount_for_all'];
	$_discount['gd_unit'] = $_POST['goods_discount_by_term_amount_type_for_all'];
}

$discount = Clib_Application::getModelClass('goods_discount');
$goods = Clib_Application::getModelClass('goods');

foreach($_POST['chk'] as $goodsno) {

	$discount->resetData();
	$discount->load($goodsno);

	foreach($_discount as $k => $v) {
		$discount->setData($k, $v);
	}

	// 다음 요약 EP
	$ar_update['discount'] = $discount;
	daum_goods_diff($goodsno,$ar_update);
	
	if (!$discount->hasLoaded()) {
		$discount->setId($goodsno);
	}

	$discount->save();

	$goods->resetData();
	$goods->load($goodsno);
	$goods->setData('use_goods_discount', 1);
	$goods->save();

}

echo '
<script>
alert("저장되었습니다.");
parent.location.reload();
</script>
';
