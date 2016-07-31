<?php
@require "../lib.php";

unset($_POST['x'],$_POST['y']);

$qfile = & load_class('qfile','qfile');
$todayShop = &load_class('todayshop', 'todayshop');
$tsCfg = $todayShop->cfg;


function _stripslashes(&$var) {

	if (get_magic_quotes_gpc()) {

		if(is_array($var))
			array_walk($var, '_stripslashes');
		else
			$var = stripslashes($var);

	}

}	//

_stripslashes($_POST);





// 아래에서 mysql_real_escape_string 등으로 특수문자를 escape 하면 안됨.
foreach($_POST as $k=>$v)
{

	if (preg_match('/^mailMsg/',$k)) {

		$_path = "../../conf/email/{$k}.php";

		// 템플릿 파일로 저장
		$body = str_replace("cart-&gt;","cart->",$v);
		$qfile->open($_path);
		$qfile->write($body);
		$qfile->close();

		$v = array_pop(explode("/",$_path));	//
	}

	$tsCfg[$k] = ($v);

}

if (!isset($tsCfg['sortOrder'])) $tsCfg['sortOrder'] = 'open';

$todayShop->saveConfig($tsCfg);

msg('설정이 저장되었습니다.');
go($_SERVER['HTTP_REFERER']);
?>