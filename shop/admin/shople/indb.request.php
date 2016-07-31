<?
// 입점 신청은 단발성이므로 클래스로 구현하지 아니함.

include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');
// 이미 사용중인거임?

// 변수정리
$_POST['shop_sno'] = $godo['sno'];
$_POST['company_address'] = $_POST['address'];
$_POST['company_zipcode'] = implode("-",$_POST['zipcode']);
unset($_POST['x'],$_POST['y'],$_POST['address'],$_POST['zipcode']);

// 변수 체크









// 전달 변수
$param = $_POST;

// 사업자등록증 이미지 첨부?
if (isset($_FILES)) {

	foreach($_FILES as $k => $file) {

		if ($file['error'] == UPLOAD_ERR_OK) {

			$file_ext = array_pop(explode('.',$file['name']));

			if (strpos('jpg, jpeg, gif',$file_ext) === false) {
				msg('jpg, gif, png 형식의 이미지만 업로드 가능합니다.');
				exit;
			}

			$param[$k] = '@'.$file['tmp_name'];
		}

	}

}

$rs = $shople->subscribe->request($param);

?>
<script type="text/javascript">
parent.location.reload();
</script>
