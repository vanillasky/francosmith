<?

include "../_header.php";
include "../lib/page.class.php";
include "../conf/config.pay.php";

include "../conf/design.main.php";
@include "../conf/design_main.$cfg[tplSkin].php";
if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";
include dirname(__FILE__) . "/../conf/config.display.php";

try {
$r_page_num = array(20,40,60,80);
	if (!$cfg_step[4]['sort_type'] || $cfg_step[4]['sort_type'] == '1') {
		$goodsHelper   = Clib_Application::getHelperClass('front_goods');

		// 파라미터 설정
		$params = array(
			'page' => Clib_Application::request()->get('page', 1),
			'page_num' => Clib_Application::request()->get('page_num', $r_page_num[0]),
			'sort' => Clib_Application::request()->get('sort', 'goods_display.sort asc'),
			'mode' => 4,	// 4 번 그룹
			'tplSkin' => $cfg['shopMainGoodsConf'] == "E" ? $cfg['tplSkin'] : '',
		);

		if ($tpl->var_['']['connInterpark']) {
			
			$params['inpk_prdno'] = true;
		}

		// 상품 목록
		$goodsCollection = $goodsHelper->getGoodsCollection($params);

		$loop = $goodsHelper->getGoodsCollectionArray($goodsCollection);
		$pg = $goodsCollection->getPaging();
	} else {
		$mainAutoSort = Core::loader('mainAutoSort');
		$hashtag = Core::loader('hashtag');

		//현재 사용중인 테이블
		$mainAutoSort_useTable = $mainAutoSort->getUseTable($cfg_step[4]['sort_type']);
		//최대 상품수
		$mainAutoSort_sortLimit = $mainAutoSort->getSortLimit();
		//해시태그 자동진열
		if((string)$cfg_step[4]['sort_type'] === '5'){
			$sortNum = $mainAutoSort_useTable.".auto_goodsno DESC";
		}
		else {
			$sortNum = "sort".$cfg_step[4]['sort_type']."_".$cfg_step[4]['select_date'];
		}
		$orderby = 'order by '.$sortNum;

		list($add_table, $add_where, $add_group) = $mainAutoSort->getSortTerms($cfg_step[4], $sortNum);

		$page_num = $_GET['page_num'] ? $_GET['page_num'] : $r_page_num[0];
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$_pg = new Page($page,$page_num);
		list($cnt) = $db->fetch("SELECT COUNT(*) FROM (SELECT ".GD_GOODS.".goodsno FROM ".$mainAutoSort_useTable." {$add_table} WHERE ".GD_GOODS.".open AND link {$add_where} {$add_order} LIMIT ".$mainAutoSort_sortLimit.") gd_sort_cnt");

		$query = "
			SELECT
				*
			FROM (
				SELECT
					".GD_GOODS.".*,".GD_GOODS_OPTION.".stock,".GD_GOODS_OPTION.".price,".GD_GOODS_OPTION.".consumer
				FROM
					".$mainAutoSort_useTable."
					{$add_table}
				WHERE
					".GD_GOODS.".open
					AND link
					{$add_where}
					{$add_group} {$orderby}
				LIMIT ".$mainAutoSort_sortLimit."
			) gd_sort
			".($_GET['sort'] ? "ORDER BY ".(strstr($_GET['sort'],'reserve') ? str_replace('reserve','goods_reserve',$_GET['sort']) : $_GET['sort']) : "")."
			LIMIT ".(($page-1)*$page_num).",".$page_num."
		";

		$res = $db->query($query);
		while($data = $db->fetch($res,1)){
			$data['goods_view_url'] = "../goods/goods_view.php?goodsno=".$data['goodsno'];
			$data['icon'] = setIcon($data['icon'],$data['regdt']);
			if ($displayCfg['displayType'] === 'discount') {
				$discountModel = '';
				$goodsDiscount = '';
				if ($data['use_goods_discount'] === '1') {
					$discountModel = Clib_Application::getModelClass('Goods_Discount');
					$goodsDiscount = $discountModel->getDiscountAmountSearch($data);
				}
				if ($goodsDiscount) {
					$data['oriPrice'] = $data['price'];
					$data['goodsDiscountPrice'] = $data['price'] - $goodsDiscount;
					$data['special_discount_amount'] = $goodsDiscount;
				}
				else {
					$data['oriPrice'] = '0';
					$data['goodsDiscountPrice'] = $data['price'];
				}
			}
			
			$data['hashtag'] = $hashtag->getHashtagList('goodsList', array('goodsno' => $data['goodsno']));
			
			$_loop[] = $data;
		}
		$loop = $_loop;
		$pg->page['navi'] = $_pg->getNavi($cnt);
		$pg->recode['total'] = $cnt;
	}

	$selected['page_num'][Clib_Application::request()->get('page_num')] = "selected";

	$tpl->assign(array(
				'loop'	=> $loop,
				'pg'	=> $pg,
				'slevel' =>  Clib_Application::session()->getMemberLevel(),
				));
	$tpl->print_('tpl');
}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
