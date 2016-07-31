<?
class update_faq extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$itemcd = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_FAQ);
		$builder->set($param);
		$builder->where('sno = ?', $sno);

		if ($itemcd)
			$builder->where('itemcd = ?', $itemcd);

		return $builder->query();

	}

}
?>