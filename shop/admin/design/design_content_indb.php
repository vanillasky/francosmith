<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "modify":

		include_once dirname(__FILE__) . "/../../conf/config.php";
		include_once dirname(__FILE__) . "/../lib.skin.php";

		{ // 디자인코디파일 저장

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin/" . $cfg['tplSkinWork'] . "/" . $_GET['design_file']);
			$qfile->write(stripslashes( $_POST['content'] ) );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		break;
}

go($_SERVER[HTTP_REFERER] . '&' . time());

?>