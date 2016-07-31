<?
class admin_main_state extends GODO_DB_procedure {

	function execute() {

		$adminMainState = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		# 날짜 설정
		$m7Date	= date('Y-m-d', strtotime('-7 day', G_CONST_NOW) );
		$m2Date	= date('Y-m-d', strtotime('-2 day', G_CONST_NOW) );
		$m1Date	= date('Y-m-d', strtotime('-1 day', G_CONST_NOW) );
		$eDate	= date('Y-m-d', G_CONST_NOW);
		$mDate	= date('Y-m-01', G_CONST_NOW);

		# Where 문
		$_whereStr	= " and date between '".$m7Date."' and '".$eDate."' group by orddate order by null ";
		$_whereStrM	= " and date between '".$mDate."' and '".$eDate."' ";

		$whereStr4	= " and regdt between '".$m7Date."' and '".$eDate."' group by orddate order by null ";
		$whereStr5	= " and date_format(day,'%Y-%m-%d') between '".$m7Date."' and '".$eDate."' group by orddate order by null ";

		$whereStrM4	= " and regdt between '".$mDate."' and '".$eDate."' ";
		$whereStrM5	= " and date_format(day,'%Y-%m-%d') between '".$mDate."' and '".$eDate."' ";

		$_table_name = 'main_state_data';

		// 조회 기준별 데이터 생성
		$_tmp_table = array();

		foreach($adminMainState AS $v){

			if ($v['chk'] == 'on') {

				switch ($v['code']) {
					case 'main01' :
					case 'main03' :
						$_tmp_table['cdt'] = true;
						break;
					case 'main04' :
						$_tmp_table['confirmdt'] = true;
						break;
					case 'main02' :
					case 'main05' :
						$_tmp_table['orddt'] = true;
						break;
				}
			}
		}

		$columns = array_keys($_tmp_table);
		foreach ($columns as $column) {

			// 최근 3개월 데이터만 갱신
			$query = "
			INSERT INTO main_state_data

			SELECT
				'$column',
				DATE_FORMAT($column,'%Y-%m-%d') as _date,
				COUNT(ordno),
				SUM(prn_settleprice),
				step,
				step2
			FROM ".GD_ORDER."

			WHERE $column > DATE_SUB(NOW(), INTERVAL 3 MONTH)

			GROUP BY _date, step, step2
			ORDER BY NULL

			ON DUPLICATE KEY UPDATE
				cnt = values(cnt),
				prn_settleprice = values(prn_settleprice)
			";
			$this->db->query($query);
		}

		//
		foreach($adminMainState AS $mKey => $mVal){

			# 매출액 (원)
			if($mVal['code'] == "main01" && $mVal['chk'] == "on"){

				$strSQL = "
					select date as orddate, sum(prn_settleprice) as value  from ".$_table_name."
					where type='cdt' and step > 0 and step2 = 0 ".$_whereStr;
				$res = $this->db->query($strSQL);

				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select sum(prn_settleprice) from ".$_table_name." where type='cdt' and step > 0 and step2 = 0".$_whereStrM);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select sum(prn_settleprice) from ".$_table_name." where type='cdt' and step > 0 and step2 = 0");
			}

			# 주문건수 (건)
			if($mVal['code'] == "main02" && $mVal['chk'] == "on"){

				$strSQL = "
					select date as orddate, sum(cnt) as value from ".$_table_name."
					where type='orddt' ".$_whereStr;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select SUM(cnt) from ".$_table_name." where type='cdt' ".$_whereStrM);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select SUM(cnt) from ".$_table_name." where type='cdt'");
			}

