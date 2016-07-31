<?
class get_member_qna_order_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(array('ORD' => GD_ORDER), array('ordno','orddt','settleprice'));
		$builder->join(array('MB' => GD_MEMBER), 'ORD.m_no = MB.m_no', null);

		$builder->where('ORD.m_no > 0 AND ORD.m_no = ?', $param[m_no]);
		$builder->order('ORD.ordno DESC');

		$param['page_num'] = 5;
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}
}

?>