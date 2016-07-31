<?php
/**
 * Clib_Helper_Admin_Goods
 */
class Clib_Helper_Admin_Goods extends Clib_Helper_Front_Goods
{

	public function getGoodsCollection($params)
	{
		$collection = Clib_Application::getCollectionClass('admin_goods');
		$collection = $this->prepareGoodsCollection($collection, $params);
		$collection->load();

		return $collection;
	}

	public function prepareGoodsCollection($collection, $params)
	{

		$collection->setCurrentPage($params['page']);
		$collection->setPageSize($params['page_num']);

		if ($params['indicate'] == 'main') {
			// 상품 진열영역

			$collection->setDisplayAreaFilter($params['smain']);
		}
		else if ($params['indicate'] == 'event') {
			// 이벤트

			$collection->setEventFilter($params['sevent']);
		}
		else {
			// 기본 (검색)

			// 카테고리
			if ($category = $params['cate']) {
				$category = array_pop(array_notnull($category));
				$collection->setCategoryFilter($category, basename($_SERVER['PHP_SELF']) == 'adm_goods_manage_link.php' ? false : true );
			}

			// 키워드 검색
			if (($skey = $params['skey']) && ($sword = $params['sword'])) {

				$sword = trim($sword);

				if (strpos($sword, 'goodsno') !== 0 && preg_match('/^A([0-9]+)$/', $sword, $matches)) {
					$sword = (int) $matches[1];
				}

				$collection->setKeywordFilter($sword, $skey, 'like');
			}

			// 재고량 검색
			if ($stock = $params['stock']) {
				$collection->setStock($params['stock']);
			}

			// 등록일
			$collection->setRegdtFilter($params['regdt']);

			// 가격
			$collection->setPriceFilter($params['price']);

			// 대표가격
			$collection->setGoodsPriceFilter($params['goods_price']);

			// 상품진열여부
			$collection->setOpenFilter($params['open']);

			// 품절상품
			$collection->setSoldoutFilter($params['soldout']);

			// 브랜드
			$collection->setBrandFilter($params['brandno']);

			// 상품별 배송비
			$collection->setDeliveryTypeFilter($params['delivery_type']);

			// 할인여부
			$collection->setDiscountFilter($params['discount']);

			// 할인기간
			$collection->setDiscountRangeFilter($params['discount_range']);

			// 색상
			if ($params['searchColor']) {
				$collection->setColorFilter($params['searchColor']);
			}

			// 분류 미연결 상품
			if ($params['unlink'] == 'Y') {
				$collection->setCategoryUnlinkedFilter();
			}

			// 브랜드 미연결 상품
			if ($params['unbrand'] == 'Y') {
				$collection->setBrandUnlinkedFilter();
			}

			// 판매재고 검색
			$collection->setStockFilter($params['stock_amount'], $params['stock_type']);

			// 원산지
			$collection->setOriginFilter($params['origin']);

			// 아이콘
			$collection->setIconFilter((array)$params['sicon']);



		}

		// 정렬
		$collection->setOrder($params['sort']);

		return $collection;

	}

}
