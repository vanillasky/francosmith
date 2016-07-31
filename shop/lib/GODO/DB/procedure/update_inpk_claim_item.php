<?
class update_inpk_claim_item extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$itmsno = @func_get_arg(1);
		$clmsno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(INPK_CLAIM_ITEM);
		$builder->set($param);
		$builder->where('itmsno = ?', $clmsno);

		if ($clmsno)
			$builder->where('clmsno = ?', $clmsno);

		return $builder->query();

	}
}

?>