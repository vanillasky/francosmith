<?php
/**
 * Clib_Helper_Admin_Goods_Option
 */
class Clib_Helper_Admin_Goods_Option extends Clib_Helper_Admin_Goods
{
	public function getGoodsCollection($params)
	{
		$collection = Clib_Application::getCollectionClass('admin_goods_option');
		$collection = $this->prepareGoodsCollection($collection, $params);
		$collection->load();

		return $collection;
	}

}
