<?php
/**
 * Clib_Collection_Goods_Origin
 * @author extacy @ godosoft development team.
 */
class Clib_Collection_Goods_Origin extends Clib_Collection_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_origin';

	/**
	 * {@inheritdoc}
	 */
	protected function construct()
	{
		$select = $this->getResource();
		$select->option('distinct');
		$select->columns('origin');
		$this->addFilter('origin', '', '>');
	}

	/**
	 *
	 * @param int $sno
	 * @return
	 */
	public function setCategoryFilter($sno)
	{
		$this->addFilter('sno', $sno);
	}

	/**
	 *
	 * @param object $data
	 * @return
	 */
	public function getIdNamePair($datas)
	{
		$pairs = array();

		foreach ($datas as $data) {
			$pairs[$data['origin']] = $data['origin'];
		}

		return $pairs;

	}

}
