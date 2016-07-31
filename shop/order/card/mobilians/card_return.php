<?php

// card_notice.php파일 내부에서 이나무가 호출한것임을 알려주기 위해 isEnamoo 셋팅
$isEnamoo = true;
$noticeResult = include dirname(__FILE__).'/card_notice.php';
unset($isEnamoo);

$ordno = $_POST['Tradeid'];
$shopConfig = Core::loader('config')->_load_config();

// 모바일샵의 결과페이지
if (isset($_GET['isMobile'])) {
	// 모바일샵 설정이 로드되어있지 않으면 설정파일 포함
	if (isset($cfgMobileShop) === false) {
		include dirname(__FILE__).'/../../../conf/config.mobileShop.php';
	}
	$okurl = $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php?ordno='.$ordno;
	$failurl = $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$ordno;
}
// 일반 결과페이지
else {
	$okurl = $shopConfig['rootDir'].'/order/order_end.php?ordno='.$ordno;
	$failurl = $shopConfig['rootDir'].'/order/order_fail.php?ordno='.$ordno;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_CFG['global']['charset']; ?>" />
		<title>결제완료 | <?php echo $shopConfig['shopName']; ?></title>
	</head>
	<body>
		<script type="text/javascript" charset="<?php echo $_CFG['global']['charset']; ?>">
			var
			result = "<?php echo $noticeResult; ?>",
			isMobile = <?php echo isset($_GET['isMobile']) ? 'true' : 'false'; ?>,
			resultUrl = "<?php echo $okurl; ?>";

			if (result !== "SUCCESS") alert(result);

			if (isMobile) {
				location.href = resultUrl;
			}
			else {
				opener.parent.location.href = resultUrl;
				window.close();
			}
		</script>
	</body>
</html>