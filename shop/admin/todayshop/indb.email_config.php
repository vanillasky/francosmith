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





// �Ʒ����� mysql_real_escape_string ������ Ư�����ڸ� escape �ϸ� �ȵ�.
foreach($_POST as $k=>$v)
{

	if (preg_match('/^mailMsg/',$k)) {

		$_path = "../../conf/email/{$k}.php";

		// ���ø� ���Ϸ� ����
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

msg('������ ����Ǿ����ϴ�.');
go($_SERVER['HTTP_REFERER']);
?>