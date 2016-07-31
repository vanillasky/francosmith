<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$goodsno = $_POST['goodsno'];
$mode = $_POST['mode'];
$shople = Core::loader('shople');
$data = $shople->getGoods($goodsno);

if ($mode == 'image') {

	$img_m = array();
	if ($_POST['image_attach_method'] == 'url') {
		// urls

		$data['img_m'] = @implode("|",$_POST['urls']);
	}
	else {
		// imgs

		### ������ǰ�� ���Ǵ� �̹��� ��������
		$imgs = explode("|",$data['img_m']);

		foreach ($_FILES['imgs']['name'] as $k => $v)
		{
			if ($_POST['del']['imgs'][$k] && preg_match('/^openmarket_/', $imgs[$k]) == 0){
				$imgs[$k] = '';
				unset($_POST['del']['imgs'][$k]);
			}
		}

		 $data['imgs'] = implode("|",$imgs);

		### �̹��� ���ε� & ���ε��� �̹����� ����
		multiUpload("imgs");

		foreach ($_FILES['imgs']['name'] as $k => $v){

			if ($_FILES['imgs']['name'][$k] && $_FILES['imgs']['name'][$k] != $imgs[$k])
			{

				$oldnm = $_FILES['imgs']['name'][$k];
				$_FILES['imgs']['name'][$k] = 'openmarket_'. $oldnm;
				$_dir	= "../../data/goods/";
				@rename($_dir.$oldnm, $_dir.$_FILES['imgs']['name'][$k]);
			}
			else {
				$_FILES['imgs']['name'][$k] = $imgs[$k];
			}

		}

		$data['img_m'] = @implode("|",$_FILES['imgs']['name']);
	}

	$data[image_changed] = true;

}
elseif($mode == 'option') {

	$data['options'] = array();

	### �ʼ��ɼ� ����
	$optnm = @implode("|",array_notnull($_POST['optnm']));
	$stock = 0;
	$idx = -1;$link[0] = 1;
	$db->query("delete from ".GD_SHOPLE_GOODS_OPTION." where goodsno=$goodsno");
	$cnt = count($_POST['opt2']);
	foreach ($_POST['option']['stock'] as $k=>$v) {
		$idx++;
		$key = (int)($idx/$cnt);
		$opt1 = str_replace("'","��",$_POST['opt1'][$key]);
		$opt2 = str_replace("'","��",$_POST['opt2'][$idx%$cnt]);

		$price = trim(str_replace(",","",$_POST[option][price][$key]));
		$consumer = trim(str_replace(",","",$_POST[option][consumer][$key]));

		$stock = $stock + $v;	// �� ���

		$query = "
		insert into ".GD_SHOPLE_GOODS_OPTION." set
			goodsno = '$goodsno',
			opt1	= '$opt1',
			opt2	= '$opt2',
			price	= '$price',
			consumer= '$consumer',
			stock	= '$v',
			link	= '$link[$idx]',
			sort	= '$idx'
		";
		$db->query($query);


		// 11���� ��ǰ ������Ʈ �ɼ� ������
		$_option = array(
			'price'=>$price,
			'consumer'=>$consumer,
			'name'=>$opt1.($opt2 ? ' / '.$opt2 : ''),
			'stock'=>$v,
		);

		$data['options'][] = array_map("htmlspecialchars",$_option);

		// ����, ���, ����
		if ($link[$idx] == 1) {
			$data['price'] = $price;
			$data['consumer'] = $consumer;
		}

	}


	$data[stock] = $stock;
	$data[optnm] = $optnm;
	$data[option_changed] = true;

}
elseif($mode == 'descript') {

	$data['longdesc'] = $_POST['longdesc'];

}
else {
	exit;
}


$shople->setGoods($data);

$method = 'EDIT_PRODUCT';
$param = array(
	'prdNo' => $data['is11st']
);

$rs = $shople->request($method,$param,$data);

if ($rs['result'] == false) {

	msg( addslashes($rs['body']) ,-1);
}
else {
	msg('�����Ǿ����ϴ�.','close','top');
}
?>
