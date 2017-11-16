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
		throw new Clib_Exception('올바른 접근이 아닙니다.');
	}

	//상품 목록 정렬 시 SQL Injection 방어
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

	//타이틀명
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

	//url 설정
	$defaultUrl = '../goods/goods_guidedSelling_list.php?guided_no='.$_GET['guided_no'];
	$pageUrl = array();
	$pageUrl['restartPageUrl'] = $defaultUrl.'&step=1';
	$pageUrl['nextPageUrl'] = $defaultUrl.'&step='.((int)$_GET['step']+1).'&'.implode("&", $hashtagParameter);
	array_pop($hashtagParameter);
	$pageUrl['prevPageUrl'] = $defaultUrl.'&step='.((int)$_GET['step']-1).'&'.implode("&", $hashtagParameter);

	//상품노출 목록수
	$guidedSellingDisplayPageNum = array(12, 20, 32, 48);

	$goodsHelper = Clib_Application::getHelperClass('front_goods');
	$params = array(
		'page' => Clib_Application::request()->get('page', 1), //페이지
		'page_num' => Clib_Application::request()->get('page_num', 12), //목록수
		'guidedSellingPage' => 'y', //가이디드 셀렝 페이지 여부
		'hashtag' => Clib_Application::request()->get('hashtagName'), //해시태그 명
		'sort' => Clib_Application::request()->get('sort', 'goods.regdt desc'), //정렬
		// GROUP BY 처리를 위해서 기존의 객체를 변경함
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
	//목록수
	$selected['page_num'][$params['page_num']] = "selected";
	$goodsCollection = $goodsHelper->getGoodsCollection($params);
	//상품목록
	$guidedSellingList = $goodsHelper->getGoodsCollectionArray($goodsCollection, null);
	//페이징 관련
	$pg = $goodsCollection->getPaging();
	//SNS
	if(is_object($hashtag)){
		$snsBtn = $hashtag->getSnsBtn('y');
	}

	if($guidedSelling_lastPage !== 'y'){
		$guidedData = $unitData = $detailData = array();
		$guidedData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
		if(!$guidedData['guided_no']){
			throw new Clib_Exception('DATA가 존재하지 않습니다.');
		}
		$unitData = $guidedSelling->getGuidedSellingUnitData($_GET['guided_no'], (int)$_GET['step']);
		if(!$unitData['unit_no']){
			throw new Clib_Exception('DATA가 존재하지 않습니다.');
		}
		$detailData = $guidedSelling->getGuidedSellingDetailData($unitData['unit_no']);
		if(count($detailData) < 1){
			throw new Clib_Exception('DATA가 존재하지 않습니다.');
		}

		$tpl->assign(array(
			'guidedSelling_backgroundColor' => $guidedData['guided_backgroundColor'], //배경색
			'displayType' => $unitData['unit_displayType'], //디스플레이 타입
			'backgroundImageUrl' => $unitData['backgroundImageUrl'], //백그라운드 이미지
			'questionName' => $unitData['unit_question'], //질문명
			'answerList' => $detailData, //답변 정보
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
		'guidedSellingTitle' => $guidedSellingTitle, //타이틀명
		'guidedSellingDisplayPageNum' => $guidedSellingDisplayPageNum, //상품노출 목록수
		'imageSize' => $cfg['img_s'], //상품 이미지 사이즈
		'guidedSellingList' => $guidedSellingList, //가이디드 셀링 상품 리스트
		'hashtagNameList' => $_GET['hashtagName'], //해시태그명
		'pg' => $pg, //페이징 관련
		'snsBtn' => $snsBtn, //SNS
		'guidedSelling_firstPage' => $guidedSelling_firstPage, //첫번째 페이지 체크
		'guidedSelling_lastPage' => $guidedSelling_lastPage, //마지막 페이지 체크
		'guided_no' => $_GET['guided_no'], //가이디드 셀링 번호
		'step' => $_GET['step'], //스텝
		'pageUrl' => $pageUrl, //페이지 Url
		'indicatorLoop' => $indicatorLoop, //스텝 지표
		'lastStep' => $lastStep, //마지막 스텝
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
?>