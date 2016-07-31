<?
define('SHOPROOT', realpath(dirname(__FILE__)));
include(dirname(__FILE__).'/lib/GODO/init.php');
include(dirname(__FILE__).'/conf/db.conf.php'); // db 접속 정보

$db= Core::loader('GODO_DB');
$db->driver('godomysql');
$db->addServer($db_host,$db_user,$db_pass,$db_name,$_CFG['global']['charset']);
unset($db_host,$db_user,$db_pass,$db_name);
?>
