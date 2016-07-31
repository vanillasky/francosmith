<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];


switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_LOG_HACK." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "modify":

		### 데이타 수정
		$query = "
		update ".GD_LOG_HACK." set
			reason		= '$_POST[reason]',
			adminMemo	= '$_POST[adminMemo]'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);
		echo "<script>parent.location.reload();</script>";
		exit;
		break;
}

go($_POST[returnUrl]);

?>