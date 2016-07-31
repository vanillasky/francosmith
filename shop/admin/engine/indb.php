<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

switch ($_POST[mode]){

	case "engine":
		$qfile->open("../../conf/engine.php");
		$qfile->write("<? \n");

		$qfile->write("\$card = array( \n");
		foreach ($_POST[card] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$omi = array( \n");
		foreach ($_POST[omi] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$enuri = array( \n");
		foreach ($_POST[enuri] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$bb = array( \n");
		foreach ($_POST[bb] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$mm = array( \n");
		foreach ($_POST[mm] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$danawa = array( \n");
		foreach ($_POST[danawa] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$naver_elec = array( \n");
		foreach ($_POST[naver_elec] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$naver_bea = array( \n");
		foreach ($_POST[naver_bea] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$naver_milk = array( \n");
		foreach ($_POST[naver_milk] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("\$yahoo = array( \n");
		foreach ($_POST[yahoo] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");

		$qfile->write("?>");
		$qfile->close();

		break;
}

go($_SERVER[HTTP_REFERER]);

?>