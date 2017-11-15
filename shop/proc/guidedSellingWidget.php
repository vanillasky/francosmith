<?php
include '../_header.php';
$jQueryPath = $cfg['rootDir'] . '/lib/js/jquery-1.11.3.min.js';

try {
	if(!$_GET['guided_no']){
		throw new Clib_Exception('�߸��� ���� �Դϴ�.');
	}
	$guidedSelling = Core::loader('guidedSelling');

	$guidedData = $unitData = $detailData = array();
	$guidedData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
	if(!$guidedData['guided_no']){
		throw new Clib_Exception('GUIDED DATA�� �������� �ʽ��ϴ�.');
	}
	$unitData = $guidedSelling->getGuidedSellingUnitData($_GET['guided_no']);
	if(!$unitData['unit_no']){
		throw new Clib_Exception('���� DATA�� �������� �ʽ��ϴ�.');
	}
	$detailData = $guidedSelling->getGuidedSellingDetailData($unitData['unit_no']);
	if(count($detailData) < 1){
		throw new Clib_Exception('�亯 DATA�� �������� �ʽ��ϴ�.');
	}

	$tpl->assign(array(
		'jQueryPath' => $jQueryPath, //jQuery ���
		'guided_no' => $_GET['guided_no'], //���̵�� ���� ��ȣ
		'preview' => $_GET['preview'], //���������
		'guided_widgetId' => $_GET['guided_widgetId'], //���� ���̵�
		'guidedSelling_backgroundColor' => $guidedData['guided_backgroundColor'], //����
		'displayType' => $unitData['unit_displayType'], //���÷��� Ÿ��
		'backgroundImageUrl' => $unitData['backgroundImageUrl'], //��׶��� �̹���
		'questionName' => $unitData['unit_question'], //������
		'answerList' => $detailData, //�亯 ����
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	exit;
}
?>