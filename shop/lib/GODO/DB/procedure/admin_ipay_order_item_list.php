<?
class admin_ipay_order_item_list extends GODO_DB_procedure {

	function execute() {

		$ipaysno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('AI' => GD_AUCTIONIPAY_ITEM));
		$builder->leftjoin(array('G' => GD_GOODS),'AI.goodsno = G.goodsno', 'img_s');

		$builder->where('AI.ipaysno = ? ', $ipaysno);

		return $this->db->utility()->getAll($builder);;

	}
}

?>