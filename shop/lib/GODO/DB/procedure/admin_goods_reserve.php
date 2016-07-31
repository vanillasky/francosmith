<?
class admin_goods_reserve extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		/*
		 * 가져오는 데이터 가 아래와 같음
		 */
		return $this->db->procedure('admin_goods_price', $param);

	}
}

?>