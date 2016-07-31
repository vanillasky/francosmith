<?php
/**
 * Clib_Collection_Goods_Maker
 * @author extacy @ godosoft development team.
 */
class Clib_Collection_Goods_Maker extends Clib_Collection_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_maker';

	/**
	 * {@inheritdoc}
	 */
	protected function construct()
	{
		$select = $this->getResource();
		$select->option('distinct');
		$select->columns('maker');
		$this->addFilter('maker', '', '>');
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
			$pairs[$data['maker']] = $data['maker'];
		}

		return $pairs;

	}

}
