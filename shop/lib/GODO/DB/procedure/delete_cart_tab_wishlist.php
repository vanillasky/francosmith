<?
class delete_cart_tab_wishlist extends GODO_DB_procedure {

	function execute() {

		$m_no = @func_get_arg(0);
		$idx = @func_get_arg(1);

		$builder = $this->db->builder()->select();
		$builder->from(array('WS'=>GD_MEMBER_WISHLIST),'sno');
		$builder->leftjoin(array('G'=>GD_GOODS),'WS.goodsno = G.goodsno',null);
		$builder->leftjoin(array('GO'=>GD_GOODS_OPTION),'WS.goodsno = GO.goodsno and WS.opt1=GO.opt1 and WS.opt2=GO.opt2',null);
		$builder->where('G.open = 1');
		$builder->where('WS.m_no = ?', $m_no);
		$builder->order('WS.sno DESC');

		$this->db->query( $builder->toString() );

		if ($this->db->dataSeek($idx)) {

			$row = $this->db->fetch();

			$builder = $this->db->builder()->delete();
			$builder->from(GD_MEMBER_WISHLIST);
			$builder->where('m_no = ?', $m_no);
			$builder->where('sno = ?', $row['sno']);

			return $builder->query();
		}

		return false;

	}

}
?>