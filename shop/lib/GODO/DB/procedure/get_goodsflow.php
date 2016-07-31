<?
class get_goodsflow extends GODO_DB_procedure {

	function execute() {

		$UniqueCd = @func_get_arg(0);
		$status = @func_get_arg(1);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODSFLOW);

		$builder->where('UniqueCd = ?', $UniqueCd);

		if ($status)
			$builder->where('status = ?', $status);

		return $builder->fetch();

	}
}

?>