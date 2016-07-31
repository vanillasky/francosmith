<?php
include dirname(__FILE__) . '/../../../lib/library.php';
@include dirname(__FILE__) . '/../../../conf/config.mobileShop.php';
$payco = Core::loader('payco');

if($_GET['isMobile'] == 'Y'){
	$orderDir = '../../../..' . $cfgMobileShop['mobileShopRootDir'] . '/ord/';
}
else {
	$orderDir = '../../';
}

if($payco->screenType == 'MOBILE' || $_GET['isMobile'] == 'Y'){
	$endLocateType = 'self';
}
else {
	$endLocateType = 'parent';
}

if((int)$_GET['code'] == 0){
	//성공
	$orderFinalUrl = $orderDir . 'order_end.php?ordno=' . $_GET['ordno'];
}
else {
	//실패
	$orderFinalUrl = $orderDir . 'order_fail.php?ordno=' . $_GET['ordno'];
}
?>
<html>
<head>
	<style>
	body				{ margin: 0px; padding: 0px; overflow: hidden;}
	.layout				{ width: 100%; height: 600px; text-align: center; }
	.layoutTop			{ width: 100%; text-align: left; padding: 20px 0px 20px 20px; }
	.layoutSolid		{ width: 100%; border: 2px #ff0008 solid; }
	.progressImage		{ width: 100%; text-align: center; margin-top: 150px;}
	.progressImageSub1	{ margin-top: 39px;}
	.progressImageSub2	{ margin-top: 33px;}
	</style>
</head>
<body>
	<form name="paycoCardGateForm" method="get" action="<?php echo $orderFinalUrl; ?>">
		<input type="hidden" name="ordno" value="<?php echo $_GET['ordno']; ?>">
	</form>
	<div class="layout">
		<div class="layoutTop"><img src="./img/payco_logo.gif"></div>
		<div class="layoutSolid"></div>
		<div class="progressImage">
			<div><img src="./img/payco_img.gif"></div>
			<div class="progressImageSub1"><img src="./img/payco_icon_loading.gif"></div>
			<div class="progressImageSub2"><img src="./img/payco_text_loading.gif"></div>
		</div>
	</div>

	<?php if($endLocateType == 'self'){ ?>
		<script type="text/javascript">
			var orderFinalUrl = '<?php echo $orderFinalUrl; ?>';

			window.location.href = orderFinalUrl;
		</script>
	<?php } else { ?>
		<script type="text/javascript">
			var orderFinalUrl = '<?php echo $orderFinalUrl; ?>';

			window.domain = '<?php echo $_SERVER[HTTP_HOST]; ?>';
			if(opener){
				opener.window.location.href = orderFinalUrl;
			}
			else {
				var f = document.paycoCardGateForm;
				if(f){
					f.target = 'payco_parent';
					f.submit();
				}
				else {
					window.open(orderFinalUrl, 'payco_parent', '');
				}
			}
			window.open('', '_self', '');
			window.close();
		</script>
	<?php } ?>
</body>
</html>