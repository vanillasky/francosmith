<?php
include_once(dirname(__FILE__) . '/../../../lib/library.php');
include_once(dirname(__FILE__) . '/../../../conf/payco.cfg.php');

$responseData = array();
if(!is_object($cart))		$cart		= Core::loader('cart', $_COOKIE['gd_isDirect']);
if(!is_object($paycoApi))	$paycoApi	= Core::loader('paycoApi');
if(!is_object($payco))		$payco		= Core::loader('payco');
$_POST['paycoType'] = ($_POST['paycoType'] == 'CHECKOUT') ? $_POST['paycoType'] : 'EASYPAY';
$_POST['isMobile'] = ($_GET['isMobile']) ? $_GET['isMobile'] : $_POST['isMobile'];
$imgPath = $cfg['rootDir'] . '/order/card/payco/img/';
?>
<html>
<head>
	<style>
	body				{ margin: 0px; padding: 0px; overflow: hidden;}
	.layout				{ width: 100%; height: 600px; text-align: center; }
	.layoutTop			{ width: 100%; text-align: left; padding: 20px 0px 20px 20px; }
	.layoutSolid		{ width: 100%; height:4px; background-color: #ff0008; }
	.progressImage		{ width: 100%; text-align: center; margin-top: 150px;}
	.progressImageSub1	{ margin-top: 39px;}
	.progressImageSub2	{ margin-top: 33px;}
	</style>
</head>
<body>
	<div class="layout">
		<div class="layoutTop"><img src="<?php echo $imgPath; ?>payco_logo.gif"></div>
		<div class="layoutSolid"></div>
		<div class="progressImage">
			<div><img src="<?php echo $imgPath; ?>payco_img.gif"></div>
			<div class="progressImageSub1"><img src="<?php echo $imgPath; ?>payco_icon_loading.gif"></div>
			<div class="progressImageSub2"><img src="<?php echo $imgPath; ?>payco_text_loading.gif"></div>
		</div>
	</div>
</body>
</html>
<?php
//cart 상품 체크
if(!$cart->item){
	$payco->msgLocate("상품정보를 찾을 수 없습니다.", $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
	exit;
}

//주문가능여부 체크
foreach($cart->item as $data){
	$errorMsg = $payco->check_paycoOrderAble($_POST['paycoType'], $data['goodsno'], $_POST['isMobile']);
	if($errorMsg){
		$payco->msgLocate($errorMsg, $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
		break;
	}
}

$responseData = $payco->apiExecute('reserve');

if($responseData['code'] != '000'){
	if($responseData['msg']) {
		$errorMsg = iconv("utf-8", "euc-kr", $responseData['msg']);
	}
	else {
		$errorMsg = "통신이 정상적이지 않습니다. 잠시후에 다시 시도하여 주세요.";
	}
	$payco->msgLocate($errorMsg, $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
	exit;
}

if($responseData['data']['orderSheetUrl']){
	//페이코 주문번호 저장
	$payco->saveReserveOrderNo($_POST['ordno'], $responseData['data']['orderSheetUrl']);

	//팝업오픈
	$payco->locateSettlePopupPage($responseData['data']['orderSheetUrl'], $_POST['isMobile'], 'card_gate');
}
else {
	$payco->msgLocate("통신이 정상적이지 않습니다. 잠시후에 다시 시도하여 주세요.", $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
	exit;
}
?>