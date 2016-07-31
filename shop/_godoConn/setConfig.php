<?
require_once(dirname(__FILE__)."/../lib/qfile.class.php");
$qfile = new qfile();

## conf/config.php ssl 정보저장
if($_GET[mode] == 'ssl'){
	@include "../conf/config.php";

	$arr = array('ssl' => 1,
		'ssl_type'=>'godo',
		'ssl_sdate' => $_GET[ssl_sdate],
		'ssl_edate' => $_GET[ssl_edate],
		'ssl_domain' => $_GET[ssl_domain],
		'ssl_port' => $_GET[ssl_port],
		'ssl_step'=>'',
	);
	$cfg = array_map("stripslashes",$cfg);
	$cfg = array_map("addslashes",$cfg);
	$cfg = array_merge($cfg,$arr);

	$qfile->open("../conf/config.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfg = array( \n");
	foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
}
else if($_GET[mode] == 'ssl_wait') {
	@include "../conf/config.php";

	$arr = array('ssl' => 1,
		'ssl_step'=>'wait',
	);
	$cfg = array_map("stripslashes",$cfg);
	$cfg = array_map("addslashes",$cfg);
	$cfg = array_merge($cfg,$arr);

	$qfile->open("../conf/config.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfg = array( \n");
	foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
}
else if($_GET[mode] == 'ssl_process') {
	@include "../conf/config.php";

	$arr = array('ssl' => 1,
		'ssl_step'=>'ssl_process',
	);
	$cfg = array_map("stripslashes",$cfg);
	$cfg = array_map("addslashes",$cfg);
	$cfg = array_merge($cfg,$arr);

	$qfile->open("../conf/config.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfg = array( \n");
	foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
}
?>