<?
include "../lib.php";

$now = date('Y-m-d H:i:s');

$data = array();

switch ($_POST['mode']) {
	case 'config':
		require_once("../../lib/qfile.class.php");
		$qfile = new qfile();

		$image_uploaded = false;

		// soldout 오버레이 soldout_overlay이미지
		if ($_FILES['soldout_overlay']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['soldout_overlay'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png',$_ext) !== false) {	// 허용 확장자 검사
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/custom/soldout_overlay')) {
						echo '<script>parent.document.getElementById("el-user-soldout-overlay").style.backgroundImage="../../data/goods/icon/custom/soldout_overlay?'.time().'";</script>';
					}
				}
			}
			else {
				msg('상품 오버레이 이미지는 png 이미지만 허용됩니다.');
			}
		}

		//  모바일 soldout 오버레이 mobile_custom_soldout이미지
		if ($_FILES['mobile_custom_soldout']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['mobile_custom_soldout'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png',$_ext) !== false) {	// 허용 확장자 검사
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/mobile_custom_soldout')) {
						echo '<script>parent.document.getElementById("mobile-el-user-soldout-overlay").style.backgroundImage="../../data/goods/icon/mobile_custom_soldout?'.time().'";</script>';
					}
				}
			}
			else {
				msg('상품 오버레이 이미지는 png 이미지만 허용됩니다.');
			}
		}

		// 품절 아이콘
		if ($_FILES['soldout_icon']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['soldout_icon'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png gif jpg jpeg',$_ext) !== false) {	// 허용 확장자 검사
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/custom/soldout_icon')) {
						echo '<script>parent.document.getElementById("el-user-soldout-icon").src="../../data/goods/icon/custom/soldout_icon?'.time().'";</script>';
					}
				}
			}
		}

		// 가격 대체 이미지
		if ($_FILES['soldout_price']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['soldout_price'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png gif jpg jpeg',$_ext) !== false) {	// 허용 확장자 검사
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

		msg('저장되었습니다.');
		exit;
		break;

}

?>