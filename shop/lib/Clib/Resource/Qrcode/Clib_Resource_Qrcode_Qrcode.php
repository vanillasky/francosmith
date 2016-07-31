<?php
/**
 * Clib_Resource_Qrcode_Qrcode
 */
class Clib_Resource_Qrcode_Qrcode extends Clib_Resource_Abstract
{

	public function deleteGoodsCode($object, $id)
	{
		$delete = $this->getSqlBuilder('delete');
		$delete->from($object->getTableName());
		$delete->where('contsNo = ?', $id);
		$delete->where('qr_type = ?', 'goods');
		$delete->query();
		return $object;

	}

	public function loadGoodsCode($object, $id)
	{
		$alias = Clib_Application::getAlias($object->getClassName());

		$select = $this->getSqlBuilder();
		$select->from(array($alias => $object->getTableName()));

		$select->where(sprintf('`%s`.qr_type = ?', $alias), 'goods');
		$select->where(sprintf('`%s`.contsNo = ?', $alias), $id);

		if ($data = $select->fetch(1)) {
			$object->setData($data);
			$object->setOriginalData();

			$object->setLoaded(true);
			$object->setChanged(false);
		}
		else {
			$object->resetData();
		}

		return $object;

	}

	public function loadEventCode($object, $id)
	{
		$alias = Clib_Application::getAlias($object->getClassName());

		$select = $this->getSqlBuilder();
		$select->from(array($alias => $object->getTableName()));

		$select->where(sprintf('`%s`.qr_type = ?', $alias), 'event');
		$select->where(sprintf('`%s`.contsNo = ?', $alias), $id);

		if ($data = $select->fetch(1)) {
			$object->setData($data);
			$object->setOriginalData();

			$object->setLoaded(true);
			$object->setChanged(false);
		}
		else {
			$object->resetData();
		}

		return $object;

	}

}
