<?
class admin_member_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_MEMBER);

		if ($param['skey'] && $param['sword']) {

			switch ($param['skey']) {
				case 'resno':
					$tmp = str_replace( "-", "", $param['sword'] );
					$builder->where('resno1 = ? AND resno2 = ?', array(
						md5(substr( $tmp, 0, 6 )),
						md5(substr( $tmp, 6, 7 )),
					));
					break;

				case 'all':
					$builder->search(GD_MEMBER, 'm_no', array('m_id','name','nickname','email','phone','mobile','recommid','company'), $param['sword']);
					break;

				default :
					$builder->search(GD_MEMBER, 'm_no', $param['skey'], $param['sword']);
					break;
			}
		}

		if ($param['sstatus']!='') $builder->where('status = ?', $param['sstatus']);
		if ($param['slevel']!='') $builder->where('level = ?', $param['slevel']);

		if ($param['ssum_sale'][0] != '' && $param['ssum_sale'][1] != '') {
			$builder->where('sum_sale between ? and ?', $param['ssum_sale']);
		}
		else if ($param['ssum_sale'][0] != '' && $param['ssum_sale'][1] == '') {
			$builder->where('sum_sale >= ?', $param['ssum_sale'][0]);
		}
		else if ($param['ssum_sale'][0] == '' && $param['ssum_sale'][1] != '') {
			$builder->where('sum_sale <= ?', $param['ssum_sale'][1]);
		}

		if ($param['semoney'][0] != '' && $param['semoney'][1] != '') {
			$builder->where('emoney between ? and ?', $param['semoney']);
		}
		else if ($param['semoney'][0] != '' && $param['semoney'][1] == '') {
			$builder->where('emoney >= ?', $param['semoney'][0]);
		}
		else if ($param['semoney'][0] == '' && $param['semoney'][1] != '') {
			$builder->where('emoney <= ?', $param['semoney'][1]);
		}

		if ($param['sregdt'][0] && $param['sregdt'][1]) {
			$builder->where('regdt BETWEEN ? AND ?', array(
				Core::helper('Date')->min($param['sregdt'][0]),
				Core::helper('Date')->max($param['sregdt'][1]),
			));
		}

		if ($param['slastdt'][0] && $param['slastdt'][1]) {
			$builder->where('last_login BETWEEN ? AND ?', array(Core::helper('Date')->min($param['slastdt'][0]),Core::helper('Date')->max($param['slastdt'][1])));
		}

		if ($param['sex']) $builder->where('sex = ?', $param['sex']);

		if ($param['sage']!=''){
			$age[] = date('Y') + 1 - $param['sage'];
			$age[] = $age[0] - 9;
			foreach ($age as $k => $v) $age[$k] = substr($v,2,2);
			if ($param['sage'] == '60') $builder->where('RIGHT(birth_year,2) <= ', $age[1]);
			else $builder->where('RIGHT(birth_year,2) between ? and ?', $age);
		}

		if ($param['scnt_login'][0] != '' && $param['scnt_login'][1] != '') $builder->where('cnt_login between ? and ?', $param['scnt_login']);
		else if ($param['scnt_login'][0] != '' && $param['scnt_login'][1] == '') $builder->where('cnt_login >= ?', $param['scnt_login'][0]);
		else if ($param['scnt_login'][0] == '' && $param['scnt_login'][1] != '') $builder->where('cnt_login <= ?', $param['scnt_login'][1]);

		if ($param['dormancy']){
			$dormancyDate	= date("Y-m-d",strtotime("-{$param['dormancy']} day"));
			$builder->where("last_login <= ?", $dormancyDate);
		}

		if ($param['mailing']) $builder->where("mailling = ?", $param['mailing']);
		if ($param['smsyn']) $builder->where("sms = ?", $param['smsyn']);

		if( $param['birthtype'] ) $builder->where("calendar = ?", $param['birthtype']);

		if( $param['birthdate'][0] ){
			if( $param['birthdate'][1] ){
				if(strlen($param['birthdate'][0]) > 4 && strlen($param['birthdate'][1]) > 4)
					$builder->where("concat(birth_year, birth) between ? and ?", $param['birthdate']);
				else
					$builder->where("birth between ? and ?", $param['birthdate']);
			}else{
				$builder->where("birth = ?", $param['birthdate'][0]);
			}
		}

		if( $param['marriyn'] ) $builder->where("marriyn = ?", $param['marriyn']);

		if( $param['marridate'][0] ){
			if( $param['marridate'][1] ){
				if(strlen($param['marridate'][0]) > 4 && strlen($param['marridate'][1]) > 4)
					$builder->where("marridate between ? and ?", $param['marridate']);
				else
					$builder->where("substring(marridate,5,4) between ? and ?", $param['marridate']);
			}else{
				$builder->where("substring(marridate,5,4) = ?", $param['marridate'][0]);
			}
		}

		// 회원가입 유입 경로
		if(is_array($param['inflow'])) foreach($param['inflow'] as $v) {
			if($inflow_where) $inflow_where .= " OR ";
			if($v) $inflow_where .= "inflow = '$v'";
		}
		if($inflow_where) $where[] = $inflow_where;

		# 메인에서 생일자 SMS 확인용
		if ($param['mobileYN'] == "y") $builder->where("mobile != ?", '');

		$builder->where("m_id != ?", 'godomall');

		if ($param['sort']) $builder->order($param['sort']);
		else $builder->order('regdt desc');

		// db 다운로드 시
		if ($param['download']) {

			if ($param['limitmethod'] == 'part') {
				$builder->limit($param['offset'], $param['rowcount']);
			}

			$result = $this->db->utility()->getAll($builder);
		}
		// 회원 목록
		else {

			if(!$param['page_num']) $param['page_num'] = 15;
			if(!$param['page']) $param['page'] = 1;

			$result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);
		}

		return $result;

	}

}
?>