<?php
class Clib_Model_Goods_Hashtag extends Clib_Model_Abstract
{
	protected $idColumnName = 'sno';

	/**
	 *
	 * @return
	 */
	public function getTableName()
	{
		return 'gd_hashtag';
	}
}
