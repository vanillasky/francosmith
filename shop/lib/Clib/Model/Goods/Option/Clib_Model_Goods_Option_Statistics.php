<?php
/**
 * Clib_Model_Goods_Option_Statistics
 * @author Class Generator by extacy @ godosoft development team.
 */
class Clib_Model_Goods_Option_Statistics extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'gos_option_sno';

	public function addOrder($ea)
	{
		$resource = $this->getResource();
		$resource->addOrder($this, $ea);
	}

	public function addRelease($ea)
	{
		$resource = $this->getResource();
		$resource->addRelease($this, $ea);
	}

	public function addCancel($ea)
	{
		$resource = $this->getResource();
		$resource->addCancel($this, $ea);
	}

	public function addReturn($ea)
	{
		$resource = $this->getResource();
		$resource->addReturn($this, $ea);
	}

}
