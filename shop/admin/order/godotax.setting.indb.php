<?php
include "../lib.php";
require_once("../../lib/qfile.class.php");
$godotax = Core::loader('godotax');

$site_id = (string)$_POST['godotax_site_id'];
$api_key = (string)$_POST['godotax_api_key'];


if($godotax->check_connection($site_id,$api_key)==false) {
	msg("ȸ�� ID�� API_KEY�� �ٽ� Ȯ�����ּ���");
	exit;
}




$config_pay = $config->load('configpay');
$config_pay['tax']['useyn']=(string)$_POST['useyn'];
$config_pay['tax']['use_a']=(string)$_POST['use_a'];
$config_pay['tax']['use_o']=(string)$_POST['use_o'];
$config_pay['tax']['use_v']=(string)$_POST['use_v'];
$config_pay['tax']['step']=(string)$_POST['step'];
$config_pay = array_map('strip_slashes',$config_pay);
$config_pay = array_map('add_slashes',$config_pay);
$qfile = new qfile();
$qfile->open("../../conf/config.pay.php");
$qfile->write("<? \n");
foreach ($config_pay as $k=>$v) {
	foreach ($v as $k2=>$v2) {
		$qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
	}
}
$qfile->write("?>");
$qfile->close();


$config_godotax = array(
	'site_id'=>$site_id,
	'api_key'=>$api_key,
);
$config_pay = $config->save('godotax',$config_godotax);

msg("����Ǿ����ϴ�");

?>
