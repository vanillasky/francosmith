<?
class get_coupon extends GODO_DB_procedure {

	function execute() {

		$couponcd = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_COUPON);

		$builder->where('couponcd = ?', $couponcd);

		return $builder->fetch();

	}

}
?>