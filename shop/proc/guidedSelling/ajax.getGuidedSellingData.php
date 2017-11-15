<?php
include '../../lib/library.php';

try {
	if(!trim($_POST['mode'])){
		throw new Exception("�ùٸ� ������ �ƴմϴ�.");
	}
	if(is_file('../../lib/guidedSelling.class.php')){
		$guidedSelling = Core::loader('guidedSelling');
	}
	if(!is_object($guidedSelling)){
		throw new Exception("���̵� ���� ����߰� ��ġ�� Ȯ���� �ּ���.");
	}

	foreach($_POST as $key => $value){
		$_POST[$key] = iconv("UTF-8", "EUC-KR", urldecode($value));
	}

	$resultArray = array();
	switch($_POST['mode']){
		//input �ؽ��±� ����Ʈ
		case 'inputList':
			$hashtag = Core::loader('hashtag');
			if(!is_object($hashtag)){
				throw new Exception("�ؽ��±� ����߰� ��ġ�� Ȯ���� �ּ���.");
			}

			$resultArray = $hashtag->getInputListHashtag($_POST['searchText']);
		break;

		//�����߰�
		case 'getLiveQuestion': case 'getLiveQuestionModify':
			$resultArray = $guidedSelling->getLiveQuestion($_POST);
		break;

		//�亯�߰�
		case 'getLiveAnswer' :
			$resultArray = $guidedSelling->getLiveAnswer($_POST);
		break;

		//���÷��� ���� ����
		case 'changeAnswerDisplay':
			$resultArray = $guidedSelling->changeAnswerDisplay($_POST);
		break;

		//submit �� ������ üũ
		case 'checkBeforeSubmit' :
			$resultArray = $guidedSelling->checkBeforeSubmit($_POST);
		break;

		case 'openHashtagPage' :
			$resultArray = $guidedSelling->checkHashtagOpenPage($_POST['hashtagName']);
		break;
	}

	if($resultArray['result'] !== 'success'){
		throw new Exception($resultArray['data']);
	}

	echo "success|".gd_json_encode($resultArray['data']);
	exit;
}
catch(Exception $e){
	echo "fail|".$e->getMessage();
	exit;
}
?>