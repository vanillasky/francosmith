<?
class get_goods_option extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_GOODS_OPTION);
		$builder->where('sno = ?', $sno);

		return $builder->fetch();

	}
}

?>