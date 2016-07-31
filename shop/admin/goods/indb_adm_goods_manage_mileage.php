<?
include "../lib.php";

### ����Ÿ ��ȿ�� �˻�
if (in_array($_POST['method'], array('direct', 'price')) === false) break;
if ($_POST['method'] == 'direct' && (is_numeric($_POST['reserve']) === false || $_POST['reserve'] < 0)) break;
if ($_POST['method'] == 'price' && in_array($_POST['roundunit'], array(1, 10, 100, 1000)) === false) break;
if ($_POST['method'] == 'price' && in_array($_POST['roundtype'], array('down', 'halfup', 'up')) === false) break;

### ������ �籸��(�˻���� ��ü)
if ($_POST['isall'] == 'Y' && $_POST['query']){
	$_POST['chk'] = array();
	$res = $db->query(base64_decode($_POST['query']));
	while ($data=$db->fetch($res)) $_POST['chk'][] = $data['goodsno'];
}

### ���ϸ���(������) �ϰ�����
$goods = Clib_Application::getModelClass('goods');

foreach ($_POST['chk'] as $goodsno){

	$goods->resetData();
	$goods->load($goodsno);

	if ($_POST['method'] == 'direct') {
		$goods_reserve = $reserve = $_POST['reserve'];

		// Ư�� �ݾ��϶�
		$goods->setData('use_emoney', 1);
		$goods->setData('goods_reserve', $reserve);

		foreach($goods->getOptions() as $option) {
			$option->setData('reserve',$reserve);
			daum_goods_diff($goods['goodsno'],$option);	// ���� ��� EP
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
			daum_goods_diff($goods['goodsno'],$option);	// ���� ��� EP
			$option->save();

			if ($option['link']) {
				$goods_reserve = $reserve;
			}

		}

	}

	// �ǸŰ��� ���� �����϶�
	$goods->setData('use_emoney', 1);
	$goods->setData('goods_reserve', $goods_reserve);
	$goods->save();

	$goodsnos[] = $goodsno;

}

### ������Ʈ �Ͻ�
$Goods = new Goods();
foreach($goodsnos as $v){
	$Goods -> update_date($v);
}

echo '
<script>
alert("����Ǿ����ϴ�.");
parent.location.reload();
</script>
';
