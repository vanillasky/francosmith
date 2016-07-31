<?
class get_goods_brand extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODS_BRAND);
		$builder->where('sno = ?', $sno);

		return $builder->fetch();

	}

}
?>