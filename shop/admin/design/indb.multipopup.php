<?php
/**
 * 멀티 팝업 등록 처리
 * @author cjb3333 , artherot @ godosoft development team.
 */

include "../lib.php";

// 멀티 팝업 Class
$multipopup	= Core::loader('MultiPopup');

// 처리 모드
if ($_POST['mode']) {
	$mode	= $_POST['mode'];
	unset($_POST['mode'], $_POST['x'], $_POST['y']);
} else {
	$mode	= $_GET['mode'];
	unset($_GET['mode']);
}

switch ($mode)
{
	// 멀티 팝업 등록
	case "popupRegister":

		// 등록 처리
		$multipopup->popupRegister($_POST['code']);

		// 등록 이력 기록
		$mpl = Core::loader('MultiPopupLog');
		$mpl->sendInsertLog($_POST['code'], $_POST['text']);

		go($_SERVER['HTTP_REFERER']."&code=".$_POST['code']);
		break;

	// 멀티 팝업 수정
	case "popupModifiy":

		// 수정 처리
		$multipopup->popupModifiy($_POST['code']);
		break;

	// 멀티 팝업 복사
	case "copyPopup":

		// 복사 처리
		$multipopup->popupCopy($_GET['code']);
		break;

	// 멀티 팝업 삭제
	case "delPopup":

		// 삭제 처리
		$multipopup->popupDelete($_GET['code']);
		break;

	// 멀티 팝업 이미지 등록
	case "upload":

		// 이미지 등록 처리
		$multipopup->imgUploadTemp();
		exit();
		break;

	default:
		break;

}

go($_SERVER['HTTP_REFERER']);
?>