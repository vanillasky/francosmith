<?php
include '../_header.php';
$jQueryPath = $cfg['rootDir'] . '/lib/js/jquery-1.11.3.min.js';

try {
	if(!$_GET['guided_no']){
		throw new Clib_Exception('잘못된 접근 입니다.');
	}
	$guidedSelling = Core::loader('guidedSelling');

	$guidedData = $unitData = $detailData = array();
	$guidedData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
	if(!$guidedData['guided_no']){
		throw new Clib_Exception('GUIDED DATA가 존재하지 않습니다.');
	}
	$unitData = $guidedSelling->getGuidedSellingUnitData($_GET['guided_no']);
	if(!$unitData['unit_no']){
		throw new Clib_Exception('질문 DATA가 존재하지 않습니다.');
	}
	$detailData = $guidedSelling->getGuidedSellingDetailData($unitData['unit_no']);
	if(count($detailData) < 1){
		throw new Clib_Exception('답변 DATA가 존재하지 않습니다.');
	}

	$tpl->assign(array(
		'jQueryPath' => $jQueryPath, //jQuery 경로
		'guided_no' => $_GET['guided_no'], //가이디드 셀링 번호
		'preview' => $_GET['preview'], //프리뷰상태
		'guided_widgetId' => $_GET['guided_widgetId'], //위젯 아이디
		'guidedSelling_backgroundColor' => $guidedData['guided_backgroundColor'], //배경색
		'displayType' => $unitData['unit_displayType'], //디스플레이 타입
		'backgroundImageUrl' => $unitData['backgroundImageUrl'], //백그라운드 이미지
		'questionName' => $unitData['unit_question'], //질문명
		'answerList' => $detailData, //답변 정보
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	exit;
}
?>