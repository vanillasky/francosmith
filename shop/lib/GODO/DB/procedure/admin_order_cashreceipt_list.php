<?
class admin_order_cashreceipt_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(
			array('CSRCT' => GD_CASHRECEIPT)
		)
		->leftjoin(
			 array('ORD' => GD_ORDER)
			,'CSRCT.ordno = ORD.ordno'
			,array('step','step2','o_ordno' => 'ordno')
		)
		->order('CSRCT.crno DESC')
		;

		if ($param['skey'] == 'certno' && strlen($param['sword']) == 13)
		{
			$certno_encode = encode(substr($param['sword'],6,7),1);
			$certno = substr($param['sword'],0,6);

			$builder->where('CSRCT.certno = ?', $certno);
			$builder->where('CSRCT.certno_encode = ?', $certno_encode);
		}
		else if ($param['sword']) {
			$builder->where($param['skey'].' like ?', $this->db->wildcard($param['sword']));
		}

		if ($param['regdt'][0] && $param['regdt'][1]) {
			$builder->where('CSRCT.regdt between ? AND ?', array(Core::helper('Date')->min($param['regdt'][0]),Core::helper('Date')->max($param['regdt'][1])));
		}


		if ($param['status']){
			$builder->where('CSRCT.status IN (?)', array($param['status']));
		}
		if ($param['singly'] == 'Y') $builder->where('CSRCT.singly = ?', 'Y');
		else if ($param['singly'] == 'N') $builder->where('CSRCT.singly != ?', 'Y');

		$param['page_num'] = 20;

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}

?>