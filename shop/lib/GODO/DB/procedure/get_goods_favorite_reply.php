<?
class get_goods_favorite_reply extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);
		$customerType = @func_get_arg(1);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODS_FAVORITE_REPLY);
		$builder->where('sno = ?', $sno);

		if ($customerType) {
			$builder->where('customerType = ?', $customerType);
		}

		return $builder->fetch(1);

	}

}
?>
