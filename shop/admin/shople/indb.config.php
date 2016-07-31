<?
include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

unset($_POST['x'],$_POST['y']);

// stripslashes 하는 이유는 db 클래스의 _query_print 함수를 보면 알게 됨.
foreach($_POST as $k=>$v) {

	if(is_array($v)) {
		foreach ($v as $k1=>$v1) $shopleCfg[$k][$k1] = $v1;
	}
	else {
		$shopleCfg[$k] = $v;
	}
}

$shople->saveConfig($shopleCfg);

msg('설정이 저장되었습니다.');
?>
