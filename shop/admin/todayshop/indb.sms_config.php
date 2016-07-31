<?php
@require "../lib.php";

$todayShop = &load_class('todayshop', 'todayshop');
$tsCfg = $todayShop->cfg;

// sms 사용여부 체크 (checkbox 는 체크되지 않으면 전송되지 않음)
foreach (array('orderc','salec','giftc','orderg','deliveryg','cancel') as $k) {
	$_key = 'smsUse_'.$k;
	$_POST[$_key] = isset($_POST[$_key]) ? $_POST[$_key] : '';
}

unset($_POST['x'],$_POST['y']);
foreach($_POST as $k=>$v)
{
	if(is_array($v)):
		foreach ($v as $k1=>$v1) $tsCfg[$k][] = addslashes($v1);
	else:
		$tsCfg[$k] = addslashes($v);
	endif;
}

if (!isset($tsCfg['sortOrder'])) $tsCfg['sortOrder'] = 'open';

$todayShop->saveConfig($tsCfg);

msg('설정이 저장되었습니다.');
?>