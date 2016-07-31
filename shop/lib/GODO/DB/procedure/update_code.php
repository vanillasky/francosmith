<?
class update_code extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$groupcd = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_CODE);
		$builder->set($param);
		$builder->where('sno = ?', $sno);

		if ($groupcd)
			$builder->where('groupcd = ?', $groupcd);

		return $builder->query();

	}
}

?>