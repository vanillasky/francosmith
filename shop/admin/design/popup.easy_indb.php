<?

include "../lib.php";
require_once("../../lib/upload.lib.php");

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "modify":

		### ��Ų �� �̹������� ����
		$easySkin = "../../data/skin/easy";
		$easyImg = $easySkin . $_POST[imgpath];


		### ��� üũ
		$errMsg = array();
		if ( !file_exists( $easySkin ) ) $errMsg[] = 'easy ��Ų�� �������� �ʽ��ϴ�.';
		if ( !file_exists( $easyImg ) || empty( $_POST[imgpath] ) ) $errMsg[] = basename( $_POST[imgpath] ) . ' ������ ��Ų���� �������� �ʽ��ϴ�.';
		if ( count( $errMsg ) ) msg(implode( "\\n", $errMsg ),$code=-1); // ���޽���


		### �̹�����ü
		if ( $_FILES['userfile']['name'] ){
			$upload = new upload_file($_FILES['userfile'],$easyImg,'image');
			if(!$upload -> upload()) msg('������ �ùٸ��� �ʽ��ϴ�.',-1);
		}

		break;
}

go($_SERVER[HTTP_REFERER]);

?>