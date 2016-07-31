<?php
/**
 * Clib_Resource_Goods_Goods
 * @author extacy @ godosoft development team.
 */
class Clib_Resource_Goods_Goods extends Clib_Resource_Abstract
{
	public function getDiscountInformation($goodsno)
	{
		$query = "select * from gd_goods_discount where gd_goodsno = $goodsno";
		return $this->db->fetch($query, 1);
	}

}
