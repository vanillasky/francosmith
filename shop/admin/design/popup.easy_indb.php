<?

include "../lib.php";
require_once("../../lib/upload.lib.php");

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "modify":

		### 스킨 및 이미지파일 정의
		$easySkin = "../../data/skin/easy";
		$easyImg = $easySkin . $_POST[imgpath];


		### 경고 체크
		$errMsg = array();
		if ( !file_exists( $easySkin ) ) $errMsg[] = 'easy 스킨이 존재하지 않습니다.';
		if ( !file_exists( $easyImg ) || empty( $_POST[imgpath] ) ) $errMsg[] = basename( $_POST[imgpath] ) . ' 파일이 스킨내에 존재하지 않습니다.';
		if ( count( $errMsg ) ) msg(implode( "\\n", $errMsg ),$code=-1); // 경고메시지


		### 이미지교체
		if ( $_FILES['userfile']['name'] ){
			$upload = new upload_file($_FILES['userfile'],$easyImg,'image');
			if(!$upload -> upload()) msg('파일이 올바르지 않습니다.',-1);
		}

		break;
}

go($_SERVER[HTTP_REFERER]);

?>