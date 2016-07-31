<?php
/**
 * Clib_Model_Goods_Maker
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Goods_Maker extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'goodsno';

	public function getTableName()
	{
		return 'gd_goods';
	}

}
