<?php
/**
 * Clib_Model_Goods_Option_Preset
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Goods_Option_Preset extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function getSetName()
	{
		return $this->getData('title');
	}

	public function getSet()
	{
		$set = array();
		if ($this['optnm1'] || $this['opt1']) {
			$this['optnm1'] = $this['optnm1'] ? $this['optnm1'] : '可记1';
			$set[$this['optnm1']] = str_replace('^',',',$this['opt1']);
		}

		if ($this['optnm2'] || $this['opt2']) {
			$this['optnm2'] = $this['optnm2'] ? $this['optnm2'] : '可记2';
			$set[$this['optnm2']] = str_replace('^',',',$this['opt2']);
		}

		return $set;
	}

	public function getTableName()
	{
		return GD_DOPT;
	}

}
