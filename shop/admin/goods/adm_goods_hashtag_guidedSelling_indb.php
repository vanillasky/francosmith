<?php
include '../lib.php';

try {
	$errorMessage = '';

	if(is_file('../../lib/guidedSelling.class.php')){
		$guidedSelling = Core::loader('guidedSelling');
	}
	if(!is_object($guidedSelling)){
		throw new Exception("가이디드 셀링 기능추가 패치를 확인해 주세요.");
	}

	$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
	if(!trim($mode)){
		throw new Exception("올바른 접근이 아닙니다.");
	}

	switch($mode){
		case 'write' :
			$errorMessage = $guidedSelling->saveGuidedSelling($_POST, $_FILES);
			if($errorMessage !== ''){
				echo "<script>parent.parent.endProgress();</script>";
				throw new Exception($errorMessage);
			}
			msg('정상적으로 저장되었습니다.', './adm_goods_hashtag_guidedSelling_list.php', 'parent');
		break;

		case 'modify':
			$errorMessage = $guidedSelling->modifyGuidedSelling($_POST, $_FILES);
			if($errorMessage !== ''){
				echo "<script>parent.parent.endProgress();</script>";
				throw new Exception($errorMessage);
			}
			msg('정상적으로 수정되었습니다.', './adm_goods_hashtag_guidedSelling_write.php?mode=modify&guided_no='.$_POST['guided_no'], 'parent');
		break;

		//이미지+텍스트형 이미지등록
		case 'saveTempImage' : case 'saveTempBackgroundImage' :
			$resultArray = array();
			$resultArray = $guidedSelling->saveGuidedSellingTempImage($_POST, $_FILES);
			if($resultArray['result'] !== 'ok'){
				throw new Exception($resultArray['data']);
			}
			else {
				//이미지 실시간 반영
				if(trim($resultArray['data'])){
					echo "<script>parent.parent.adjustLiveImage('".$_POST[mode]."','".$_POST[uniqueKey]."', '".$_POST[index]."', '".$resultArray[data]."');</script>";
				}
			}

			msg("이미지가 등록 되었습니다.");

			echo "<script>parent.parent.closeLayer();</script>";
		break;

		case 'delete' :
			$errorMessage = $guidedSelling->deleteGuidedSelling('all', $_GET['guided_no']);
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}
			msg('삭제되었습니다.', './adm_goods_hashtag_guidedSelling_list.php');
		break;
	}
	exit;
}
catch(Exception $e){
	msg($e->getMessage(), -1);
	exit;
}
?>