			# 입금확인 (건)
			if($mVal['code'] == "main03" && $mVal['chk'] == "on"){

				$strSQL = "
					select date as orddate, sum(cnt) as value  from ".$_table_name."
					where type='cdt' and step = 1 and step2 = 0 ".$_whereStr;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select sum(cnt) from ".$_table_name." where type='cdt' and step = 1 and step2 = 0".$_whereStrM);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select sum(cnt) from ".$_table_name." where type='cdt' and step = 1 and step2 = 0");
			}

			# 배송완료 (건)
			if($mVal['code'] == "main04" && $mVal['chk'] == "on"){

				$strSQL = "
					select date as orddate, sum(cnt) as value from ".$_table_name."
					where step = 4 and step2 = 0 ".$_whereStr;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select sum(cnt) from ".$_table_name." where type='confirmdt' and step = 4 and step2 = 0".$_whereStrM);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select sum(cnt) from ".$_table_name." where type='confirmdt' and step = 4 and step2 = 0");
			}

			# 취소 / 환불 / 반품 (건)
			if($mVal['code'] == "main05" && $mVal['chk'] == "on"){

				$strSQL = "
					select date as orddate, sum(cnt) as value from ".$_table_name."
					where type='orddt' and step2 >= 40 and step2 < 50 ".$_whereStr;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select sum(cnt) from ".$_table_name." where type='orddt' and step2 >= 40 and step2 < 50".$_whereStrM);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select sum(cnt) from ".$_table_name." where type='orddt' and step2 >= 40 and step2 < 50");
			}

			# 상품후기 (건)
			if($mVal['code'] == "main06" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(sno) as value from ".GD_GOODS_REVIEW."
					where sno=parent ".$whereStr4;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select count(sno) from ".GD_GOODS_REVIEW." where sno=parent".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select count(sno) from ".GD_GOODS_REVIEW." where sno=parent");
			}

			# 상품문의 (건)
			if($mVal['code'] == "main07" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(sno) as value from ".GD_GOODS_QNA."
					where sno=parent ".$whereStr4;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select count(sno) from ".GD_GOODS_QNA." where sno=parent".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select count(sno) from ".GD_GOODS_QNA." where sno=parent");
			}

			# 1:1문의 (건)
			if($mVal['code'] == "main08" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(sno) as value from ".GD_MEMBER_QNA."
					where sno=parent ".$whereStr4;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select count(sno) from ".GD_MEMBER_QNA." where sno=parent".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select count(sno) from ".GD_MEMBER_QNA." where sno=parent");
			}

			# 회원가입 (명)
			if($mVal['code'] == "main09" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(m_no) as value from ".GD_MEMBER."
					where 1 ".$whereStr4;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select count(m_no) from ".GD_MEMBER." where 1 ".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select count(m_no) from ".GD_MEMBER);
			}

			# 방문자수 (명)
			if($mVal['code'] == "main10" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(day,'%Y-%m-%d') as orddate, uniques as value from ".MINI_COUNTER."
					where 1 ".$whereStr5;
				$res = $this->db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $this->db->fetch("select sum(uniques) from ".MINI_COUNTER." where 1 ".$whereStrM5);
				# 전체
				list( $allData['allcnt'] ) = $this->db->fetch("select uniques from ".MINI_COUNTER." where day = '0'");
			}

			if($mVal['chk'] == "on"){
				while ($param=$this->db->fetch($res)){

					# 2일전
					if($param['orddate'] == $m2Date)	$mainState[$mVal['code']][0] = $param['value'];
					# 1일전
					if($param['orddate'] == $m1Date)	$mainState[$mVal['code']][1] = $param['value'];
					# 오늘
					if($param['orddate'] == $eDate)	$mainState[$mVal['code']][2] = $param['value'];
					# 일주일
					if($param['orddate'] >= $m7Date && $param['orddate'] <= $eDate)	$mainState[$mVal['code']][3] = $mainState[$mVal['code']][3] + $param['value'];
					# 30일
					# $mainState[$mVal['code']][4] = $mainState[$mVal['code']][4] + $param['value'];
				}

				# 한달
				$mainState[$mVal['code']][4] = $allData['monthcnt'];
				# 전체
				$mainState[$mVal['code']][5] = $allData['allcnt'];
			}

			unset($allData['allcnt']);
		}

		return $mainState;
	}

}
?>