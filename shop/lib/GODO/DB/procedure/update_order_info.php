<?
class update_order_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$ordno = @func_get_arg(1);

		// 결제로그는 기존 결제로그와 병합
		if(isset($param['settlelog']))
		{
			$param['settlelog'] = $this->db->expression('CONCAT(IF(`settlelog` IS NULL, \'\', `settlelog`), ?)', $param['settlelog']);
		}

		$builder = $this->db->builder()->update();
		$builder->from(GD_ORDER);
		$builder->set($param);

		$builder->where('ordno = ?', $ordno);

		return $builder->query();

	}

}
?>