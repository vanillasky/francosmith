<?php
class Clib_Collection_Goods_Brand extends Clib_Collection_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_brand';

	protected function construct()
	{
		$this->setOrder('sno', 'asc');
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
			$pairs[$data['brandnm']] = $data['sno'];
		}

		return $pairs;

	}

}
