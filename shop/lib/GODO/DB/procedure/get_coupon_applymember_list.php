<?
class get_coupon_applymember_list extends GODO_DB_procedure {

	function execute() {

		$applysno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('CMB' => GD_COUPON_APPLYMEMBER), array('m_no'));
		$builder->leftjoin(array('MB' => GD_MEMBER)			,'CMB.m_no = MB.m_no', array('m_id','name'));

		$builder->where('CMB.applysno = ?', $applysno);

		return $this->db->utility()->getAll($builder);

	}

}
?>
