<?php
include '../lib/library.php';

$mode = $_POST['mode'];
if(!$mode) $mode = $_GET['mode'];

switch($mode){
	case "chkBoardCaptcha":
		header("Content-type: text/html; charset=euc-kr");
		if (file_exists("../conf/bd_$_POST[id].php")) {
			include "../conf/bd_$_POST[id].php";
		}

		# Anti-Spam 검증
		$switch = ($bdSpamBoard&1 ? '103' : '000') . ($bdSpamBoard&2 ? '4' : '0');
		$rst = antiSpam($switch, "", "post");
		if (substr($rst[code],0,1) == '4') exit ("자동등록방지문자가 일치하지 않습니다. 다시 입력하여 주십시요.");
		if ($rst[code] <> '0000') exit("무단 링크를 금지합니다.");
		echo 'true';
		break;
}
?>