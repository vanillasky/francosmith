<?php
include("../lib/library.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

$m_id = (string)$_POST['m_id'];
$password = (string)$_POST['password'];

$result = $session->login($m_id,$password);

if ($result === true) echo 'result=true';
elseif ($result === 'NOT_VALID') echo 'result=false|msg=���̵� �Ǵ� ��й�ȣ �Է� ���� �����Դϴ�.';
elseif ($result === 'NOT_FOUND') echo 'result=false|msg=���̵� �Ǵ� ��й�ȣ �����Դϴ�.';
elseif ($result === 'NOT_ACCESS') echo 'result=false|msg=������ �� ����Ʈ���� ���ε��� �ʾ� �α����� ���ѵ˴ϴ�.';
?>