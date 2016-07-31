<?
class delete_goods_favorite_reply extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$customerType = @func_get_arg(2);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_GOODS_FAVORITE_REPLY)->where('sno = ?', $sno)->where('customerType = ?', $customerType);

		return $builder->query();

	}

}
?>