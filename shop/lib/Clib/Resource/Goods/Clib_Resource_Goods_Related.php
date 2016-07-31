<?php
/**
 * Clib_Resource_Goods_Related
 * @author extacy @ godosoft development team.
 */
class Clib_Resource_Goods_Related extends Clib_Resource_Abstract
{

	public function deleteAll($object, $id)
	{
		$delete = $this->getSqlBuilder('delete');
		$delete->from($object->getTableName());
		$delete->where('goodsno = ?', $id);
		$delete->query();

		return $object;
	}

	public function getSort($object, $id)
	{

		$select = $this->getSqlBuilder();
		$select->from($object->getTableName(), 'MAX(sort) AS sort');
		$select->where('goodsno = ?', $id);
		$tmp = $select->fetch();

		return is_null($tmp['sort']) ? 0 : $tmp['sort'] + 1;

	}

}
