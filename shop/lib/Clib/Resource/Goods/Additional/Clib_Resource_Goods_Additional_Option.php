<?php
/**
 * Clib_Resource_Goods_Additional_Option
 * @author extacy@godosoft development team .
 */
class Clib_Resource_Goods_Additional_Option extends Clib_Resource_Abstract
{
	/**
	 *
	 * @param object $object
	 * @param object $goodsno
	 * @return
	 */
	public function initStatus($object, $goodsno)
	{
		$update = $this->getSqlBuilder('update');
		$update->from('gd_goods_add');
		$update->set(array('stats' => 0));

		$update->where('goodsno = ?', $goodsno);
		$update->query();
	}

	/**
	 *
	 * @param object $object
	 * @param object $goodsno
	 * @return
	 */
	public function deleteUnnecessary($object, $goodsno)
	{
		$delete = $this->getSqlBuilder('delete');
		$delete->from($object->getTableName());
		$delete->where('goodsno = ?', $goodsno);
		$delete->where('stats = 0');
		$delete->query();
	}

}
