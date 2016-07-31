<?php
include dirname(__FILE__) . '/../../lib/library.php';

if($_SERVER['REMOTE_ADDR'] != '211.233.51.165' && $_SERVER['REMOTE_ADDR'] != '211.233.51.166' && $_SERVER['REMOTE_ADDR'] != '211.233.51.250') exit;
if(!$_POST['sno']) exit;
if(!$godo['sno']){
	if(!$config){
		$config = Core::loader('config');
	}
	if($config){
		$godo = $config->load('godo');
	}
}

//페이코 > 쇼핑몰 인증
if($godo['sno'] == $_POST['sno']) {
	exit('ok');
}
else {
	exit('false');
}
?>