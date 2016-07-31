<?
class update_inpk_claim extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$clmsno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(INPK_CLAIM);
		$builder->set($param);
		$builder->where('clmsno = ?', $clmsno);

		return $builder->query();

	}
}

?>