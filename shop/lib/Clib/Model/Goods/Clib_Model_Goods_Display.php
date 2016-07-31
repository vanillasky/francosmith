<?php
/**
 * Clib_Model_Goods_Display
 */
class Clib_Model_Goods_Display extends Clib_Model_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'no';

	public function getMaxSortNum($mode = null)
	{
		return $this->getResource()->getMaxSortNum($this, $mode);
	}

}
