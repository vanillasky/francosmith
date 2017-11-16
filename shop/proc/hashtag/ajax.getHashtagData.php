<?php
include '../../lib/library.php';

try {
	if(!$_POST['mode']){
		throw new Exception("�ùٸ� ������ �ƴմϴ�.");
	}

	$hashtag = Core::loader('hashtag');
	if(!is_object($hashtag)){
		throw new Exception("�ؽ��±� ����߰� ��ġ�� Ȯ���� �ּ���.");
	}

	foreach($_POST as $key => $value){
		$_POST[$key] = iconv("UTF-8", "EUC-KR", $value);
	}

	$resultArray = array();
	switch($_POST['mode']){
		//input �ؽ��±� ����Ʈ
		case 'inputList':
			$resultArray = $hashtag->getInputListHashtag($_POST['searchText']);
		break;

		//������� > ��ǰ�� > ��ǰ �ؽ��±�
		case 'goodsList':
			$resultArray = $hashtag->getGoodsListHashtag($_POST['goodsno']);
		break;

		//������� > �ؽ��±� ���� > ����Ʈ
		//������� > �ؽ��±� ���ü��� > ����ڼ��� �˾�
		case 'allList': case 'clickAllList':
			$resultArray = $hashtag->getAllListHashtag($_POST);
		break;

		//������� > ��ǰ�� > ��ǰ �ؽ��±�
		case 'addLayout':
			$resultArray = $hashtag->addHashtagLayout($_POST['hashtag']);
		break;

		//������� > �ؽ��±� ���� > �߰�
		case 'addLive' :
			$resultArray = $hashtag->addHashtagLive($_POST['hashtag']);
		break;

		//������� > �ؽ��±� ���� > ����
		case 'deleteLive' :
			$resultArray = $hashtag->deleteHashtagLive($_POST['hashtag']);
		break;

		//������� > ���� �ؽ��±� ���� > ����
		case 'deleteManageLive' :
			$resultArray = $hashtag->deleteManageHashtagLive($_POST['hashtag'], $_POST['goodsno']);
		break;

		//������� > �ؽ��±� ���� ���� > �⺻ �ؽ��±� ����
		case 'migrationHashtag' :
			$resultArray = $hashtag->migrationHashtag($_POST['checkboxParam']);
		break;

		//������� > �ؽ��±� ���� ���� > ����� ���� �˾� ������
		case 'saveDisplay' :
			$resultArray = $hashtag->saveHashtagDisplay($_POST['hashtagNo']);
		break;

		//������� > ��ǰ�������� > �߰�
		case 'addLiveUser' :
			$resultArray = $hashtag->saveHashtagLiveUser($_POST);
		break;

		//������� > ��ǰ > ���������� ��ǰ���� (�ǽð� �ؽ��±� üũ)
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
						//���Ұ�
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