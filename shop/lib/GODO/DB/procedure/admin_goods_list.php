<?
class admin_goods_list extends GODO_DB_procedure {

	protected function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(
			array('G'=>GD_GOODS)
			,array('goodsno','goodsnm','img_s','img_l','icon','open','regdt','runout','usestock','inpk_prdno','totstock','use_emoney','delivery_type','goods_delivery','color')
		);
		$builder->join(
			array('GO'=>GD_GOODS_OPTION), 'G.goodsno = GO.goodsno'
			,array('price','reserve','link')
		);

		$builder->leftjoin(
			array('GB'=>GD_GOODS_BRAND), 'G.brandno = GB.sno'
			,array('brandnm')
		);

		// 투데이샵 상품
		if ($param['todaygoods'])
			$builder->where('G.todaygoods = ?',$param['todaygoods']);

		// 검색어
		if ($param['sword']) {

			switch ($param['skey']) {
				case 'all' :
					$_columns = array('goodsnm','goodsno','goodscd','keyword');
					break;
				default :
					$_columns = array($param['skey']);
					break;
			}

			$builder->search(GD_GOODS, 'G.goodsno', $_columns, $param['sword']);
		}

		// 가격
		if ($param['price'][0] && $param['price'][1])
			$builder->where('GO.price BETWEEN ? AND ?', $param['price']);

		// 브랜드
		if ($param['brandno'])
			$builder->where('G.brandno = ?',$param['brandno']);

		// 브랜드 미지정 (admin/goods/link.php 에서 사용)
		if ($param['unbrand'] == 'Y')
			$builder->where('G.brandno = ?',0);

		// 등록일
		if ($param['regdt'][0] && $param['regdt'][1]) {
			$builder->where('G.regdt BETWEEN ? AND ?', array(
				Core::helper('Date')->min($param['regdt'][0]),
				Core::helper('Date')->max($param['regdt'][1]),
			));
		}

		// 출력여부
		if ((string)$param['open'] != '')
			$builder->where('G.open = ?',$param['open']);

		// 블로그연동
		if ($param['blog'])
			$builder->where('G.useblog = ?','y');

		// 상품 카테고리 (unlink : admin/goods/link.php 에서 사용, 분류가 연결되지 않은 상품 검색시)
		if ($param[cate] || $param[unlink] == 'Y') {
			$category = array_notnull($param[cate]);
			$category = array_pop($category);

			if ($category){
				$builder->leftjoin(array('LNK'=>GD_GOODS_LINK), 'G.goodsno = LNK.goodsno', null);

				if ($param[unlink] == 'Y') {
					$builder->where('ISNULL(LNK.category)');
				}
				else {
					$cate_helper = Core::loader('category');

					// 상품분류 연결방식 전환 여부에 따른 처리
					$builder->where(getCategoryLinkQuery('LNK.category', $category, 'where'));
				}
			}
		}

		// 재고량 xx 이하 검색
		if ((int)$param['minQuantity'] > 0) {
			$builder->where('GO.stock <= ?', (int)$param['minQuantity']);
		}

		// 아이콘
		if (sizeof($param['sicon'])) {
			if ($param['sicon']['custom'] == 1) {
				unset($param['sicon']['custom']);
				$_max = sizeof($r_myicon);

				$checked[sicon][custom] = "checked";
			}
			else {
				unset($param['sicon']['custom']);
				$_max = 8;
			}

			$subWhere = array();

			for ($i=0;$i<$_max;$i++) {
				if ($param['sicon'][$i] > 0) {
					$_bit = pow(2,$i);
					$subWhere[] = $builder->parse('(G.icon & ?) > 0', $_bit);
				}
			}

			if(count($subWhere)) {
				$builder->where( '('.implode(' OR ',$subWhere).')' );
			}

		}

		// 배송비
		if ($param[delivery_type] != '')
			$builder->where('G.delivery_type = ?',$param[delivery_type]);

		// 색상
		if ($param['searchColor']) {
			$arr_searchColor = explode("#", $param['searchColor']);
			$subWhere = array();
			foreach($arr_searchColor as $k => $v) {
				if($v) $subWhere[] = $builder->parse('G.color LIKE ?', $this->db->wildcard($v));
			}

			if(count($subWhere)) {
				$builder->where( '('.implode(' OR ',$subWhere).')' );
			}
		}

		// 제외상품
		if ($param['except_goodsno']) {
			if (!is_array($param['except_goodsno'])) $param['except_goodsno'] = array($param['except_goodsno']);
			$builder->where('G.goodsno NOT IN (?)',array($param['except_goodsno']));
		}

		// 정렬
		if ($param['sort']) {
			$_tmp = explode(' ',$param['sort']);
			$order_column = $_tmp[0];
			$order_direction = isset($_tmp[1]) ? $_tmp[1] : '';
		}
		else {
			$order_column = 'goodsno';
			$order_direction = 'desc';
		}

		// true : link = 1 인 옵션만 선택, false : 전체 옵션 선택
		if (! $param['multi_rows_option']) {
			$builder->where('GO.link = 1');
			$builder->group('G.goodsno');
		}

		// 모든 필드 선택
		if ($param['wild_card_select']) {
			$builder->reset('column')->columns(array('G.*','GO.*','GB.brandnm'));
		}

		switch ($order_column) {
			case 'goodsno' :
				$builder->order('G.goodsno '.$order_direction);
				break;
			case 'regdt' :
				$builder->order('G.regdt '.$order_direction);
				break;
			case 'goodsnm' :
				$builder->order('G.goodsnm '.$order_direction);
				break;
			case 'price' :
				$builder->order('GO.price '.$order_direction);
				break;
			case 'brandno' :
				$builder->order('G.brandno '.$order_direction);
				break;
			case 'maker' :
				$builder->order('G.maker '.$order_direction);
				break;
			case 'stock' :
				$builder->order('GO.stock '.$order_direction);
				break;
		}

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}
}

?>