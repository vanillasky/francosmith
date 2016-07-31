<?
class get_coupon_goodsno_list extends GODO_DB_procedure {

	function execute() {

		$couponcd = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('CG' => GD_COUPON_GOODSNO), array('goodsno'));
		$builder->leftjoin(array('G' => GD_GOODS)			,'CG.goodsno = G.goodsno', array('goodsnm','img_s'));
		$builder->leftjoin(array('GO' => GD_GOODS_OPTION)			,'CG.goodsno = GO.goodsno AND GO.link = 1', array('price'));

		$builder->where('CG.couponcd = ?', $couponcd);

		return $this->db->utility()->getAll($builder);

	}

}
?>
