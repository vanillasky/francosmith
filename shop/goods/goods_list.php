<?
if (!preg_match('/^[0-9]*$/', $_GET['category']))
	exit ;

include "../_header.php";
include "../lib/page.class.php";
include "../conf/config.pay.php";
if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";
include dirname(__FILE__) . "/../conf/config.display.php";

try {

	$goodsHelper   = Clib_Application::getHelperClass('front_goods');

	// 카테고리
	$categoryModel = $goodsHelper->getCategoryModel(Clib_Application::request()->get('category'));

	// template_ 에서 global 변수로 사용하기 때문에 설정 함.
	$_GET['category'] = $category = $categoryModel->getId();

	if (!$categoryModel->hasLoaded()) {
		throw new Clib_Exception('분류페이지에 카테고리가 지정되지 않았습니다.');
	}

	// 권한 체크
	if (!$categoryModel->checkPermission(Clib_Application::session()->getMemberLevel())) {
		throw new Clib_Exception('이용 권한이 없습니다.\\n회원등급이 낮거나 회원가입이 필요합니다.');
	}

	// 카테고리 진열 허용 여부 체크
	if (!Clib_Application::session()->isAdmin() && getCateHideCnt($categoryModel->getId()) > 0) {
		throw new Clib_Exception('해당분류는 진열이 허용된 분류가 아닙니다.');
	}

	// 카테고리 상품 목록 설정
	$lstcfg = $categoryModel->getConfig();

	// 파라미터 설정
	$params = array(
		'page' => Clib_Application::request()->get('page', 1),
		'page_num' => Clib_Application::request()->get('page_num', $lstcfg['page_num'][0]),
		'keyword' => Clib_Application::request()->get('keyword'),
		'sort' => Clib_Application::request()->get('sort', $categoryModel->getSortColumnName()),
		'category' => $categoryModel->getId(),
	);

	if ($tpl->var_['']['connInterpark']) {
		$params['inpk_prdno'] = true;
	}

	// 상품 목록
	$goodsCollection = $goodsHelper->getGoodsCollection($params);

	$selected['page_num'][$params['page_num']] = "selected";

	#####크리테오######
	$criteo = new Criteo();
	if ($criteo->begin()) {
		$criteo->get_list($goodsHelper->getIdsArray($goodsCollection));
		$systemHeadTagEnd .= $criteo->scripts;
		$tpl->assign('systemHeadTagEnd', $systemHeadTagEnd);
	}
	###################

	// 상품분류 연결방식 전환 여부에 따른 처리
	$whereArr	= getCategoryLinkQuery('CMGL0.category', Clib_Application::request()->get('category'));

	// 카테고리 총 상품개수 for paging
	$query = " SELECT ";
	$query.= " COUNT(".$whereArr['distinct']." CMGG0.goodsno) AS __CNT__ ";
	$query.= " FROM ".GD_GOODS." AS CMGG0 ";
	$query.= " INNER JOIN ".GD_GOODS_LINK." AS CMGL0 ON CMGG0.goodsno = CMGL0.goodsno ";
	$query.= " WHERE  (CMGL0.hidden = '0') ";
	$query.= " and ".$whereArr['where'];
	$query.= " and (CMGG0.open = '1') ";

	// 품절 상품 제외
	if ($cfg_soldout['exclude_category']) {
		$query.= "and !( CMGG0.runout = 1 OR (CMGG0.usestock = 'o' AND CMGG0.usestock IS NOT NULL AND CMGG0.totstock < 1) ) ";
	}

	//DB Cache 사용 141030
	$dbCache = Core::loader('dbcache')->setLocation('goodslist');

	if (!$out = $dbCache->getCache($query)) {
  		$totalCount = $db->fetch($query); // 전체 레코드
		if ($totalCount && $dbCache) $dbCache->setCache($query, $totalCount);
  	} else {
  		$totalCount = $out;
  	}

	// 페이징 처리
	$offset[0] = $params['page'];
	$offset[1] = $params['page_num'];
	$total_count = $totalCount['__CNT__'];
	if ($total_count % $offset[1]) {
		$totalpage = (int)($total_count / $offset[1]) + 1;
	}
	else {
		$totalpage = $total_count / $offset[1];
	}

	// 페이징
	$pg = new Page($offset[0], $offset[1]);
	$pg->recode['total'] = $total_count;
	$pg->page['total'] = $totalpage;
	$pg->idx = $pg->recode['total'] - $pg->recode['start'];
	$pg->setNavi($tpl2 = '');
	$pg->query = $query;

	$tpl->assign(array(
		'pg' => $pg,
		'loopM' => $goodsHelper->getGoodsCollectionArray($goodsCollection, $categoryModel),
		'lstcfg' => $lstcfg,
		'slevel' => Clib_Application::session()->getMemberLevel(),
	));
	$tpl->print_('tpl');

}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
