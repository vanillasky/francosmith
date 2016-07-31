<?
class get_order_info extends GODO_DB_procedure {

	function execute() {

		$ordno = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
			->from(array('ORD'=>GD_ORDER))
			->leftjoin(array('MB'=>GD_MEMBER), 'ORD.m_no = MB.m_no', array('m_id','m_no','name'))
			->leftjoin(array('BNK'=>GD_LIST_BANK), 'ORD.bankAccount = BNK.sno', array('bank','account'))
			->leftjoin(array('DLC'=>GD_LIST_DELIVERY), 'ORD.deliveryno = DLC.deliveryno', array('deliverycomp'))
			->where('ORD.ordno = ? ', $ordno);

		return $builder->fetch(1);

	}

}
?>