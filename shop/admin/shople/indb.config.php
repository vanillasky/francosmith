<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

unset($_POST['x'],$_POST['y']);

// stripslashes �ϴ� ������ db Ŭ������ _query_print �Լ��� ���� �˰� ��.
foreach($_POST as $k=>$v) {

	if(is_array($v)) {
		foreach ($v as $k1=>$v1) $shopleCfg[$k][$k1] = $v1;
	}
	else {
		$shopleCfg[$k] = $v;
	}
}

$shople->saveConfig($shopleCfg);

msg('������ ����Ǿ����ϴ�.');
?>
