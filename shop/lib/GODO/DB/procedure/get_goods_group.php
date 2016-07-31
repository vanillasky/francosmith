<?
class get_goods_group extends GODO_DB_procedure {

	function execute() {

		global $cfg_soldout, $cfg, $tpl;

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('GD' => GD_GOODS_DISPLAY),null);
		$builder->join(array('G' => GD_GOODS)			,'GD.goodsno = G.goodsno');
		$builder->join(array('GO' => GD_GOODS_OPTION)	,'GD.goodsno = GO.goodsno AND GO.link = 1', array('price','reserve','consumer','stock','opt1','opt2'));
		$builder->leftjoin(array('GB' => GD_GOODS_BRAND),'G.brandno = GB.sno', 'brandnm');

		$builder->where('GD.mode = ?', $param['mode']);
		$builder->where('G.open = 1');

		if ($tpl->var_['']['connInterpark']) $builder->where('G.inpk_prdno != ?','');

		if( $cfg['shopMainGoodsConf'] == "E" ){
			$builder->where('GD.tplSkin = ?',$cfg['tplSkin']);
		}else{
			$builder->where('GD.tplSkin is null or GD.tplSkin = ?','');
		}
		// 품절 상품 제외
		if ($cfg_soldout['exclude_main']) {
			$builder->where('!( G.runout = 1 OR (G.usestock = ? AND G.totstock < 1) )', 'o');
		}
		// 제외시키지 않는 다면, 맨 뒤로 보낼지를 결정
		else if ($cfg_soldout['back_main']) {
			$_GET[sort] = "`soldout` ASC, ".$_GET[sort];

			$builder->columns(array(
				'soldout' => $this->db->expression('IF (G.runout = 1 , 1, IF (G.usestock = \'o\' AND G.totstock = 0, 1, 0))')
			));
		}

		$builder->order($_GET[sort]);
		$builder->group('GD.goodsno');

		return $this->db->utility()->getPaging($builder, $_GET[page_num], $_GET[page]);

	}

}
?>
