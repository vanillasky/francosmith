<?php

include "../lib/library.php";
include "../lib/naverNcash.class.php";

$naverNcash = Core::loader('naverNcash');

?>
<html>
	<head>
		<script type="text/javascript">
		window.onload = function()
		{
			var mileageInfo = "<?php echo $_COOKIE['Ncisy']; ?>";
			try{ if(opener.wcs) mileageInfo = opener.wcs.getMileageInfo(); }catch(e){}
			if(opener.document.getElementById('mileageUseAmount<?=$naverNcash->api_id;?>'))
			{
				location.href = "<?php echo $naverNcash->cash_save_use($_GET['reqTxId'], $_GET['maxUseAmount']); ?>";
			}
			else
			{
				location.href = ("<?php echo $naverNcash->cash_save_use($_GET['reqTxId'], $_GET['maxUseAmount']); ?>").replace(/service\/v2\/accumulation/,"service/accumulation").replace(/\&maxUseAmount\=\&/,"&");
			}
		}
		</script>
	</head>
	<body>
	</body>
</html>