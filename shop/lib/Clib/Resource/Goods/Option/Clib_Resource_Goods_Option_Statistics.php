<?php
/**
 * Clib_Resource_Goods_Option_Statistics
 * @author Class Generator by extacy @ godosoft development team.
 */
class Clib_Resource_Goods_Option_Statistics extends Clib_Resource_Abstract
{

	public function addOrder($object, $ea)
	{
		$query = sprintf('INSERT INTO %s SET gos_option_sno = %d, gos_order = %d ON DUPLICATE KEY UPDATE gos_order = gos_order + VALUES(gos_order)', $object->getTableName(), $object->getId(), $ea);
		$this->db->query($query);
	}

	public function addRelease($object, $ea)
	{
		$query = sprintf('INSERT INTO %s SET gos_option_sno = %d, gos_release = %d ON DUPLICATE KEY UPDATE gos_release = gos_release + VALUES(gos_release)', $object->getTableName(), $object->getId(), $ea);
		$this->db->query($query);
	}

	public function addCancel($object, $ea)
	{
		$query = sprintf('INSERT INTO %s SET gos_option_sno = %d, gos_cancel = %d ON DUPLICATE KEY UPDATE gos_cancel = gos_cancel + VALUES(gos_cancel)', $object->getTableName(), $object->getId(), $ea);
		$this->db->query($query);
	}

	public function addReturn($object, $ea)
	{
		$query = sprintf('INSERT INTO %s SET gos_option_sno = %d, gos_return = %d ON DUPLICATE KEY UPDATE gos_return = gos_return + VALUES(gos_return)', $object->getTableName(), $object->getId(), $ea);
		$this->db->query($query);
	}

}
