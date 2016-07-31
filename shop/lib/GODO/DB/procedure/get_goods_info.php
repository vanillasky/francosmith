<?
class get_goods_info extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('G'=>GD_GOODS));
		$builder->join(array('GO'=>GD_GOODS_OPTION),'G.goodsno = GO.goodsno AND GO.link = 1');
		$builder->leftjoin(array('GB'=>GD_GOODS_BRAND),'G.brandno = GB.sno', 'brandnm');

		// brand, 카테고리
		if (is_array($goodsno)) {
			$builder->where('G.goodsno in (?)', array($goodsno));
			$result = $this->db->utility()->getAll($builder);
		}
		else {
			$builder->where('G.goodsno = ?', $goodsno);
			$result = $this->db->utility()->getOne($builder);
			$result = $result->current();
		}

		return $result;

	}

}
?>