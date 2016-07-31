<?
class get_auction_ipay_orer_item_list extends GODO_DB_procedure {

	function execute() {

		$ordno = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(array('OI' => GD_ORDER_ITEM), array('sno','goodsnm','goodsno','opt1','opt2','addopt','ea','price','memberdc'));
		$builder->join(array('G' => GD_GOODS),'OI.goodsno = G.goodsno', array('optnm','img_s','img_l','longdesc'));

		$builder->where('OI.ordno = ?', $ordno);
		$builder->order('OI.sno');


		return $this->db->utility()->getAll($builder);

	}

}
?>