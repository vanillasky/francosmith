<?php
include '../../lib/library.php';

try {
	if(!trim($_POST['mode'])){
		throw new Exception("올바른 접근이 아닙니다.");
	}
	if(is_file('../../lib/guidedSelling.class.php')){
		$guidedSelling = Core::loader('guidedSelling');
	}
	if(!is_object($guidedSelling)){
		throw new Exception("가이드 셀링 기능추가 패치를 확인해 주세요.");
	}

	foreach($_POST as $key => $value){
		$_POST[$key] = iconv("UTF-8", "EUC-KR", urldecode($value));
	}

	$resultArray = array();
	switch($_POST['mode']){
		//input 해시태그 리스트
		case 'inputList':
			$hashtag = Core::loader('hashtag');
			if(!is_object($hashtag)){
				throw new Exception("해시태그 기능추가 패치를 확인해 주세요.");
			}

			$resultArray = $hashtag->getInputListHashtag($_POST['searchText']);
		break;

		//질문추가
		case 'getLiveQuestion': case 'getLiveQuestionModify':
			$resultArray = $guidedSelling->getLiveQuestion($_POST);
		break;

		//답변추가
		case 'getLiveAnswer' :
			$resultArray = $guidedSelling->getLiveAnswer($_POST);
		break;

		//디스플레이 유형 변경
		case 'changeAnswerDisplay':
			$resultArray = $guidedSelling->changeAnswerDisplay($_POST);
		break;

		//submit 전 데이터 체크
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