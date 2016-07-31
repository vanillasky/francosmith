<?php
@require "../lib.php";
require_once("../../lib/todayshop_cache.class.php");

$todayShop = &load_class('todayshop', 'todayshop');
$tsCfg = $todayShop->cfg;
$tsPG = array();

unset($_POST['x'],$_POST['y']);

// ��������
if (isset($_POST['subscribe'])) {
	$_POST['subscribe'] = serialize($_POST['subscribe']);
}

// ��������
if (isset($_POST['interest'])) {
	$_POST['interest'] = serialize($_POST['interest']);
}

if ($_POST['useTodayShop'] == 'n') {
	$_POST['shopMode'] = 'regular';
}
foreach($_POST as $k=>$v)
{
	if(is_array($v)):
		foreach ($v as $k1=>$v1) $tsCfg[$k][$k1] = addslashes($v1);
	else:
		$tsCfg[$k] = addslashes($v);
	endif;
}

if (!isset($tsCfg['sortOrder'])) $tsCfg['sortOrder'] = 'open';
$todayShop->saveConfig($tsCfg);

// ĳ�� ����
todayshop_cache::truncate();

msg('������ ����Ǿ����ϴ�.');
?>