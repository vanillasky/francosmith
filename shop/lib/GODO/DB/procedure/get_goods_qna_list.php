<?
class get_goods_qna_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
			->from(array('QNA'=>GD_GOODS_QNA),array('sno','parent','email','ip','subject','contents','regdt','name','m_no','goodsno','secret','notice'))
			->leftjoin(array('MB'=>GD_MEMBER),'QNA.m_no = MB.m_no',array('m_id','m_name' => 'name','mobile','phone','m_email'=>'email'));

		if ($param[cate]) {
			$category = array_notnull($param[cate]);
			$category = array_pop($category);

			if ($category){

				$cate_helper = Core::loader('category');

				$builder->leftjoin(array('LNK'=>GD_GOODS_LINK), 'QNA.goodsno = LNK.goodsno', null);

				// 상품분류 연결방식 전환 여부에 따른 처리
				$builder->where(getCategoryLinkQuery('LNK.category', $category, 'where'));

			}
		}

		if ($param['skey'] && $param[sword]){
			if ($param['skey']== 'goodnm' ||  $param['skey']== 'all'){
				$tmp = array();
				$res = $db->query("select goodsno from ".GD_GOODS." where goodsnm like '%$param[sword]%'");
				while ($param=$db->fetch($res))$tmp[] = $param[goodsno];
				if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "QNA.goodsno in(" . implode( ",", $tmp ) . ")";
				else $goodnm_where = "0";
			}

			if ($param['skey']== 'all') $subwhere[] = "( concat( subject, contents, ifnull(m_id, ''), ifnull(QNA.name, '') ) like '%$param[sword]%' or $goodnm_where )";
			else if ($param['skey']== 'goodnm') $subwhere[] = $goodnm_where;
			else if ($param['skey']== 'm_id') $subwhere[] = "concat( ifnull(m_id, ''), ifnull(QNA.name, '') ) like '%$param[sword]%'";
			else $subwhere[] = $param['skey']." like '%$param[sword]%'";
		}

		if ($param[sregdt][0] && $param[sregdt][1]) {
			$subwhere[] = "QNA.regdt between date_format({$param[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$param[sregdt][1]},'%Y-%m-%d 23:59:59')";
		}

		if (count($subwhere))
		{
			$parent = array();
			$res = $this->db->query( "select parent from ".GD_GOODS_QNA." QNA left join ".GD_MEMBER." MB on QNA.m_no=MB.m_no {$subtable} where " . implode(" and ", $subwhere) );
			while ( $row = $this->db->fetch( $res ) ) $parent[] = $row['parent'];
			$parent = array_unique ($parent);
			if ( count( $parent ) ) $builder->where("QNA.parent in ('" . implode( "','", $parent ) . "')");
			else $builder->where("QNA.parent in ('0')");
		}

		switch ($param[sort]){

			case "regdt asc":
				$builder->order('QNA.regdt ASC');
				break;
			case "regdt desc":
				$builder->order('QNA.regdt DESC');
				break;
			case "subject desc":
				$builder->order('QNA.subject ASC');
				break;
			case "subject asc":
				$builder->order('QNA.subject DESC');
				break;
			default :
				$builder->order('QNA._order ASC');
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