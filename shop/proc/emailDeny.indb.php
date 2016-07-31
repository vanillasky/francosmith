<?php
### 이메일 수신거부 처리
include '../lib/library.php';

$emailDeny = Core::loader('LibEmailDeny');
$res = $emailDeny->setDeny($_POST['k'], $_POST['id']);

if ($res['result'] == true) {
	echo '<script type="text/javascript">alert("이메일 수신거부가 정상 처리되었습니다.\n재수신을 원하시는 경우 마이페이지 > 회원정보수정에서 정보,이벤트메일 수신에 “체크”해 주시기 바랍니다."); parent.window.close();</script>';
}
else {
	echo '<script type="text/javascript">alert("이메일 수신거부에 실패하였습니다.\n잠시 후 다시 시도하거나 마이페이지 > 회원정보수정에서 정보,이벤트메일수신에 “체크”를 해지해 주시기 바랍니다."); parent.window.close();</script>';
}
?>