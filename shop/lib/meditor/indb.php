<?

include "../library.php";
require_once("../upload.lib.php");

switch ($_POST[mode]){

	case "InsertImage":

		$_POST[idx] += 0;
		$_POST['mini_url'] = trim($_POST['mini_url']);

		if ($_POST['mini_url'] != '' && $_POST['mini_url'] != 'http://'){
			$src = $_POST['mini_url'];
		}
		else {
			//$dir = "data/";
			$dir = "../../data/editor/";
			if ($_FILES[mini_file][error] === UPLOAD_ERR_INI_SIZE) {
				echo '<script>alert("이미지 파일 사이즈가 너무 큽니다. (최대 '.byte2str(str_to_byte(ini_get('upload_max_filesize'))).')");</script>';
				exit;
			}
			if (!preg_match("/^image/",$_FILES[mini_file][type])){
				echo "<script>alert('이미지 파일만 업로드가 가능합니다');</script>";
				exit;
			}

			if (is_uploaded_file($_FILES[mini_file][tmp_name])){
				$div = explode(".",$_FILES[mini_file][name]);
				$filename = substr(md5(microtime()),0,16).".".$div[count($div)-1];
				$upload = new upload_file($_FILES['mini_file'],$dir.$filename,'image');
				if(!$upload -> upload()){
					echo "<script>alert('이미지 파일만 업로드가 가능합니다');</script>";
					exit;
				}
				setDu('editor'); # 계정용량 계산
			}
			$src = dirname($_SERVER[PHP_SELF])."/".$dir.$filename;
		}

		if ($_POST[imgWidth] && $_POST[imgHeight]) $size = " width='$_POST[imgWidth]' height='$_POST[imgHeight]'";

		if ($src) echo "<script>parent.opener.mini_set_html($_POST[idx],\"<img src='$src' $size>\");</script>";
		echo "<script>parent.window.close();</script>";
		break;

}

?>