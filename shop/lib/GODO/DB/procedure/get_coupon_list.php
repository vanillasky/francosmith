<?
class get_coupon_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(array('CP'=>GD_COUPON));
		$builder->leftjoin(array('CA'=>GD_COUPON_APPLY), 'CP.couponcd=CA.couponcd', array('cnt'=>$this->db->expression('count(CA.sno)')));

		if ($param[cate]){
			$category = array_notnull($param[cate]);
			$category = $category[count($category)-1];
		}

		if($param[goodstype] != null && $param[goodstype] != 'a'){

			$builder->where('CP.goodstype = ?' , $param[goodstype]);

			if($param[goodstype]){

				$subWhere = array();

				if($category){
					$builder->leftjoin(array('CC'=>GD_COUPON_CATEGORY), 'CP.couponcd=CC.couponcd', null);
					$subWhere[] = $builder->parse('CC.category LIKE ?', $this->db->wildcard($category,1));
				}

				if($param[gword]){
					if($param[gkey] != 'goodsno'){
						$res = $this->db->query("select goodsno from ".GD_GOODS." where $param[gkey] like '%$param[gword]%'");
						while($param = $this->db->fetch($res,1)) $arr[] = $param[goodsno];
						$subWhere[] = $builder->parse('CG.goodsno in (?)', array($arr));
					}else{
						$subWhere[] = $builder->parse('CG.goodsno = ?', $param[gword]);
					}

					$builder->leftjoin(array('CG'=>GD_COUPON_GOODSNO), 'CP.couponcd = CG.couponcd', null);
				}

				if(count($subWhere)) {
					$builder->where( '('.implode(' OR ',$subWhere).')' );
				}

			}
		}

		if ($param[sword]){
			$t_skey = ($param[skey]=="all") ? "concat( CP.couponcd, CP.coupon )" : $param[skey];
			$builder->where($t_skey.' like ?' , $this->db->wildcard($param[sword]));
		}

		if( $param[ability] ){
			$builder->where('CP.ability IN (?)' , array($param[ability]));
		}

		if( $param[coupontype] ){
			$builder->where('CP.coupontype IN (?)' , array($param[coupontype]));
		}

		if($param[regdt][0] && $param[regdt][1]){

			if($param[dtkind] == 'sddate') {
				$builder->where(
					'(CP.sdate <= ? AND CP.edate >= ? AND CP.priodtype = ?) OR CP.priodtype = ?',
					array(
						Core::helper('Date')->min($param[regdt][0]),
						Core::helper('Date')->max($param[regdt][1]),
						'0',
						'1',
					)
				);
			}
			else {

				$builder->where(
					sprintf('CP.%s between ? AND ?',$param[dtkind]),
					array(
						Core::helper('Date')->min($param[regdt][0]),
						Core::helper('Date')->max($param[regdt][1]),
					)
				);
			}

		}

		$builder->group('CP.couponcd');
		$builder->order('CP.regdt desc');

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}
?>