<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$goodsno = $_POST['goodsno'];
$shople = Core::loader('shople');
$data = $shople->getGoods($goodsno);

### 필수데이터 검증
$vData = $_POST;
if ($_POST['image_attach_method'] == 'url') {

	$vData['imgs'] = @implode("|",$_POST['urls']);

}
else {
	$imgs = explode("|",$data['img_m']);
	foreach ($_FILES['imgs']['name'] as $k => $v) {
		if ($_FILES['imgs']['name'][$k] && !isset($vData['imgs'])) $vData['imgs'] = $_FILES['imgs']['name'][$k];
		else if ($_POST['del']['imgs'][$k]);
		else if ($imgs[$k] && !isset($vData['imgs'])) $vData['imgs'] = $imgs[$k];
	}

}

if (($needs = $shople->verifyData($vData)) !== false) {
	msg("다음과 같이 필수데이터는 입력해야 합니다.\\n\\n--------------- 체크항목 ---------------\\n- ". implode("\\n- ", $needs), 0);
	exit;
}

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

// 배송비 처리
switch ($_POST['delivery_type']) {
	case '1':
		$data['delivery_price'] = 0;
		break;
	case '2':
		$data['delivery_price'] = $_POST['delivery_price'];
		break;
	case '3':
		$data['delivery_price'] = $_POST['delivery_price2'];
		break;
	default :
		// 기본 정책대로

		break;
}

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
		link	= '$link[$idx]'
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
	if ($link == 1) {
		$data['price'] = $price;
		$data['consumer'] = $consumer;
	}

}

### 상품 데이타 수정

/*
$data[origin_kind] =>
$data[origin_name] =>
*/
//$data[img_m] = @implode("|",$file[imgs][name])

$data[goodscd] = $_POST['goodscd'];
$data[goodsnm] = $_POST['goodsnm'];
$data[tax] = $_POST['tax'];
$data[shortdesc] =$_POST['shortdesc'];
$data[longdesc] = $_POST['longdesc'];
$data[max_count] = $_POST['max_count'];
$data[age_flag] = $_POST['age_flag'];
$data[as_info] = $_POST['as_info'];
//$data[rtnexch_info] =
$data[delivery_type] =$_POST['delivery_type'];
$data[delivery_price] = $_POST['delivery_price'];

$data[runout] = $_POST['runout'];
$data[usestock] = $_POST['usestock'];


if ($data[full_dispno] != $_POST['category']) {
	$data[full_dispno] = $data[dispno] = $_POST['category'];
	$data[dispno_changed] = true;
}


$data[consumer] = $_POST['consumer'];

$data[price] = $_POST['price'];
$data[stock] = $stock;
$data[optnm] = $optnm;


$shople->setGoods($data);


$method = 'REGISTER_PRODUCT';
$param = '';

if ($data['is11st']) {
	$data[option_changed] = true;
	$method = 'EDIT_PRODUCT';
	$param = array(
		'prdNo' => $data['is11st']
	);
}

$rs = $shople->request($method,$param,$data);

if ($rs['result'] === true) {

	$_11stcode = $rs['body'];

	$db->query("DELETE FROM ".GD_SHOPLE_GOODS_MAP." WHERE goodsno = '$goodsno'");

	$query = "
	INSERT INTO ".GD_SHOPLE_GOODS_MAP." SET
		goodsno = '$goodsno',
		11st = '$_11stcode',
		regdt = NOW()
	";
	$db->query($query);
}
else {
	msg(addslashes($rs['body']));
	exit;
}


### 계정용량 계산
setDu('goods');

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
msg('전송되었습니다.');
?>
