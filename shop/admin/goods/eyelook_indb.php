<?

include "../lib.php";
require_once("../../lib/upload.lib.php");
include_once "../_mobileapp/admin_eyelook.class.php";
$upload = new upload_file;
$eyelook = new admin_eyelook();

$mode = $_POST['mode'] ?  $_POST['mode'] : $_GET['mode'];

if(is_uploaded_file($_FILES['img_eyelook']['tmp_name'][0])){
	$div = explode(".",$_FILES['img_eyelook']['name'][0]);
	$ext = $div[count($div)-1];

	if(($div[count($div)-1] != "PNG") && ($div[count($div)-1] != "png") && ($div[count($div)-1] != "GIF") && ($div[count($div)-1] != "gif")) {
		msg('아이룩 상품이미지 파일이 올바르지 않습니다.',-1);
		exit;
	}
}

function chk_goods_img($type){
	global $upload;
	if($_FILES[$type]){
		$file_array = array();
		$file_array = reverse_file_array($_FILES[$type]);
		foreach($file_array as $k => $v){
			$upload->upload_file($file_array[$k],'','image');
			if(!$upload->file_extension_check())return false;
			if(!$upload->file_type_check())return false;
		}
	}
	return true;
}

function delGoodsImg($str)
{
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$div = explode("|",$str);
	foreach ($div as $v){
		if ($v == '') continue;

		if (is_file($_dir.$v)) @unlink($_dir.$v);
		if (is_file($_dirT.$v)) @unlink($_dirT.$v);
	}
}

switch ($mode){

	case "register":

		list($cnt) = $db->fetch("select count(*) cnt from ".GD_EYELOOK." where goodsno='".$_POST['goodsno']."'");
		if($cnt > 0)	msg('아이룩 상품 이미지가 이미 등록되어 있습니다.',-1);
		
		if(!chk_goods_img('img_eyelook'))	msg('아이룩 상품이미지 파일이 올바르지 않습니다.',-1);
		multiUpload("img_eyelook");
		
		$db->query("insert into ".GD_EYELOOK." set goodsno='".$_POST['goodsno']."',img_eyelook='".$file['img_eyelook']['name'][0]."', regdt=now()");

		msg("아이룩 상품 이미지가 등록 되었습니다.");
		$_POST[returnUrl] = "./eyelook_goods.php";
		break;

	case "modify":
		
		$data = $db->fetch("select * from ".GD_EYELOOK." where idx='".$_POST['idx']."'");
		$_POST['del']['img_eyelook'][0] = 1;
		
		if(!chk_goods_img('img_eyelook'))	msg('아이룩 상품이미지 파일이 올바르지 않습니다.',-1);
		multiUpload("img_eyelook");

		$db->query("update ".GD_EYELOOK." set img_eyelook='".$file['img_eyelook']['name'][0]."' where idx='".$_POST['idx']."'");

		msg("아이룩 상품 이미지가 변경 되었습니다.");
		if (!$_POST[returnUrl]) $_POST[returnUrl] = "./eyelook_goods.php";
		break;

	case "delete":

		foreach ($_POST['chk'] as $key => $idx) {

			$eye_data = $db->fetch("select * from ".GD_EYELOOK." where idx='".$idx."'");

			delGoodsImg($eye_data['img_eyelook']);

			$db->query("delete from ".GD_EYELOOK." where idx='".$idx."'");

		}

		msg("아이룩 상품 이미지가 삭제 되었습니다.");
		if (!$_POST[returnUrl]) $_POST[returnUrl] = "./eyelook_goods.php";
		break;
}

$result = $eyelook -> introduction();

go($_POST[returnUrl]);