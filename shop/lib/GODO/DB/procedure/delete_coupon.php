<?
class delete_coupon extends GODO_DB_procedure {

	function execute() {

		$couponcd = @func_get_arg(0);

		$query = "select * from ".GD_COUPON_APPLY." where couponcd = '$couponcd'";
		$res = $this->db->query($query);
		while($param =  $this->db -> fetch($res)) {

			$this->db->builder()->delete()->from(GD_COUPON_APPLYMEMBER)->where('applysno = ?', $param[sno])->query();
			$this->db->builder()->delete()->from(GD_COUPON_APPLY)->where('couponcd = ?', $param[couponcd])->query();
			$this->db->builder()->delete()->from(GD_COUPON_CATEGORY)->where('couponcd = ?', $param[couponcd])->query();
			$this->db->builder()->delete()->from(GD_COUPON_GOODSNO)->where('couponcd = ?', $param[couponcd])->query();
			$this->db->builder()->delete()->from(GD_COUPON)->where('couponcd = ?', $param[couponcd])->query();

		}

	}

}
?>