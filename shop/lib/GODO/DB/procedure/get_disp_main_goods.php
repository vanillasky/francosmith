<?
class get_disp_main_goods extends GODO_DB_procedure {

	function execute() {

		global $cfg;

		$mode = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(
			array('L'=>GD_GOODS_DISPLAY)
			,array('mode','goodsno')
		);

		$builder->join(
			array('G'=>GD_GOODS), 'L.goodsno = G.goodsno'
			,array('goodsnm','img_s')
		);

		$builder->join(
			array('GO'=>GD_GOODS_OPTION), 'G.goodsno = GO.goodsno'
			,array('price')
		);

		$builder->where('GO.link = 1');

		if ($mode) {
			$builder->where('L.mode = ?', $mode);
		}

		if ($cfg['shopMainGoodsConf'] == 'E')
			$builder->where('L.tplSkin = ?', $cfg['tplSkinWork']);
		else
			$builder->where('L.tplSkin is null');

		//$builder->group('L.mode');

		$builder->option('distinct');

		$builder->order('L.sort');

		return $this->db->utility()->getAll($builder);


	}
}

?>