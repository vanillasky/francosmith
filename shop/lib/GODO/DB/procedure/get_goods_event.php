<?
class get_goods_event extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
			->from(array('A'=>GD_GOODS_DISPLAY), null)
			->join(array('E'=>GD_EVENT), 'E.sno = SUBSTRING(A.mode,2)' , 'subject')
			->join(array('G'=>GD_GOODS), 'A.goodsno = G.goodsno', null)

			->where('A.mode like ?', $this->db->wildcard('e',1))
			->where('E.sdate <= ?', date('Ymd', G_CONST_NOW))
			->where('E.edate >= ?', date('Ymd', G_CONST_NOW))
		;

		$builder->where('G.goodsno = ?', $goodsno);

		return $this->db->utility()->getAll($builder);

	}

}
?>