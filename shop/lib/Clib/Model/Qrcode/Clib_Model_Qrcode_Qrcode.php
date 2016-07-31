<?php
/**
 * Clib_Model_Qrcode_Qrcode
 */
class Clib_Model_Qrcode_Qrcode extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function deleteGoodsCode($goodsno)
	{
		return $this->getResource()->deleteGoodsCode($this, $goodsno);
	}

	public function loadGoodsCode($goodsno)
	{
		return $this->getResource()->loadGoodsCode($this, $goodsno);
	}

	public function loadEventCode($eventno)
	{
		return $this->getResource()->loadEventCode($this, $eventno);
	}

}
