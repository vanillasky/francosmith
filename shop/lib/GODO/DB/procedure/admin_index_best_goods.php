<?
class admin_index_best_goods extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$_start_date = strtotime( sprintf('-%d day', $param['range']) , G_CONST_NOW );

		$query = "select goodsno,count(goodsno) as cnt from ".GD_ORDER_ITEM." where istep in (1,2,3,4) and ordno > '".strtotime("-7 day")."000' group by goodsno order by cnt desc limit 3";

		$sub = $this->db->builder()->select();

		$sub
		->from(array('O'=>GD_ORDER),null)
		->join(array('OI'=>GD_ORDER_ITEM),'O.ordno = OI.ordno',null)
		->columns(array('OI.goodsno','cnt' => $this->db->expression('COUNT(OI.goodsno)')))
		->where('O.orddt > ?', date('Y-m-d H:i:s', $_start_date) )
		->where('OI.istep IN (?)', array(array(1,2,3,4)) )
		->group('OI.goodsno')
		->order('cnt desc');

		$builder = $this->db->builder()->select();

		$builder
		->from(array('ORD'=>$sub),null)
		->join(array('G'=>GD_GOODS),'ORD.goodsno = G.goodsno')
		->limit($param['limit']);

		return $this->db->utility()->getAll($builder);

	}

}
?>