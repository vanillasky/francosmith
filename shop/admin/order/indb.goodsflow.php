<?php
include "../lib.php";
include "../../conf/config.php";

//header("Content-Type: text/html; charset=utf-8");

$gf = Core::loader('goodsflow_v2');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

switch ($_POST['process']) {
	case 'invoice':

		$target = array();

		if ($_POST['target_type'] == 'query') {
			$query = base64_decode($_POST['query']);
			$rs = $db->query($query);
			while ($row = $db->fetch($rs,1)) {
				$target[] = $row['ordno'];
			}
		}
		else {
			$target = $_POST['target']['ordno'];
		}

		if (($gf->invoice($target,$_POST['mode'])) === true) {
			// �½��÷� ���� ��� �������� �̵��ǹǷ�, ó���Ұ� ����.
		}
		else {
			if(!$cfg[compName] || !$cfg[address] || !$cfg[compPhone]) $message = "�⺻���� �������� ȸ�� �������� ��ȣ�� / ��ȭ��ȣ / �ּҸ� Ȯ�����ּ���. ";
			msg($message.'�ֹ����� ���ۿ� �����߽��ϴ�.','close');
			exit;
		}
		break;

	case 'delivery' :

		$target = array();

		if ($_POST['target_type'] == 'query') {
			$query = base64_decode($_POST['query']);
			$rs = $db->query($query);
			while ($row = $db->fetch($rs,1)) {
				$target[] = $row['ordno'];
			}
		}
		else {
			$target = $_POST['TransUniqueCd'];
		}

		if (($rs = $gf->delvering($target)) === true) {
			msg('����� ó���Ǿ����ϴ�.',$_SERVER['HTTP_REFERER']);
			exit;
		}
		else {
			msg('�ֹ����� ���ۿ� �����߽��ϴ�.',-1);
			exit;
		}
		break;

}	// switch ($_POST['process'])


switch ($_GET['process']) {

	case 'cancel':
		if (($rs = $gf->cancel($_GET['TransUniqueCd'])) === true) {
			msg('�߱� ���ó�� �Ǿ����ϴ�.', $_SERVER['HTTP_REFERER']);
			exit;
		}
		else {
			msg('���ó���� �����߽��ϴ�.',-1);
			exit;
		}
		break;

	case 'reinvoice':
		if (($rs = $gf->reinvoice($_GET['TransUniqueCd'])) === true) {
			// �½��÷� ���� ��� �������� �̵��ǹǷ�, ó���Ұ� ����.
		}
		else {
			echo '
			<script>
				try {
					opener.location.reload();
				} catch (e) { }
			</script>
			';
			msg('��߼� ó���� �����߽��ϴ�.','close');
			exit;
		}
		break;
}
?>
