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
		throw new Clib_Exception('�ؽ��±װ� �������� �ʾҽ��ϴ�.');
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
		}
	}

	$hashtagList = array();
	//�ؽ���ũ ������ Ÿ��Ʋ��
	$hashtagTitle = '#'.$_GET['hashtag'];
	//��ǰ���� ��ϼ�
	$hashtagDisplayPageNum = array(12, 20, 32, 48);

	$goodsHelper = Clib_Application::getHelperClass('front_goods');
	$params = array(
		'page' => Clib_Application::request()->get('page', 1), //������
		'page_num' => Clib_Application::request()->get('page_num', 12), //��ϼ�
		'hashtagPage' => 'y', //�ؽ��±� ����Ʈ ����
		'hashtag' => Clib_Application::request()->get('hashtag'), //�ؽ��±� ��
		'sort' => Clib_Application::request()->get('sort', 'goods.goodsno'), //����
	);
	//��ϼ�
	$selected['page_num'][$params['page_num']] = "selected";
	$goodsCollection = $goodsHelper->getGoodsCollection($params);
	//��ǰ���
	$hashtagList = $goodsHelper->getGoodsCollectionArray($goodsCollection, null);
	//����¡ ����
	$pg = $goodsCollection->getPaging();
	//SNS
	if(is_object($hashtag)){
		$snsBtn = $hashtag->getSnsBtn();
	}

	$tpl->assign(array(
		'hashtagTitle' => $hashtagTitle, //�ؽ��±� ������ Ÿ��Ʋ��
		'hashtagDisplayPageNum' => $hashtagDisplayPageNum, //��ǰ���� ��ϼ�
		'imageSize' => $cfg['img_s'], //��ǰ �̹��� ������
		'hashtagList' => $hashtagList, //�ؽ��±� ��ǰ ����Ʈ
		'pg' => $pg, //����¡ ����
		'snsBtn' => $snsBtn, //SNS
	));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
?>