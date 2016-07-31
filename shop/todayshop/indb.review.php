<?
require_once('../lib/todayshop_cache.class.php');
include "../lib/library.php";
require_once("../lib/upload.lib.php");

function confirm_reload($str,$url='')
{
	if($url){
		echo "
		<script>
		alert('$str');
		if (opener){ opener.location.reload(); window.close(); }
		else location.href='$url';
		</script>
		";
	}else{
		echo "
		<script>
		alert('$str');
		if (opener){ opener.location.reload(); window.close(); }
		else parent.location.reload();
		</script>
		";
	}
	exit;
}

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text',
		'contents' => array('html', 'ent_quotes'),
		'subject' => array('html', 'ent_quotes'),

	));
}

$mode = $_POST[mode];
if(!$_POST['secret'])$_POST['secret']=0;

if ($mode) todayshop_cache::remove('*','goodsreview');	// ���� ĳ�� ����
switch ($mode){

	case "add_review":

		$query = "
		insert into ".GD_TODAYSHOP_GOODS_REVIEW." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			point		= '$_POST[point]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		$sno=$db->lastID();

		$attach = 0;

		// �̹��� ���ε�
		if ($_FILES['attach']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			if (is_uploaded_file($_FILES['attach'][tmp_name])){
				$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
				$filename = 'TSRV'.sprintf("%010s", $sno);
				$filename_tmp = $filename.'_tmp';
				$upload = new upload_file($_FILES['attach'],$data_path.$filename_tmp,'image');
				if (!$upload -> upload()){
					msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
					exit;
				} else {
					$img_size = getimagesize( $data_path.$filename_tmp);
					if ($img_size[0] > 700) {
						thumbnail($data_path.$filename_tmp,$data_path.$filename,700);
					} else {
						copy($data_path.$filename_tmp,$data_path.$filename);
					}
					@unlink($data_path.$filename_tmp);
					$attach = 1;
				}
			}
		}

		$db->query("update ".GD_TODAYSHOP_GOODS_REVIEW." set parent=sno, contents='$_POST[contents]', attach='$attach' where sno='$sno'");
		confirm_reload("���������� ��ϵǾ����ϴ�","goods_review_list.php?goodsno=".$_POST[goodsno]);
		break;

	case "mod_review":

		### ����üũ
		list( $password ) = $db->fetch("select password from ".GD_TODAYSHOP_GOODS_REVIEW." where sno = '$_POST[sno]'");
		if ( !isset($sess) && $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����

		if ($_POST[remove_attach] == 1) {
			$name = 'TSRV'.sprintf("%010s", $_POST[sno]);
			@unlink($_SERVER['DOCUMENT_ROOT'].'/shop/data/review/'.$name);

			$attach_query = ", attach = '0'";
		}

		// �̹��� ���ε�
		if ($_FILES['attach']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			if (is_uploaded_file($_FILES['attach'][tmp_name])){
				$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
				$filename = 'TSRV'.sprintf("%010s", $_POST[sno]);
				$filename_tmp = $filename.'_tmp';
				$upload = new upload_file($_FILES['attach'],$data_path.$filename_tmp,'image');
				if (!$upload -> upload()){
					msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
					exit;
				} else {
					$img_size = getimagesize( $data_path.$filename_tmp);
					if ($img_size[0] > 700) {
						thumbnail($data_path.$filename_tmp,$data_path.$filename,700);
					} else {
						copy($data_path.$filename_tmp,$data_path.$filename);
					}
					@unlink($data_path.$filename_tmp);
					$attach_query = ", attach = '1'";
				}
			}
		}

		$query = "
		update ".GD_TODAYSHOP_GOODS_REVIEW." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			point		= '$_POST[point]',
			name		= '$_POST[name]'
			$attach_query
		where sno = '$_POST[sno]'
		";
		$db->query($query);
		confirm_reload("���������� �����Ǿ����ϴ�");
		break;

	case "reply_review":

		$query = "
		insert into ".GD_TODAYSHOP_GOODS_REVIEW." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			parent		= '$_POST[sno]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		$sno=$db->lastID();

		$attach = 0;

		// �̹��� ���ε�
		if ($_FILES['attach']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			if (is_uploaded_file($_FILES['attach'][tmp_name])){
				$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
				$filename = 'TSRV'.sprintf("%010s", $sno);
				$filename_tmp = $filename.'_tmp';
				$upload = new upload_file($_FILES['attach'],$data_path.$filename_tmp,'image');
				if (!$upload -> upload()){
					msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
					exit;
				} else {
					$img_size = getimagesize( $data_path.$filename_tmp);
					if ($img_size[0] > 700) {
						thumbnail($data_path.$filename_tmp,$data_path.$filename,700);
					} else {
						copy($data_path.$filename_tmp,$data_path.$filename);
					}
					@unlink($data_path.$filename_tmp);
					$attach = 1;
				}
			}
		}

		$db->query("update ".GD_TODAYSHOP_GOODS_REVIEW." set contents='$_POST[contents]', attach='$attach' where sno='$sno'");
		confirm_reload("���������� ��ϵǾ����ϴ�");
		break;

	case "del_review":

		### ����üũ
		list( $password ) = $db->fetch("select password from ".GD_TODAYSHOP_GOODS_REVIEW." where sno = '$_POST[sno]'");
		if ( !isset($sess) && $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����

		$query = "delete from ".GD_TODAYSHOP_GOODS_REVIEW." where sno = '$_POST[sno]'";
		$db->query($query);

		$name = 'TSRV'.sprintf("%010s", $_POST[sno]);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/shop/data/review/'.$name);

		confirm_reload("���������� �����Ǿ����ϴ�");
		break;

}

?>
