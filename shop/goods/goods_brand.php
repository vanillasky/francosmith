<?
if(!preg_match('/^[0-9]*$/',$_GET['brand'])) exit;

$rtm[] = microtime();
include "../_header.php";
include "../lib/page.class.php";
include "../conf/config.pay.php";
if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";
include dirname(__FILE__) . "/../conf/config.display.php";
$rtm[] = microtime();

try {

	$goodsHelper = Clib_Application::getHelperClass('front_goods');

	//
	$brandModel = $goodsHelper->getBrandModel(Clib_Application::request()->get('brand'));

	### 환경변수 호출, 브랜드 명 assign.
	$lstcfg = $brandModel->getConfig();
	$lstcfg['brandnm'] = $brandModel->getBrandnm();

	// 파라미터 설정
	$params = array(
		'page' => Clib_Application::request()->get('page', 1),
		'page_num' => Clib_Application::request()->get('page_num', $lstcfg['page_num'][0]),
		'keyword' => Clib_Application::request()->get('keyword'),
		'sort' => Clib_Application::request()->get('sort', 'goods_link.sort asc'),
		'brandno' => $brandModel->getId(),
	);

	// GROUP BY 처리를 위해서 기존의 객체를 변경함
	$params['resetRelationShip'] = array(
		'categories' => array(
			'modelName' => 'goods_link',
			'isCollection' => true,
			'foreignColumn' => 'goodsno',
			'deleteCascade' => true,
			'withoutGroup' => false,
		),
	);

	if ($tpl->var_['']['connInterpark']) {
		$params['inpk_prdno'] = true;
	}

	// 상품 목록
	$goodsCollection = $goodsHelper->getGoodsCollection($params);

	$tpl->assign(array(
		'pg' => $goodsCollection->getPaging(),
		'loopM' => $goodsHelper->getGoodsCollectionArray($goodsCollection, $brandModel),
		'lstcfg' => $lstcfg,
	));
	$tpl->print_('tpl');

}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
