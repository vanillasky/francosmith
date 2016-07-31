<?
class get_cart_tab_wishlist extends GODO_DB_procedure {

	function execute() {

		$m_no = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('WS'=>GD_MEMBER_WISHLIST));
		$builder->leftjoin(array('G'=>GD_GOODS),'WS.goodsno = G.goodsno',array('goodsnm','img' => 'img_s'));
		$builder->leftjoin(array('GO'=>GD_GOODS_OPTION),'WS.goodsno = GO.goodsno and WS.opt1=GO.opt1 and WS.opt2=GO.opt2',array('price','reserve'));
		$builder->where('G.open = 1');
		$builder->where('WS.m_no = ?', $m_no);
		$builder->order('WS.sno DESC');

		return $this->db->utility()->getAll($builder);

	}

}
?>