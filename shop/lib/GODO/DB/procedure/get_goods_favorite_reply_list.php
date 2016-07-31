<?
class get_goods_favorite_reply_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODS_FAVORITE_REPLY);

		if ($param['type'])
			$builder->where('customerType = ?', $param['type']);

		$builder->order('regdt desc');

		$param['page_num'] = !$param['page_num'] ? 10 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		$result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

		return $result;

	}
}

?>