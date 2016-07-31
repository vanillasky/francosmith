<?php
/**
 * Clib_Collection_Admin_Goods
 * @author extacy @ godosoft development team.
 */
class Clib_Collection_Admin_Goods extends Clib_Collection_Goods_Abstract
{

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setCategoryFilter($value, $wildcard = true)
	{
		if ($value != '') {
			// 상품분류 연결방식 전환 여부에 따른 처리
			$filterCondition1	= $value;
			$filterCondition2	= '';
			if ($wildcard && _CATEGORY_NEW_METHOD_ === false) {
				$filterCondition1	= Clib_Application::database()->wildcard($value, 1);
				$filterCondition2	= 'like';
			}
			$this->addFilter('goods_link.category', $filterCondition1, $filterCondition2);
		}

	}

	/**
	 *
	 * @return
	 */
	public function setCategoryUnlinkedFilter()
	{

		$model = Clib_Application::getModelClass('goods');
		$model->setRelationShip('categories', array(
			'modelName' => 'goods_link',
			'isCollection' => true,
			'foreignColumn' => 'goodsno',
			'deleteCascade' => true,
			'joinType' => 'left',
		));

		$this->setValueModel($model);

		$this->delFilter('goods_link.category');
		$this->addFilter('goods_link.category', null, '<=>');
	}

	/**
	 *
	 * @param object $keyword
	 * @param object $column [optional]
	 * @return
	 */
	public function setKeywordFilter($keyword, $column = null)
	{
		if (is_null($column)) {
			return false;
		}

		$this->addFilter($column, Clib_Application::database()->wildcard($keyword), 'like');

	}

	/**
	 *
	 * @param array
	 * @return
	 */
	public function setRegdtFilter($value)
	{
		if ($value[0] || $value[1]) {
			if ($value[0]) {
				$this->addFilter('goods.regdt', Core::helper('date')->min($value[0]), '>=');
			}

			if ($value[1]) {
				$this->addFilter('goods.regdt', Core::helper('date')->max($value[1]), '<=');
			}
		}
	}

	/**
	 *
	 * @param array
	 * @return
	 */
	public function setPriceFilter($value)
	{

		if ($value[0] && $value[1]) {
			$this->addRangeFilter('goods_option.price', $value);
		}
		else if ($value[0]) {
			$this->addFilter('goods_option.price', $value[0], '>=');
		}
		else if ($value[1]) {
			$this->addFilter('goods_option.price', $value[1], '<=');
		}
	}

	/**
	 *
	 * @param array
	 * @return
	 */
	public function setGoodsPriceFilter($value)
	{

		if ($value[0] && $value[1]) {
			$this->addRangeFilter('goods.goods_price', $value);
		}
		else if ($value[0]) {
			$this->addFilter('goods.goods_price', $value[0], '>=');
		}
		else if ($value[1]) {
			$this->addFilter('goods.goods_price', $value[1], '<=');
		}
	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setOpenFilter($value)
	{
		if ($value != '') {
			$this->addFilter('goods.open', $value);
		}
	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setSoldoutFilter($value)
	{
		if ($value === '0') {
			// 품절 상품 제외
			$this->addExpressionJoinFilter("!( goods.runout = 1 OR ( goods.usestock = 'o' AND goods.usestock IS NOT NULL AND goods.totstock < 1))");
		}
		else if ($value === '1') {
			// 품절 상품만
			$this->addExpressionJoinFilter(" ( goods.runout = 1 OR ( goods.usestock = 'o' AND goods.totstock < 1))");
		}
		else {
			// 전체
		}

	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setBrandFilter($value)
	{
		if ($value != '') {
			$this->addFilter('goods.brandno', $value);
		}
	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setColorFilter($value)
	{
		$expressions = array();

		foreach (explode('#', $value) as $color) {
			if (empty($color))
				continue;
			$expressions[] = sprintf(' goods.color like \'%s\' ', Clib_Application::database()->wildcard($color));
		}

		if (sizeof($expressions)) {
			$this->addExpressionFilter(implode(' or ', $expressions));
		}

	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setDisplayAreaFilter($value)
	{
		$this->addFilter('goods_display.mode', $value);

	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setEventFilter($value)
	{
		$this->addFilter('goods_display.mode', 'e' . $value);
	}

	/**
	 *
	 * @return
	 */
	public function setBrandUnlinkedFilter()
	{
		$this->delFilter('goods.brandno');
		$this->addFilter('goods.brandno', 0);
	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setDeliveryTypeFilter($value)
	{
		if (!is_null($value) && $value != '') {
			$this->delFilter('goods.delivery_type');
			$this->addFilter('goods.delivery_type', $value);
		}

	}

	public function setDiscountFilter($value)
	{
		switch ((int) $value) {
			case 1 :
				// 할인
				$this->addFilter('goods_discount.gd_sno', '', '>');
				break;
			case 2 :
				// 할인상품 제외
				$this->addFilter('goods_discount.gd_sno', null, 'is');
				break;
		}
	}

	public function setDiscountRangeFilter($value)
	{
		if ($value[0] && $value[1]) {
			$time = Core::helper('date')->min($value[0], false);
			$this->addFilter('goods_discount.gd_start_date', $time, '>=');

			$time = Core::helper('date')->max($value[1], false);
			$this->addFilter('goods_discount.gd_end_date', $time, '<=');
		} else if ($value[0]) {
			$time = Core::helper('date')->min($value[0], false);
			$this->addFilter('goods_discount.gd_start_date', $time, '>=');
		} else if ($value[1]) {
			$time = Core::helper('date')->max($value[1], false);
			$this->addFilter('goods_discount.gd_end_date', $time, '<=');
		}
	}

	public function setStockFilter($value, $type = 'product')
	{
		$stock = array_map("intval", $value);

		if ($stock[0] || $stock[1]) {

			$this->addFilter('usestock', 'o');

			// 판매재고 전체
			if ($type == 'product') {

				if ($stock[0]) {
					$this->addFilter('totstock', $stock[0], '>=');
				}

				if ($stock[1]) {
					$this->addFilter('totstock', $stock[1], '<=');
				}

			}
			// 옵션별 판매재고
			else if ($type == 'item') {

				$this->addFilter('goods_option.go_is_deleted', 1, '<>');

				if ($stock[0]) {
					$this->addFilter('goods_option.stock', $stock[0], '>=');
				}

				if ($stock[1]) {
					$this->addFilter('goods_option.stock', $stock[1], '<=');
				}
			}

		}
	}

	public function setStock($value)
	{
		$stock = $value;
		if ($stock) {
			$this->addFilter('goods_option.stock', $stock, '<=');
		}
	}

	/**
	 *
	 * @param object $value
	 * @return
	 */
	public function setOriginFilter($value)
	{
		if ($value != '') {
			$this->addFilter('goods.origin', $value);
		}
	}

	public function setIconFilter($icons)
	{
		if (!is_array($icons) || sizeof($icons) < 1) {
			return ;
		}

		$expression = array();

		foreach ($icons as $icon) {
			$expression[] = "( goods.icon & $icon ) > 0";
		}

		$this->addExpressionFilter( implode(' OR ', $expression) );

	}

}
