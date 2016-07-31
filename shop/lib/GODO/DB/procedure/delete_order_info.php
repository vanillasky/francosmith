<?
class delete_order_info extends GODO_DB_procedure {

	function execute() {

		$ordno = @func_get_arg(0);

		// 원 주문 데이터
		$param = $this->db->procedure('get_order_info', $ordno);

		// 주문 데이터 백업 (gd_order_del 테이블 - 정규화 되어 있지 않음)
		$columns = $this->db->desc(GD_ORDER_DEL);

		$_body = array();

		foreach($columns as $column) {
			if (isset($param[$column]))
				$_body[$column] = $param[$column];
		}

		if (sizeof($_body) > 0) {

			$builder = $this->db->builder()->insert();
			$builder->into(GD_ORDER_DEL)->set($_body);
			$builder->query();

			$this->db->query("delete from ".GD_ORDER." where ordno='$ordno'");

			### 통합 주문데이타 삭제
			$this->db->query("delete from ".GD_INTEGRATE_ORDER." where ordno='$ordno' AND channel = 'enamoo'");
			$this->db->query("delete from ".GD_INTEGRATE_ORDER_ITEM." where ordno='$ordno' AND channel = 'enamoo'");

			### 쿠폰내역이 있으면 삭제합니다.
			list($applysno) = $this->db -> fetch("select applysno  from ".GD_COUPON_ORDER." where ordno='$ordno'");

			if($applysno){
				$this->db->query("update ".GD_COUPON_APPLY." set status = '0' where sno='$applysno'");
				$this->db->query("delete from ".GD_COUPON_ORDER." where ordno='$ordno'");
			}

		}

		return true;

	}

}
?>