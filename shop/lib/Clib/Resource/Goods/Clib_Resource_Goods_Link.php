<?php
/**
 * Clib_Resource_Goods_Link
 * @author extacy @ godosoft development team.
 */
class Clib_Resource_Goods_Link extends Clib_Resource_Abstract
{

	public function deleteExclude($object, $goodsno, $categories)
	{
		foreach ($categories as $category) {
			$_categories[] = $category['category'];
		}

		$delete = $this->getSqlBuilder('delete');
		$delete->from($object->getTableName());
		$delete->where('goodsno = ?', $goodsno);
		$delete->where('category NOT IN (?)', array($_categories));

		$delete->query();
	}

}
