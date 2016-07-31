<?
include "../lib.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

require_once("../../lib/qfile.class.php");
$qfile = new qfile();

switch ($_POST[mode]){

	case "auctionos":

		$partner = array();
		@include "../../conf/auctionos.php";

		$_POST['partner']['useYn'] = $_POST['useYn'];

		$partner = array_map("addslashes",array_map("stripslashes",$partner));
		$partner = array_merge($partner,$_POST[partner]);

		$qfile->open("../../conf/auctionos.php");
		$qfile->write("<? \n");
		$qfile->write("\$partner = array( \n");
		foreach ($partner as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;

	case "aboutcoupon":

		$config->save('aboutcoupon',array(
			'use_aboutcoupon'=>$_POST[use_aboutcoupon],
			'use_test'=>$_POST[use_test],
			'left_loc'=>$_POST[left_loc],
			'top_loc'=>$_POST[top_loc],
			'startdate'=>$_POST[regdt][0],
			'enddate'=>$_POST[regdt][1]

		));

		## 설정 변경로그 저장
		$query = "INSERT INTO gd_log_aboutcoupon SET ";
		$query.= " use_aboutcoupon='".$_POST['use_aboutcoupon']."' ";
		$query.= " , use_test='".$_POST['use_test']."'";
		$query.= " , m_id='".$sess['m_id']."'";
		$query.= " , regdt=now() ";
		$db->query($query);
		break;
}

go($_SERVER[HTTP_REFERER]);

?>