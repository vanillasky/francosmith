<?php
include '../_header.php';
include '../lib/page.class.php';
include '../conf/config.pay.php';
$soldoutConfigPage = dirname(__FILE__) . '/../conf/config.soldout.php';
$snsConfigPage = dirname(__FILE__) . '/../conf/sns.cfg.php';
if(is_file($soldoutConfigPage)) include $soldoutConfigPage;
if(is_file($snsConfigPage)) include $snsConfigPage;

try {
	if(is_file('../lib/hashtag.class.php')){
		$hashtag = Core::loader('hashtag');
	}

	if(!$_GET['hashtag']){
		throw new Clib_Exception('해시태그가 지정되지 않았습니다.');
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
		}
	}

	$hashtagList = array();
	//해시태크 페이지 타이틀명
	$hashtagTitle = '#'.$_GET['hashtag'];
	//상품노출 목록수
	$hashtagDisplayPageNum = array(12, 20, 32, 48);

	$goodsHelper = Clib_Application::getHelperClass('front_goods');
	$params = array(
		'page' => Clib_Application::request()->get('page', 1), //페이지
		'page_num' => Clib_Application::request()->get('page_num', 12), //목록수
		'hashtagPage' => 'y', //해시태그 리스트 여부
		'hashtag' => Clib_Application::request()->get('hashtag'), //해시태그 명
		'sort' => Clib_Application::request()->get('sort', 'goods.goodsno'), //정렬
	);
	//목록수
	$selected['page_num'][$params['page_num']] = "selected";
	$goodsCollection = $goodsHelper->getGoodsCollection($params);
	//상품목록
	$hashtagList = $goodsHelper->getGoodsCollectionArray($goodsCollection, null);
	//페이징 관련
	$pg = $goodsCollection->getPaging();
	//SNS
	if(is_object($hashtag)){
		$snsBtn = $hashtag->getSnsBtn();
	}

	$tpl->assign(array(
		'hashtagTitle' => $hashtagTitle, //해시태그 페이지 타이틀명
		'hashtagDisplayPageNum' => $hashtagDisplayPageNum, //상품노출 목록수
		'imageSize' => $cfg['img_s'], //상품 이미지 사이즈
		'hashtagList' => $hashtagList, //해시태그 상품 리스트
		'pg' => $pg, //페이징 관련
		'snsBtn' => $snsBtn, //SNS
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
?>