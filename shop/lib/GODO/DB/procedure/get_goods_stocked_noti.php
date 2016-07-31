<?
class get_goods_stocked_noti extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(array('NT' => GD_GOODS_STOCKED_NOTI));
		$builder->join(array('G' => GD_GOODS) , 'NT.goodsno = G.goodsno', 'goodsnm');

		$builder->where('NT.goodsno = ?', $param['goodsno']);
		$builder->where('NT.sended = ?', 0);

		if ($param['method'] != 'all') $builder->where('NT.sno IN (?)', array($param['chk']));

		$builder->limit(1000);

		return $this->db->utility()->getAll($builder);

	}

}
?>