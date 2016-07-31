<?php
/**
 * Clib_Resource_Goods_Display
 * @author extacy @ godosoft development team.
 */
class Clib_Resource_Goods_Display extends Clib_Resource_Abstract
{

	public function getMaxSortNum($object, $mode = null)
	{

		$select = $this->getSqlBuilder('select');
		$select->from($object->getTableName(), 'MAX(sort) as num');

		if (! is_null($mode)) {
			$select->where('mode = ?', $mode);
		}

		$tmp = $select->fetch();

		return $tmp['num'];

	}

}
