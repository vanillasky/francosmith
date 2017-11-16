<?php
include '../../lib/library.php';

try {
	if(!$_POST['mode']){
		throw new Exception("올바른 접근이 아닙니다.");
	}

	$hashtag = Core::loader('hashtag');
	if(!is_object($hashtag)){
		throw new Exception("해시태그 기능추가 패치를 확인해 주세요.");
	}

	foreach($_POST as $key => $value){
		$_POST[$key] = iconv("UTF-8", "EUC-KR", $value);
	}

	$resultArray = array();
	switch($_POST['mode']){
		//input 해시태그 리스트
		case 'inputList':
			$resultArray = $hashtag->getInputListHashtag($_POST['searchText']);
		break;

		//관리모드 > 상품상세 > 상품 해시태그
		case 'goodsList':
			$resultArray = $hashtag->getGoodsListHashtag($_POST['goodsno']);
		break;

		//관리모드 > 해시태그 관리 > 리스트
		//관리모드 > 해시태그 관련설정 > 사용자설정 팝업
		case 'allList': case 'clickAllList':
			$resultArray = $hashtag->getAllListHashtag($_POST);
		break;

		//관리모드 > 상품상세 > 상품 해시태그
		case 'addLayout':
			$resultArray = $hashtag->addHashtagLayout($_POST['hashtag']);
		break;

		//관리모드 > 해시태그 관리 > 추가
		case 'addLive' :
			$resultArray = $hashtag->addHashtagLive($_POST['hashtag']);
		break;

		//관리모드 > 해시태그 관리 > 삭제
		case 'deleteLive' :
			$resultArray = $hashtag->deleteHashtagLive($_POST['hashtag']);
		break;

		//관리모드 > 빠른 해시태그 수정 > 삭제
		case 'deleteManageLive' :
			$resultArray = $hashtag->deleteManageHashtagLive($_POST['hashtag'], $_POST['goodsno']);
		break;

		//관리모드 > 해시태그 관련 설정 > 기본 해시태그 설정
		case 'migrationHashtag' :
			$resultArray = $hashtag->migrationHashtag($_POST['checkboxParam']);
		break;

		//관리모드 > 해시태그 관련 설정 > 사용자 설정 팝업 페이지
		case 'saveDisplay' :
			$resultArray = $hashtag->saveHashtagDisplay($_POST['hashtagNo']);
		break;

		//유저모드 > 상품상세페이지 > 추가
		case 'addLiveUser' :
			$resultArray = $hashtag->saveHashtagLiveUser($_POST);
		break;

		//관리모드 > 상품 > 메인페이지 상품진열 (실시간 해시태그 체크)
		case 'checkLiveHashtag':
			$resultArray = array('result'=>'success', 'data'=>'');

			$hashtagNameArray = array();
			$hashtagNameArray = gd_json_decode(stripslashes($_POST['hashtagName']));
			$hashtagNameArray = array_filter($hashtagNameArray);

			if(count($hashtagNameArray) > 0){
				foreach($hashtagNameArray as $dataArray){
					$hashtagName = '';
					$checkResult = false;

					$hashtagName = $hashtag->setHashtag($dataArray['value']);
					$checkResult = $hashtag->checkHashtag($hashtagName);
					if($checkResult === true){
						//사용불가
						$resultArray['data'] = $dataArray['key'];
						break;
					}
				}
			}
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