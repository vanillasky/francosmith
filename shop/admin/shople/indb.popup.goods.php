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

		### 상점상품에 사용되는 이미지 삭제방지
		$imgs = explode("|",$data['img_m']);

		foreach ($_FILES['imgs']['name'] as $k => $v)
		{
			if ($_POST['del']['imgs'][$k] && preg_match('/^openmarket_/', $imgs[$k]) == 0){
				$imgs[$k] = '';
				unset($_POST['del']['imgs'][$k]);
			}
		}

		 $data['imgs'] = implode("|",$imgs);

		### 이미지 업로드 & 업로드한 이미지명 변경
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

	### 필수옵션 저장
	$optnm = @implode("|",array_notnull($_POST['optnm']));
	$stock = 0;
	$idx = -1;$link[0] = 1;
	$db->query("delete from ".GD_SHOPLE_GOODS_OPTION." where goodsno=$goodsno");
	$cnt = count($_POST['opt2']);
	foreach ($_POST['option']['stock'] as $k=>$v) {
		$idx++;
		$key = (int)($idx/$cnt);
		$opt1 = str_replace("'","’",$_POST['opt1'][$key]);
		$opt2 = str_replace("'","’",$_POST['opt2'][$idx%$cnt]);

		$price = trim(str_replace(",","",$_POST[option][price][$key]));
		$consumer = trim(str_replace(",","",$_POST[option][consumer][$key]));

		$stock = $stock + $v;	// 총 재고량

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


		// 11번가 상품 업데이트 옵션 데이터
		$_option = array(
			'price'=>$price,
			'consumer'=>$consumer,
			'name'=>$opt1.($opt2 ? ' / '.$opt2 : ''),
			'stock'=>$v,
		);

		$data['options'][] = array_map("htmlspecialchars",$_option);

		// 가격, 재고, 정가
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
	msg('수정되었습니다.','close','top');
}
?>
