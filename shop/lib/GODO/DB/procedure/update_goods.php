<?
class update_goods extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$goodsno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_GOODS);
		$builder->set($param);
		$builder->where('goodsno = ?', $goodsno);

		if ($builder->query()) {

			// 색인 추출 및 갱신
			//$sc = $this->db->indexer();
			//$sc->generate(GD_GOODS, $goodsno, $param, array('keyword','goodsnm','goodscd','maker'));

			return true;
		}
		else {
			return false;
		}


	}
}

?>