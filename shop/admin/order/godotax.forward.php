<?php
include "../lib.php";

$godotax = Core::loader('godotax');
$config_godotax = $config->load('godotax');

if(!$config_godotax['site_id'] || !$config_godotax['api_key']) {
	msg("�����ڼ��ݰ�꼭 ����Ʈ�� ���� ID�� API_KEY�� �������ּž��մϴ�",'../order/godotax.setting.php');
	exit;
}

?>
<script type="text/javascript">
alert("������ �̵��մϴ�");
window.open('<?=$godotax->getLinkList()?>','_blank','toolbar=1,  status=1, menubar=1 , scrollbars=1, resizable=1');
history.go(-1);
</script>
