<?php
include '../lib.php';
include '../../lib/qfile.class.php';
$qfile = new qfile();

$hashtag = Core::loader('hashtag');
if(!is_object($hashtag)){
	msg("해시태그 기능추가 패치를 확인해 주세요.", -1);
	exit;
}
if(!$_POST['mode']){
	msg("올바른 접근이 아닙니다.", -1);
	exit;
}

try {
	switch($_POST['mode']){
		//해시태그 관리
		//해시태그 관련 설정 저장
		case 'listConfigSave' : case 'configSave' :
			$errorMessage = '';
			$errorMessage = $hashtag->saveConfig($_POST);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			msg("정상적으로 저장되었습니다.");
			popupReload();
			exit;
		break;

		case 'listSave' :
			$errorMessage = '';
			$errorMessage = $hashtag->saveList($_POST);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			msg("정상적으로 저장되었습니다.");
			popupReload();
			exit;
		break;
	}
}
catch(Exception $e){
	msg($e->getMessage(), -1);
	exit;
}
?>