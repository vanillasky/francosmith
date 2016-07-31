<?

include "../_header.php";
include "../lib/page.class.php";
include "../conf/config.pay.php";
@include "../conf/qr.cfg.php";
if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";
include dirname(__FILE__) . "/../conf/config.display.php";
	

try {
	### 리스트 템플릿 기본 환경변수
	if (!$size) $size	= $cfg[img_s];
	
	$goodsHelper   = Clib_Application::getHelperClass('front_goods');

	// 이벤트
	$eventModel = $goodsHelper->getEventModel(Clib_Application::request()->get('sno'));
	if (!$eventModel->hasLoaded()) {
		throw new Clib_Exception('존재하지 않는 이벤트입니다.');
	}

	if (! $eventModel->isStarted()) {
		throw new Clib_Exception('준비중인 이벤트입니다.');
	}
	else if ($eventModel->isFinished()) {
		throw new Clib_Exception('마감된 이벤트입니다.');
	}

	// 카테고리, 브랜드
	if (Clib_Application::request()->get('category') != '') {
		$categoryModel = $goodsHelper->getCategoryModel(Clib_Application::request()->get('category'));
		$category = $categoryModel->getId();
	}
	$brandModel = $goodsHelper->getBrandModel(Clib_Application::request()->get('brandno'));
	$brandno = $brandModel->getId();

	$eventModel['page_num'] = $eventModel['page_num'] ? explode(",",$eventModel['page_num']) : array(12,20,32,48);

	// 파라미터 설정
	$params = array(
		'page' => Clib_Application::request()->get('page', 1),
		'page_num' => Clib_Application::request()->get('page_num', $eventModel['page_num'][0]),
		'category' => Clib_Application::request()->get('category'),
		'brandno' => Clib_Application::request()->get('brandno'),
		'mode' => 'e' . $eventModel->getId(),
		'sort' => Clib_Application::request()->get('sort', 'goods_display.sort asc'),
	);

	if ($tpl->var_['']['connInterpark']) {
		$params['inpk_prdno'] = true;
	}

	// 상품 목록
	$goodsCollection = $goodsHelper->getGoodsCollection($params);

	// 상품이 등록된 카테고리
	$r_cate = $eventModel->getCategoryCollection()->toArray();

	// 상품이 등록된 브랜드
	$r_brand = $eventModel->getBrandCollection()->toArray();

	// qrcode
	$eventModel['qrcode'] = $goodsHelper->getEventQrCodeHtml($eventModel);

	$tpl->assign($eventModel->getData());
	$tpl->assign(array(
		'pg' => $goodsCollection->getPaging(),
		'size' => $size,
		'loop' => $goodsHelper->getGoodsCollectionArray($goodsCollection, $categoryModel),
		'slevel' => Clib_Application::session()->getMemberLevel(),
	));
	$tpl->print_('tpl');

}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}


