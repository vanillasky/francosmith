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
//cart ��ǰ üũ
if(!$cart->item){
	$payco->msgLocate("��ǰ������ ã�� �� �����ϴ�.", $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
	exit;
}

//�ֹ����ɿ��� üũ
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
		$errorMsg = "����� ���������� �ʽ��ϴ�. ����Ŀ� �ٽ� �õ��Ͽ� �ּ���.";
	}
	$payco->msgLocate($errorMsg, $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
	exit;
}

if($responseData['data']['orderSheetUrl']){
	//������ �ֹ���ȣ ����
	$payco->saveReserveOrderNo($_POST['ordno'], $responseData['data']['orderSheetUrl']);

	//�˾�����
	$payco->locateSettlePopupPage($responseData['data']['orderSheetUrl'], $_POST['isMobile'], 'card_gate');
}
else {
	$payco->msgLocate("����� ���������� �ʽ��ϴ�. ����Ŀ� �ٽ� �õ��Ͽ� �ּ���.", $_POST['paycoType'], $_POST['isMobile'], 'card_gate');
	exit;
}
?>