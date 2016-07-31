<?
class get_member_qna_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
			->from(array('QNA'=>GD_MEMBER_QNA),array('sno', 'parent', 'itemcd', 'ordno', 'subject', 'contents', 'regdt', 'm_no', 'notice', 'email', 'ip'))
			->leftjoin(array('MB'=>GD_MEMBER),'QNA.m_no = MB.m_no',array('m_id','m_name' => 'name','mobile','phone','m_email'=>'email'));

		### 검색조건
		if ($param[skey] && $param[sword])
		{
			if ($param[skey]== 'all') $subwhere[] = "concat( subject, contents, ifnull(m_id, '') ) like '%$param[sword]%'";
			else $subwhere[] = "$param[skey] like '%$param[sword]%'";
		}
		if ($param[sitemcd] <> '' && $param[sitemcd] <> 'all') $subwhere[] = "itemcd='" . $param[sitemcd] . "'"; # 분류검색
		if ($param[sregdt][0] && $param[sregdt][1]) $subwhere[] = "QNA.regdt between date_format({$param[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$param[sregdt][1]},'%Y-%m-%d 23:59:59')";

		if (count($subwhere))
		{
			$parent = array();
			$res = $this->db->query( "select parent from ".GD_MEMBER_QNA." QNA left join ".GD_MEMBER." MB on QNA.m_no=MB.m_no ".$subtable." where " . implode(" and ", $subwhere) );
			while ( $row = $this->db->fetch( $res ) ) $parent[] = $row['parent'];
			$parent = array_unique ($parent);
			if ( count( $parent ) ) $builder->where("QNA.parent in ('" . implode( "','", $parent ) . "')");
			else $where[] = $builder->where("QNA.parent in ('0')");
		}

		## 정렬
		switch ($param[sort]){

			case "regdt asc":
				$builder->order('QNA.notice desc, QNA.regdt ASC');
				break;
			case "regdt desc":
				$builder->order('QNA.notice desc, QNA.regdt DESC');
				break;
			case "subject desc":
				$builder->order('QNA.notice desc, QNA.subject ASC');
				break;
			case "subject asc":
				$builder->order('QNA.notice desc, QNA.subject DESC');
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