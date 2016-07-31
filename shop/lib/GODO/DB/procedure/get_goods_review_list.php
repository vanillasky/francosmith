<?
class get_goods_review_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
			->from(array('RV'=>GD_GOODS_REVIEW),array('sno','goodsno','subject','contents','point','regdt','emoney','name','parent','attach','notice','ip'))
			->leftjoin(array('MB'=>GD_MEMBER),'RV.m_no = MB.m_no',array('m_no','m_id','m_name' => 'name','level','phone','mobile','m_email'=>'email'));

		if($param['point']){
			switch($param['point']){
				case "all": break;
				default: $builder->where('RV.point = ?', $param['point']); break;
			}
		}

		if ($param[cate]) {
			$category = array_notnull($param[cate]);
			$category = array_pop($category);

			if ($category){

				$cate_helper = Core::loader('category');

				$builder->leftjoin(array('LNK'=>GD_GOODS_LINK), 'RV.goodsno = LNK.goodsno', null);

				// 상품분류 연결방식 전환 여부에 따른 처리
				$builder->where(getCategoryLinkQuery('LNK.category', $category, 'where'));

			}
		}

		if ($param[skey] && $param[sword]){
			if ( $param[skey]== 'goodnm' ||  $param[skey]== 'all' ){
				$tmp = array();
				$res = $this->db->query("select goodsno from ".GD_GOODS." where goodsnm like '%$param[sword]%'");
				while ($_data=$this->db->fetch($res)) $tmp[] = $_data[goodsno];
				if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "RV.goodsno in(" . implode( ",", $tmp ) . ")";
				else $goodnm_where = "false";
			}

			if ( $param[skey]== 'all' ){
				$builder->where("concat( RV.subject, RV.contents, ifnull(MB.m_id, ''), ifnull(MB.name, '') ) like '%$param[sword]%' or $goodnm_where ");
			}
			else if ( $param[skey]== 'goodnm' ) $builder->where($goodnm_where);
			else if ( $param[skey]== 'm_id' ) $builder->where("concat( ifnull(MB.m_id, ''), ifnull(MB.name, '') ) like '%$param[sword]%'");
			else {
				/*subject
				contents*/
				$builder->where("RV.$param[skey] like '%$param[sword]%'");
			}
		}

		if ($param['sregdt'][0] && $param['sregdt'][1]) {

			$builder->where("RV.regdt between ? and ?", array(
				Core::helper('Date')->min($param['sregdt'][0]),
				Core::helper('Date')->max($param['sregdt'][1]),
			));
		}


		switch ($param[sort]){

			case "regdt asc":
				$builder->order('RV.notice desc, RV.regdt ASC');
				break;
			case "regdt desc":
				$builder->order('RV.notice desc, RV.regdt DESC');
				break;
			case "point asc":
				$builder->order('RV.notice desc, RV.point asc');
				break;
			case "point desc":
				$builder->order('RV.notice desc, RV.point desc');
				break;
			case "subject desc":
				$builder->order('RV.notice desc, RV.subject ASC');
				break;
			case "subject asc":
				$builder->order('RV.notice desc, RV.subject DESC');
				break;
			default :
			case "1":
				$builder->order('RV._order ASC');
				break;
			case "2":
				$builder->order('RV.notice desc, RV.point desc');
				break;
		}

		if ($param['unlimited'] === true) {
			return $this->db->utility()->getAll($builder);
		}
		else {

			$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
			$param['page'] = !$param['page'] ? 1 : $param['page'];

			return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

		}

	}

}
?>