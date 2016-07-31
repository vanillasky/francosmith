<?
class save_order_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		if (isset($param['items'])) {
			$data_items = $param['items'];
			unset($param['items']);
		}
		else {
			$data_items = array();
		}

		// 주문 정보 저장
		$builder = $this->db->builder()->insert();
		$builder->into(GD_ORDER);
		$builder->set($param);

		if ($builder->query()) {
			foreach($data_items as $item) {

				$builder = $this->db->builder()->insert();
				$builder->into(GD_ORDER_ITEM);
				$builder->set($item);
				$builder->query();

			}
			return true;
		}
		else {
			return false;
		}

	}

}
?>