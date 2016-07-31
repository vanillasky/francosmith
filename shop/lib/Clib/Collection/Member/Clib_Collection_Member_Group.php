<?php
class Clib_Collection_Member_Group extends Clib_Collection_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'member_group';

	protected function construct()
	{
		$this->setOrder('level desc');
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
