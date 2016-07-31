<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

 
		// 상품가격 설정
		$_POST['criteo']['wi_code1'] =  $_POST['wi_code1'] ;
		$_POST['criteo']['wi_code2'] =  $_POST['wi_code2']  ;
		$_POST['criteo']['p_code'] = $_POST['p_code'];
		 
		// 저장
		$criteo = array();
		@include "../../conf/criteo.cfg.php";

		$criteo = array_map("addslashes",array_map("stripslashes",$criteo));
		$criteo = array_merge($criteo,$_POST[criteo]);

		$qfile->open("../../conf/criteo.cfg.php");
		$qfile->write("<? \n");
		$qfile->write("\$criteo = array( \n");
		foreach ($criteo as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
 
		@chmod("../../conf/criteo.cfg.php",0707);
go($_SERVER[HTTP_REFERER]);

?>