<?php
include '../lib.php';
include '../../lib/qfile.class.php';
$qfile = new qfile();

$hashtag = Core::loader('hashtag');
if(!is_object($hashtag)){
	msg("�ؽ��±� ����߰� ��ġ�� Ȯ���� �ּ���.", -1);
	exit;
}
if(!$_POST['mode']){
	msg("�ùٸ� ������ �ƴմϴ�.", -1);
	exit;
}

try {
	switch($_POST['mode']){
		//�ؽ��±� ����
		//�ؽ��±� ���� ���� ����
		case 'listConfigSave' : case 'configSave' :
			$errorMessage = '';
			$errorMessage = $hashtag->saveConfig($_POST);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			msg("���������� ����Ǿ����ϴ�.");
			popupReload();
			exit;
		break;

		case 'listSave' :
			$errorMessage = '';
			$errorMessage = $hashtag->saveList($_POST);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			msg("���������� ����Ǿ����ϴ�.");
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