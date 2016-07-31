<?php
/**
 * ��Ƽ �˾� ��� ó��
 * @author cjb3333 , artherot @ godosoft development team.
 */

include "../lib.php";

// ��Ƽ �˾� Class
$multipopup	= Core::loader('MultiPopup');

// ó�� ���
if ($_POST['mode']) {
	$mode	= $_POST['mode'];
	unset($_POST['mode'], $_POST['x'], $_POST['y']);
} else {
	$mode	= $_GET['mode'];
	unset($_GET['mode']);
}

switch ($mode)
{
	// ��Ƽ �˾� ���
	case "popupRegister":

		// ��� ó��
		$multipopup->popupRegister($_POST['code']);

		// ��� �̷� ���
		$mpl = Core::loader('MultiPopupLog');
		$mpl->sendInsertLog($_POST['code'], $_POST['text']);

		go($_SERVER['HTTP_REFERER']."&code=".$_POST['code']);
		break;

	// ��Ƽ �˾� ����
	case "popupModifiy":

		// ���� ó��
		$multipopup->popupModifiy($_POST['code']);
		break;

	// ��Ƽ �˾� ����
	case "copyPopup":

		// ���� ó��
		$multipopup->popupCopy($_GET['code']);
		break;

	// ��Ƽ �˾� ����
	case "delPopup":

		// ���� ó��
		$multipopup->popupDelete($_GET['code']);
		break;

	// ��Ƽ �˾� �̹��� ���
	case "upload":

		// �̹��� ��� ó��
		$multipopup->imgUploadTemp();
		exit();
		break;

	default:
		break;

}

go($_SERVER['HTTP_REFERER']);
?>