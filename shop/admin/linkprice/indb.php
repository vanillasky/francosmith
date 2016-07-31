<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

switch ($_POST[mode]){

	case "merchant":
		$qfile->open("../../conf/merchant.php");
		$qfile->write("<? \n");
		$qfile->write("\$linkprice = array( \n");
		foreach ($_POST[linkprice] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();

		break;
}

go($_SERVER[HTTP_REFERER]);

?>