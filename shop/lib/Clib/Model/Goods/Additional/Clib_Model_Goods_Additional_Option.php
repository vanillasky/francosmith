<?php
/**
 * Clib_Model_Goods_Additional_Option
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Goods_Additional_Option extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function initStatus($goodsno)
	{
		return $this->getResource()->initStatus($this, $goodsno);
	}

	public function deleteUnnecessary($goodsno)
	{
		return $this->getResource()->deleteUnnecessary($this, $goodsno);
	}

	public function getTableName()
	{
		return GD_GOODS_ADD;
	}

}
