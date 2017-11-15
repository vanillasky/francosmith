<?php
include '../lib.php';

if (in_array($_POST['hashtagMethod'], array('all_add_goods', 'all_add', 'tag_del')) === false) {
	msg("�ʼ� ������ �����Ǿ����ϴ�.", -1);
}
if ($_POST['hashtagMethod'] == 'all_add_goods' && !trim($_POST['hashtagName1'])) {
	msg("�ϰ������� ����� �ؽ��±׸� �Է��� �ּ���.", -1);
}
if ($_POST['hashtagMethod'] == 'all_add' && !trim($_POST['hashtagName2'])) {
	msg("�ϰ������� ����� �ؽ��±׸� �Է��� �ּ���.", -1);
}
if ($_POST['hashtagMethod'] == 'tag_del' && !trim($_POST['hashtagName3'])) {
	msg("�ؽ��±׸� �˻� �� ����Ͽ� �ּ���.", -1);
}

$hashtag = Core::loader('hashtag');

if(count($_POST['chk']) < 1){
	msg('��ǰ�� �����Ͽ� �ּ���.', -1);
}

$errorMessage = '';
$errorMessage = $hashtag->indbManageHashtag($_POST, $_POST['chk']);
if($errorMessage !== ''){
	msg($errorMessage, -1);
	break;
}

switch($_POST['hashtagMethod']){
	case 'all_add_goods': case 'all_add':
		$successMessage = '��ǰ�� �ؽ��±װ� ����Ǿ����ϴ�.';
		echo '
		<script>
		alert("'.$successMessage.'");
		parent.location.reload();
		</script>
		';
	break;

	case 'tag_del':
		$successMessage = '�����Ǿ����ϴ�.';
		echo '
		<script>
		alert("'.$successMessage.'");
		parent.location.href = "./adm_goods_manage_hashtag.php";
		</script>
		';
	break;
}
exit;