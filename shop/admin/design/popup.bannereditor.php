<?php
include "../../conf/config.php";
include "../lib.php";
include "../lib.skin.php";
?>
<!DOCTYPE html>
<head>
<title>'Godo Shoppingmall e���� Season4 �����ڸ��'</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="format-detection" content="telephone=no" />
</head>
<body class="scroll" >
<?php
$bannersno = $_GET['bannerkey'];
$bannereditor = Core::loader('bannereditor');
echo $bannereditor->getCurlData($bannersno);
include "../_footer.popup.php";
exit;
?>