<?
// ���� ��û�� �ܹ߼��̹Ƿ� Ŭ������ �������� �ƴ���.

include "../lib.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');
// �̹� ������ΰ���?

// ��������
$_POST['shop_sno'] = $godo['sno'];
$_POST['company_address'] = $_POST['address'];
$_POST['company_zipcode'] = implode("-",$_POST['zipcode']);
unset($_POST['x'],$_POST['y'],$_POST['address'],$_POST['zipcode']);

// ���� üũ









// ���� ����
$param = $_POST;

// ����ڵ���� �̹��� ÷��?
if (isset($_FILES)) {

	foreach($_FILES as $k => $file) {

		if ($file['error'] == UPLOAD_ERR_OK) {

			$file_ext = array_pop(explode('.',$file['name']));

			if (strpos('jpg, jpeg, gif',$file_ext) === false) {
				msg('jpg, gif, png ������ �̹����� ���ε� �����մϴ�.');
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
