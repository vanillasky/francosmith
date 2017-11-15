<?php
include '../_header.php';
include '../conf/config.pay.php';
$jQueryPath = $cfg['rootDir'] . '/lib/js/jquery-1.11.3.min.js';
$soldoutConfigPage = dirname(__FILE__) . '/../conf/config.soldout.php';
if(is_file($soldoutConfigPage)) include $soldoutConfigPage;

try {
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
		}
	}

	$hashtag = Core::loader('hashtag');
	$getParameter = $hashtag->getIframeWidgetUri($_SERVER['QUERY_STRING']);

	if(!$getParameter['hashtag']){
		throw new Clib_Exception('해시태그가 지정되지 않았습니다.');
	}
	if(!$getParameter['hashtagWidth']) $getParameter['hashtagWidth'] = 4; //가로 개수
	if(!$getParameter['hashtagHeight']) $getParameter['hashtagHeight'] = 2; //세로 개수
	if(!$getParameter['hashtagIframeWidth']) $getParameter['hashtagIframeWidth'] = 1000; // 위젯 iframe 사이즈
	if(!$getParameter['hashtagImageWidth']) $getParameter['hashtagImageWidth'] = 150; // 이미지 가로 사이즈

	//불러올 상품 수
	$goodsCount = (int)$getParameter['hashtagWidth'] * (int)$getParameter['hashtagHeight'];
	$getGoodsCount = ($goodsCount > 100) ? 100 : $goodsCount;

	$hashtagList = array();
	$goodsHelper = Clib_Application::getHelperClass('front_goods');
	$params = array(
		'page_num' => $getGoodsCount, //목록수
		'hashtagPage' => 'y', //해시태그 리스트 여부
		'hashtag' => $getParameter['hashtag'], //해시태그 명
		'sort' => Clib_Application::request()->get('sort', 'goods.goodsno'), //정렬
	);
	$goodsCollection = $goodsHelper->getGoodsCollection($params);
	//상품목록
	$hashtagList = $goodsHelper->getGoodsCollectionArray($goodsCollection, null);

	$tpl->assign(array(
		'hashtagWidgetID' => $getParameter['hashtagWidgetID'], //해시태그 iframe ID
		'hashtagWidth' => $getParameter['hashtagWidth'], //해시태그 가로 상품수
		'hashtagIframeWidth' => $getParameter['hashtagIframeWidth'], //위젯 iframe 사이즈
		'size' => $getParameter['hashtagImageWidth'], //이미지 가로 사이즈
		'hashtagList' => $hashtagList, //해시태그 상품 리스트
		'jQueryPath' => $jQueryPath, //jQuery 경로
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	echo $e->getMessage();
	exit;
}
?>