<?php
/**
 * Clib_Resource_Goods_Option
 * @author extacy @ godosoft development team.
 */
class Clib_Resource_Goods_Option extends Clib_Resource_Abstract
{

	// override;
	public function save($object)
	{
		$builder = $this->getSqlBuilder();
		$builder->from($object->getTableName());

		if ($object->hasLoaded()) {
			$format = sprintf('%s = ?', $object->getIdColumnName());
			$builder->update();
			$builder->where($format, $object->getId());
		}
		else {
			$builder->insert();
		}

		$builder->set($object->getChangedData());
		$builder->query();

		return $object;

	}

}
