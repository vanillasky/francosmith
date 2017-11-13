<?
include '../lib.php';
include '../../lib/naverPartner.class.php';

function responseFail(){
	echo 'fail';
	exit;
}

$naver = new naverPartner();
if (!is_object($naver)) {
	responseFail();
}

$category = '';
if ($_POST['mode'] == 'category') {
	$category = $_POST['category'];

	$query = $naver->getSelectGoodsCount($category);
	if(!$query) responseFail();

	list($count) = $db->fetch($query);
	echo $count;
}
else if ($_POST['mode'] == 'goods') {
	$query = $naver->getGoodsAllCount();
	if(!$query) responseFail();

	list($count) = $db->fetch($query);
	echo $count;
}
// 저장된 카테고리 리스트
else if (!$_POST['mode']) {
	$categoryList = $naver->getCategoryDetailed();
	$category = Array();
	for ($i=0; $i<count($categoryList); $i++) {
		if (strlen($categoryList[$i]['category']) > 3) {
			$catnm = '';
			list($catnm) = $db->fetch("select catnm from gd_category where category = ".substr($categoryList[$i]['category'],0,3));
			$categoryList[$i]['catnm'] = $catnm.' > '.$categoryList[$i]['catnm'];
		}
		$category[] = implode(',',$categoryList[$i]);
	}
	echo gd_json_encode($category);
}
?>