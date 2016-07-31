<?
include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$_POST['msg'] = isset($_POST['msg']) ? trim($_POST['msg']) : '';
$_POST['short_name'] = isset($_POST['short_name']) && $_POST['short_name'] == 1 ? true : false;
unset($_POST['x'],$_POST['y'],$_POST['stockedSMSLen']);

if (is_file("../../conf/config.stocked_noti.php")) {
	include "../../conf/config.stocked_noti.php";
	$stocked_noti_cfg = (array)$stocked_noti_cfg;
}
else {
	$stocked_noti_cfg = array();
}
$stocked_noti_cfg['shortGoodsNm'] = "n";
$stocked_noti_cfg = array_map("addslashes",$stocked_noti_cfg);
$stocked_noti_cfg = array_merge($stocked_noti_cfg,(array)$_POST);

$qfile->open("../../conf/config.stocked_noti.php");
$qfile->write("<? \n");
$qfile->write("\$stocked_noti_cfg = array( \n");
foreach ($stocked_noti_cfg as $k=>$v) {
	if (is_bool($v))
		$qfile->write("'$k' => ".($v ? 'true' : 'false').", \n");
	else
		$qfile->write("'$k' => '$v', \n");

}
$qfile->write("); \n");
$qfile->write("?>");
$qfile->close();

msg('설정이 저장되었습니다.');
?>