<?php
class Clib_Collection_Goods_Link extends Clib_Collection_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_link';

	public function mergeIntoExistItem(Clib_Model_Goods_Link $category)
	{
		foreach ($this as $key => $_category) {
			if ($_category['goodsno'] == $category['goodsno'] && $_category['category'] == $category['category']) {

				$id = $_category->getId();
				$_category->setData($category->getData());
				$_category->setId($id);

				$this->addItem($_category, $key);

				return true;
			}
		}

		return false;
	}

}
