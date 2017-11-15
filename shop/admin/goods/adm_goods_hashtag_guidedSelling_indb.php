<?php
include '../lib.php';

try {
	$errorMessage = '';

	if(is_file('../../lib/guidedSelling.class.php')){
		$guidedSelling = Core::loader('guidedSelling');
	}
	if(!is_object($guidedSelling)){
		throw new Exception("���̵�� ���� ����߰� ��ġ�� Ȯ���� �ּ���.");
	}

	$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
	if(!trim($mode)){
		throw new Exception("�ùٸ� ������ �ƴմϴ�.");
	}

	switch($mode){
		case 'write' :
			$errorMessage = $guidedSelling->saveGuidedSelling($_POST, $_FILES);
			if($errorMessage !== ''){
				echo "<script>parent.parent.endProgress();</script>";
				throw new Exception($errorMessage);
			}
			msg('���������� ����Ǿ����ϴ�.', './adm_goods_hashtag_guidedSelling_list.php', 'parent');
		break;

		case 'modify':
			$errorMessage = $guidedSelling->modifyGuidedSelling($_POST, $_FILES);
			if($errorMessage !== ''){
				echo "<script>parent.parent.endProgress();</script>";
				throw new Exception($errorMessage);
			}
			msg('���������� �����Ǿ����ϴ�.', './adm_goods_hashtag_guidedSelling_write.php?mode=modify&guided_no='.$_POST['guided_no'], 'parent');
		break;

		//�̹���+�ؽ�Ʈ�� �̹������
		case 'saveTempImage' : case 'saveTempBackgroundImage' :
			$resultArray = array();
			$resultArray = $guidedSelling->saveGuidedSellingTempImage($_POST, $_FILES);
			if($resultArray['result'] !== 'ok'){
				throw new Exception($resultArray['data']);
			}
			else {
				//�̹��� �ǽð� �ݿ�
				if(trim($resultArray['data'])){
					echo "<script>parent.parent.adjustLiveImage('".$_POST[mode]."','".$_POST[uniqueKey]."', '".$_POST[index]."', '".$resultArray[data]."');</script>";
				}
			}

			msg("�̹����� ��� �Ǿ����ϴ�.");

			echo "<script>parent.parent.closeLayer();</script>";
		break;

		case 'delete' :
			$errorMessage = $guidedSelling->deleteGuidedSelling('all', $_GET['guided_no']);
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}
			msg('�����Ǿ����ϴ�.', './adm_goods_hashtag_guidedSelling_list.php');
		break;
	}
	exit;
}
catch(Exception $e){
	msg($e->getMessage(), -1);
	exit;
}
?>