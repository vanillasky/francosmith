<?
class update_coupon extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$couponcd = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_COUPON);
		$builder->set($param);
		$builder->where('couponcd = ?', $couponcd);

		return $builder->query();

	}

}
?>