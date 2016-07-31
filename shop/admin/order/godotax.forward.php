<?php
include "../lib.php";

$godotax = Core::loader('godotax');
$config_godotax = $config->load('godotax');

if(!$config_godotax['site_id'] || !$config_godotax['api_key']) {
	msg("고도전자세금계산서 사이트에 대한 ID와 API_KEY를 설정해주셔아합니다",'../order/godotax.setting.php');
	exit;
}

?>
<script type="text/javascript">
alert("고도빌로 이동합니다");
window.open('<?=$godotax->getLinkList()?>','_blank','toolbar=1,  status=1, menubar=1 , scrollbars=1, resizable=1');
history.go(-1);
</script>
