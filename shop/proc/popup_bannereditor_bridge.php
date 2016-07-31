<?php
include "../lib/library.php";

if($cfg['tplSkinWork']){
	$workskin = $cfg['tplSkinWork'];
} else {
	$workskin = $cfg['tplSkin'];
}
$bannersno		= $_POST['bannersno'];
$imgname		= $_POST['imgname'];
if($bannersno){
	$mode = "modify";
} else {
	$mode = "register";
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	</head>
	<body>
		
	<form name="logobannerform" method="post">
		<input type="hidden" name="godoimg">
	</form>

	<script type="text/javascript">
	function logobannerlink(){
		var mode = '<?=$mode?>';
		var actionurl = '';
		if(mode == 'register'){
			actionurl = '../admin/design/design_banner_register.php?returnUrl=popup.banner.php';
		} else if(mode == 'modify'){
			actionurl = '../admin/design/design_banner_register.php?returnUrl=popup.banner.php&mode=modify&sno=<?=$bannersno;?>';
		}
		document.logobannerform.action = actionurl;
		document.logobannerform.target = 'logobanner';
		document.logobannerform.godoimg.value = '<?=$imgname;?>';
		document.logobannerform.submit();
	}
	logobannerlink();
	</script>
	</body>
</html>