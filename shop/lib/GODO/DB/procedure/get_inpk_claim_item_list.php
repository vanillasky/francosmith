<?
class get_inpk_claim_item_list extends GODO_DB_procedure {

	function execute() {

		$clmsno = @func_get_arg(0);
		$itemsno = @func_get_arg(1);

		$builder = $this->db->builder()->select();
		$builder->set($param);
		$builder->from(array('CI'=>INPK_CLAIM_ITEM));
		$builder->leftjoin(array('OI'=>GD_ORDER_ITEM), 'CI.item_sno = OI.sno');

		if ($clmsno)
			$builder->where('CI.clmsno = ?', $clmsno);

		if ($itemsno)
			$builder->where('CI.itmsno = ?', $itemsno);

		return $this->db->utility()->getAll($builder);

	}
}

?>