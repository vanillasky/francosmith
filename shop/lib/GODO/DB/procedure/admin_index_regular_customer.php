<?
class admin_index_regular_customer extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$_start_date = strtotime( sprintf('-%d day', $param['range']) , G_CONST_NOW );

		$sub = $this->db->builder()->select();

		$sub
		->from(GD_ORDER,null)
		->columns(array('m_no','cnt' => $this->db->expression('COUNT(m_no)'), 'price' => $this->db->expression('SUM(prn_settleprice)')))
		->where('step IN (?)', array( array(1,2,3,4) ))
		->where('orddt > ?', date('Y-m-d H:i:s', $_start_date) )
		->where('m_no > 0')
		->group('m_no')
		->order('cnt desc');

		$builder = $this->db->builder()->select();

		$builder
		->from(array('ORD'=>$sub))
		->join(array('MB'=>GD_MEMBER),'ORD.m_no = MB.m_no',array('name','m_id'))
		->limit($param['limit']);

		return $this->db->utility()->getAll($builder);

	}

}
?>