<?
class delete_goods extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$this->db->delete(GD_GOODS)->where('goodsno = ?', $goodsno)->query();
		$this->db->delete(GD_GOODS_ADD)->where('goodsno = ?', $goodsno)->query();
		$this->db->delete(GD_GOODS_DISPLAY)->where('goodsno = ?', $goodsno)->query();
		$this->db->delete(GD_GOODS_LINK)->where('goodsno = ?', $goodsno)->query();
		$this->db->delete(GD_GOODS_OPTION)->where('goodsno = ?', $goodsno)->query();
		$this->db->delete(GD_MEMBER_WISHLIST)->where('goodsno = ?', $goodsno)->query();

		// 색인 삭제
		$sc = $this->db->indexer();
		$sc->generate(GD_GOODS, $goodsno);

	}

}
?>