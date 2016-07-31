<?
include "../lib.php";

### 데이타 유효성 검사
if (in_array($_POST['method'], array('direct', 'price')) === false) break;
if ($_POST['method'] == 'direct' && (is_numeric($_POST['reserve']) === false || $_POST['reserve'] < 0)) break;
if ($_POST['method'] == 'price' && in_array($_POST['roundunit'], array(1, 10, 100, 1000)) === false) break;
if ($_POST['method'] == 'price' && in_array($_POST['roundtype'], array('down', 'halfup', 'up')) === false) break;

### 대상범위 재구성(검색결과 전체)
if ($_POST['isall'] == 'Y' && $_POST['query']){
	$_POST['chk'] = array();
	$res = $db->query(base64_decode($_POST['query']));
	while ($data=$db->fetch($res)) $_POST['chk'][] = $data['goodsno'];
}

### 마일리지(적립금) 일괄수정
$goods = Clib_Application::getModelClass('goods');

foreach ($_POST['chk'] as $goodsno){

	$goods->resetData();
	$goods->load($goodsno);

	if ($_POST['method'] == 'direct') {
		$goods_reserve = $reserve = $_POST['reserve'];

		// 특정 금액일때
		$goods->setData('use_emoney', 1);
		$goods->setData('goods_reserve', $reserve);

		foreach($goods->getOptions() as $option) {
			$option->setData('reserve',$reserve);
			daum_goods_diff($goods['goodsno'],$option);	// 다음 요약 EP
			$option->save();
		}

	}
	else {

		foreach($goods->getOptions() as $option) {

			$reserve = $option['price'] * ($_POST['percent'] / 100);

			if ($_POST['roundtype'] == 'down') $reserve = floor($reserve / $_POST['roundunit']) * $_POST['roundunit'];
			else if ($_POST['roundtype'] == 'halfup') $reserve = round($reserve, -(strlen($_POST['roundunit']) - 1));
			else $reserve = ceil($reserve / $_POST['roundunit']) * $_POST['roundunit'];

			$option->setData('reserve',$reserve);
			daum_goods_diff($goods['goodsno'],$option);	// 다음 요약 EP
			$option->save();

			if ($option['link']) {
				$goods_reserve = $reserve;
			}

		}

	}

	// 판매가에 따른 비율일때
	$goods->setData('use_emoney', 1);
	$goods->setData('goods_reserve', $goods_reserve);
	$goods->save();

	$goodsnos[] = $goodsno;

}

### 업데이트 일시
$Goods = new Goods();
foreach($goodsnos as $v){
	$Goods -> update_date($v);
}

echo '
<script>
alert("저장되었습니다.");
parent.location.reload();
</script>
';
