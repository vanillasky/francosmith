<?php
include("../lib/library.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

$m_id = (string)$_POST['m_id'];
$password = (string)$_POST['password'];

$result = $session->login($m_id,$password);

if ($result === true) echo 'result=true';
elseif ($result === 'NOT_VALID') echo 'result=false|msg=아이디 또는 비밀번호 입력 형식 오류입니다.';
elseif ($result === 'NOT_FOUND') echo 'result=false|msg=아이디 또는 비밀번호 오류입니다.';
elseif ($result === 'NOT_ACCESS') echo 'result=false|msg=고객님은 본 사이트에서 승인되지 않아 로그인이 제한됩니다.';
?>