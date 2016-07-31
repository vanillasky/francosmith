<?
class get_member_qna extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('Q' => GD_MEMBER_QNA));
		$builder->leftjoin(array('MB' =>GD_MEMBER), 'Q.m_no = MB.m_no', array('m_id','m_no','name'));

		$builder->where('Q.sno = ?', $sno);

		return $builder->fetch(1);

	}

}
?>