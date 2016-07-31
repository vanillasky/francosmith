<?
class update_cashreceipt extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$crno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_CASHRECEIPT);
		$builder->set($param);
		$builder->where('crno = ?', $crno);

		return $builder->query();

	}

}
?>