<?php
include '../_header.php';
include '../conf/config.pay.php';
$jQueryPath = $cfg['rootDir'] . '/lib/js/jquery-1.11.3.min.js';
$soldoutConfigPage = dirname(__FILE__) . '/../conf/config.soldout.php';
if(is_file($soldoutConfigPage)) include $soldoutConfigPage;

try {
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
		}
	}

	$hashtag = Core::loader('hashtag');
	$getParameter = $hashtag->getIframeWidgetUri($_SERVER['QUERY_STRING']);

	if(!$getParameter['hashtag']){
		throw new Clib_Exception('�ؽ��±װ� �������� �ʾҽ��ϴ�.');
	}
	if(!$getParameter['hashtagWidth']) $getParameter['hashtagWidth'] = 4; //���� ����
	if(!$getParameter['hashtagHeight']) $getParameter['hashtagHeight'] = 2; //���� ����
	if(!$getParameter['hashtagIframeWidth']) $getParameter['hashtagIframeWidth'] = 1000; // ���� iframe ������
	if(!$getParameter['hashtagImageWidth']) $getParameter['hashtagImageWidth'] = 150; // �̹��� ���� ������

	//�ҷ��� ��ǰ ��
	$goodsCount = (int)$getParameter['hashtagWidth'] * (int)$getParameter['hashtagHeight'];
	$getGoodsCount = ($goodsCount > 100) ? 100 : $goodsCount;

	$hashtagList = array();
	$goodsHelper = Clib_Application::getHelperClass('front_goods');
	$params = array(
		'page_num' => $getGoodsCount, //��ϼ�
		'hashtagPage' => 'y', //�ؽ��±� ����Ʈ ����
		'hashtag' => $getParameter['hashtag'], //�ؽ��±� ��
		'sort' => Clib_Application::request()->get('sort', 'goods.goodsno'), //����
	);
	$goodsCollection = $goodsHelper->getGoodsCollection($params);
	//��ǰ���
	$hashtagList = $goodsHelper->getGoodsCollectionArray($goodsCollection, null);

	$tpl->assign(array(
		'hashtagWidgetID' => $getParameter['hashtagWidgetID'], //�ؽ��±� iframe ID
		'hashtagWidth' => $getParameter['hashtagWidth'], //�ؽ��±� ���� ��ǰ��
		'hashtagIframeWidth' => $getParameter['hashtagIframeWidth'], //���� iframe ������
		'size' => $getParameter['hashtagImageWidth'], //�̹��� ���� ������
		'hashtagList' => $hashtagList, //�ؽ��±� ��ǰ ����Ʈ
		'jQueryPath' => $jQueryPath, //jQuery ���
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	echo $e->getMessage();
	exit;
}
?>