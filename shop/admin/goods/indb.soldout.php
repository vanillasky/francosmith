<?
include "../lib.php";

$now = date('Y-m-d H:i:s');

$data = array();

switch ($_POST['mode']) {
	case 'config':
		require_once("../../lib/qfile.class.php");
		$qfile = new qfile();

		$image_uploaded = false;

		// soldout �������� soldout_overlay�̹���
		if ($_FILES['soldout_overlay']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['soldout_overlay'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png',$_ext) !== false) {	// ��� Ȯ���� �˻�
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/custom/soldout_overlay')) {
						echo '<script>parent.document.getElementById("el-user-soldout-overlay").style.backgroundImage="../../data/goods/icon/custom/soldout_overlay?'.time().'";</script>';
					}
				}
			}
			else {
				msg('��ǰ �������� �̹����� png �̹����� ���˴ϴ�.');
			}
		}

		//  ����� soldout �������� mobile_custom_soldout�̹���
		if ($_FILES['mobile_custom_soldout']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['mobile_custom_soldout'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png',$_ext) !== false) {	// ��� Ȯ���� �˻�
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/mobile_custom_soldout')) {
						echo '<script>parent.document.getElementById("mobile-el-user-soldout-overlay").style.backgroundImage="../../data/goods/icon/mobile_custom_soldout?'.time().'";</script>';
					}
				}
			}
			else {
				msg('��ǰ �������� �̹����� png �̹����� ���˴ϴ�.');
			}
		}

		// ǰ�� ������
		if ($_FILES['soldout_icon']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['soldout_icon'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png gif jpg jpeg',$_ext) !== false) {	// ��� Ȯ���� �˻�
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/custom/soldout_icon')) {
						echo '<script>parent.document.getElementById("el-user-soldout-icon").src="../../data/goods/icon/custom/soldout_icon?'.time().'";</script>';
					}
				}
			}
		}

		// ���� ��ü �̹���
		if ($_FILES['soldout_price']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['soldout_price'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png gif jpg jpeg',$_ext) !== false) {	// ��� Ȯ���� �˻�
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/custom/soldout_price')) {
						echo '<script>parent.document.getElementById("el-user-soldout-price").src="../../data/goods/icon/custom/soldout_price?'.time().'";</script>';
					}
				}
			}
		}

		$qfile->open("../../conf/config.soldout.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg_soldout = array( \n");
		foreach ($_POST['cfg_soldout'] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		msg('����Ǿ����ϴ�.');
		exit;
		break;

}

?>