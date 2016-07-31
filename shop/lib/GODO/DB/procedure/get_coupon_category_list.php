<?
class get_coupon_category_list extends GODO_DB_procedure {

	function execute() {

		$couponcd = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_COUPON_CATEGORY, array('category'));
		$builder->order('category');

		$builder->where('couponcd = ?', $couponcd);

		return $this->db->utility()->getAll($builder);

	}

}
?>
