<?php
include '../_header.php';
include '../lib/page.class.php';
include '../conf/config.pay.php';
$soldoutConfigPage = dirname(__FILE__) . '/../conf/config.soldout.php';
$snsConfigPage = dirname(__FILE__) . '/../conf/sns.cfg.php';
$displayConfigPage = dirname(__FILE__) . '/../conf/config.display.php';
if(is_file($displayConfigPage)) include $displayConfigPage;
if(is_file($soldoutConfigPage)) include $soldoutConfigPage;
if(is_file($snsConfigPage)) include $snsConfigPage;

$jQueryPath = $cfg['rootDir'] . '/lib/js/jquery-1.11.3.min.js';

try {
	if(is_file('../lib/hashtag.class.php')){
		$hashtag = Core::loader('hashtag');
	}
	if(is_file('../lib/guidedSelling.class.php')){
		$guidedSelling = Core::loader('guidedSelling');
	}
	if(!$_GET['guided_no'] || !$_GET['step']){
		throw new Clib_Exception('�ùٸ� ������ �ƴմϴ�.');
	}

	//��ǰ ��� ���� �� SQL Injection ���
	if (class_exists('validation')) {
		$validation = new validation();
		if (method_exists($validation, 'check_goods_sort')) {
			if ($_GET['sort'] != null && $validation->check_goods_sort($_GET['sort']) === false) {
				$_GET['sort'] = null;
			}
		}
		if (method_exists($validation, 'check_digit')) {
			if ($_GET['page_num'] != null && $validation->check_digit($_GET['page_num']) === false) {
				$_GET['page_num'] = null;
			}
			if ($_GET['page'] != null && $validation->check_digit($_GET['page']) === false) {
				$_GET['page'] = null;
			}
			if ($validation->check_digit($_GET['step']) === false) {
				$_GET['step'] = null;
			}
			if ($validation->check_digit($_GET['guided_no']) === false) {
				$_GET['guided_no'] = null;
			}
		}
	}

	//Ÿ��Ʋ��
	$guidedSellingList = $hashtagParameter = array();
	foreach($_GET['hashtagName'] as $hashtagName){
		$guidedSellingTitle .= '#'.$hashtagName."&nbsp;";
		$hashtagParameter[] = "hashtagName[]=".urlencode($hashtagName);
	}

	$guidedSelling_firstPage = $guidedSelling_lastPage = '';
	$lastStep = 0;
	$lastStep = $guidedSelling->getLastStep($_GET['guided_no']);
	if((int)$_GET['step'] === 1){
		$guidedSelling_firstPage = 'y';
	}
	if((int)$_GET['step'] > (int)$lastStep){
		$guidedSelling_lastPage = 'y';
	}

	//url ����
	$defaultUrl = '../goods/goods_guidedSelling_list.php?guided_no='.$_GET['guided_no'];
	$pageUrl = array();
	$pageUrl['restartPageUrl'] = $defaultUrl.'&step=1';
	$pageUrl['nextPageUrl'] = $defaultUrl.'&step='.((int)$_GET['step']+1).'&'.implode("&", $hashtagParameter);
	array_pop($hashtagParameter);
	$pageUrl['prevPageUrl'] = $defaultUrl.'&step='.((int)$_GET['step']-1).'&'.implode("&", $hashtagParameter);

	//��ǰ���� ��ϼ�
	$guidedSellingDisplayPageNum = array(12, 20, 32, 48);

	$goodsHelper = Clib_Application::getHelperClass('front_goods');
	$params = array(
		'page' => Clib_Application::request()->get('page', 1), //������
		'page_num' => Clib_Application::request()->get('page_num', 12), //��ϼ�
		'guidedSellingPage' => 'y', //���̵�� ���� ������ ����
		'hashtag' => Clib_Application::request()->get('hashtagName'), //�ؽ��±� ��
		'sort' => Clib_Application::request()->get('sort', 'goods.regdt desc'), //����
		// GROUP BY ó���� ���ؼ� ������ ��ü�� ������
		'resetRelationShip' => array(
			'categories' => array(
				'modelName' => 'goods_link',
				'isCollection' => true,
				'foreignColumn' => 'goodsno',
				'deleteCascade' => true,
				'withoutGroup' => false,
			),
		),
	);
	//��ϼ�
	$selected['page_num'][$params['page_num']] = "selected";
	$goodsCollection = $goodsHelper->getGoodsCollection($params);
	//��ǰ���
	$guidedSellingList = $goodsHelper->getGoodsCollectionArray($goodsCollection, null);
	//����¡ ����
	$pg = $goodsCollection->getPaging();
	//SNS
	if(is_object($hashtag)){
		$snsBtn = $hashtag->getSnsBtn('y');
	}

	if($guidedSelling_lastPage !== 'y'){
		$guidedData = $unitData = $detailData = array();
		$guidedData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
		if(!$guidedData['guided_no']){
			throw new Clib_Exception('DATA�� �������� �ʽ��ϴ�.');
		}
		$unitData = $guidedSelling->getGuidedSellingUnitData($_GET['guided_no'], (int)$_GET['step']);
		if(!$unitData['unit_no']){
			throw new Clib_Exception('DATA�� �������� �ʽ��ϴ�.');
		}
		$detailData = $guidedSelling->getGuidedSellingDetailData($unitData['unit_no']);
		if(count($detailData) < 1){
			throw new Clib_Exception('DATA�� �������� �ʽ��ϴ�.');
		}

		$tpl->assign(array(
			'guidedSelling_backgroundColor' => $guidedData['guided_backgroundColor'], //����
			'displayType' => $unitData['unit_displayType'], //���÷��� Ÿ��
			'backgroundImageUrl' => $unitData['backgroundImageUrl'], //��׶��� �̹���
			'questionName' => $unitData['unit_question'], //������
			'answerList' => $detailData, //�亯 ����
		));
	}

	$indicatorLoop = array();
	for($i=1; $i<=6; $i++){
		if($i > (int)$lastStep+1){
			break;
		}

		if((int)$_GET['step'] === $i){
			$indicatorLoop[$i] = 'on';
		}
		else {
			$indicatorLoop[$i] = 'off';
		}
	}

	$tpl->assign(array(
		'guidedSellingTitle' => $guidedSellingTitle, //Ÿ��Ʋ��
		'guidedSellingDisplayPageNum' => $guidedSellingDisplayPageNum, //��ǰ���� ��ϼ�
		'imageSize' => $cfg['img_s'], //��ǰ �̹��� ������
		'guidedSellingList' => $guidedSellingList, //���̵�� ���� ��ǰ ����Ʈ
		'hashtagNameList' => $_GET['hashtagName'], //�ؽ��±׸�
		'pg' => $pg, //����¡ ����
		'snsBtn' => $snsBtn, //SNS
		'guidedSelling_firstPage' => $guidedSelling_firstPage, //ù��° ������ üũ
		'guidedSelling_lastPage' => $guidedSelling_lastPage, //������ ������ üũ
		'guided_no' => $_GET['guided_no'], //���̵�� ���� ��ȣ
		'step' => $_GET['step'], //����
		'pageUrl' => $pageUrl, //������ Url
		'indicatorLoop' => $indicatorLoop, //���� ��ǥ
		'lastStep' => $lastStep, //������ ����
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
?>