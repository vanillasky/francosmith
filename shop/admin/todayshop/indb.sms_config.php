<?php
@require "../lib.php";

$todayShop = &load_class('todayshop', 'todayshop');
$tsCfg = $todayShop->cfg;

// sms ��뿩�� üũ (checkbox �� üũ���� ������ ���۵��� ����)
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

msg('������ ����Ǿ����ϴ�.');
?>