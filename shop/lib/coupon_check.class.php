<?php
class coupon_check{
	var $db;
	var $m_no;
	var $groupsno;
	var $name;
	var $now_date;

	function coupon_check(){
		$this->db		= & $GLOBALS['db'];
		$this->m_no		= $_SESSION['sess']['m_no'];
		$this->groupsno = $_SESSION['sess']['groupsno'];
		$this->name		= $_SESSION['member']['name'];
		$this->now_date = date("Y-m-d H:i:s");
	}

	function exists_alertcoupon() {

		if( !$this->m_no || !$this->groupsno ) return false;

		// mysql 캐시를 이용하기 위해 count 를 select 하지 않음.
		$_tmp = explode(' ',$this->now_date);
		$query = "
			SELECT
				cp.*
			FROM
			(
				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				WHERE a.membertype = 0

				UNION

				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				WHERE a.membertype = 1 AND a.member_grp_sno = '".$this->groupsno."'

				UNION

				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				INNER JOIN ".GD_COUPON_APPLYMEMBER." AS b
				ON a.sno = b.applysno
				WHERE a.membertype = 2 AND b.m_no = ".$this->m_no."
			) as cp

			LEFT JOIN ".GD_COUPON_ORDER." AS d
			ON ( cp.applysno = d.applysno AND d.m_no = ".$this->m_no." )

			LEFT JOIN ".GD_COUPON_CHECK_MEMBER." AS e
			ON ( cp.couponcd = e.coupon_sno AND e.m_no = ".$this->m_no." )

			WHERE
				(d.sno IS NULL AND e.sno IS NULL) AND
				(
					( cp.priodtype = '0' AND cp.sdate <= '".$this->now_date."' AND cp.edate >= '".$this->now_date."' )
					OR
					( cp.priodtype = '1' AND ADDDATE( cp.regdt, INTERVAL cp.sdate DAY) >= '".array_shift($_tmp).' 00:00:00'."' )
				)
		";
		$ret = $this->db->query($query);

		if ($this->db->count_($ret) > 0) return true;

		return false;
	}

	function getCoupon(){
		if( !$this->m_no || !$this->groupsno ) return false;

		$query = "
			SELECT
				cp.*
			FROM
			(
				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				WHERE a.membertype = 0

				UNION

				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				WHERE a.membertype = 1 AND a.member_grp_sno = '".$this->groupsno."'

				UNION

				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				INNER JOIN ".GD_COUPON_APPLYMEMBER." AS b
				ON a.sno = b.applysno
				WHERE a.membertype = 2 AND b.m_no = ".$this->m_no."
			) as cp

			LEFT JOIN ".GD_COUPON_ORDER." AS d
			ON ( cp.applysno = d.applysno AND d.m_no = ".$this->m_no." )

			LEFT JOIN ".GD_COUPON_CHECK_MEMBER." AS e
			ON ( cp.couponcd = e.coupon_sno AND e.m_no = ".$this->m_no." )

			WHERE
				(d.sno IS NULL AND e.sno IS NULL) AND
				(
					( cp.priodtype = '0' AND cp.sdate <= '".$this->now_date."' AND cp.edate >= '".$this->now_date."' )
					OR
					( cp.priodtype = '1' AND ADDDATE( cp.regdt, INTERVAL cp.sdate DAY) >= '".array_shift(explode(' ',$this->now_date)).' 00:00:00'."' )
				)
		";

		$ret = $this->db->_select($query);
		if( $ret ){
			$rtn_datas = array();
			for($i=0, $il=count($ret); $i<$il; $i++){
				$data = $ret[$i];

				$ins_data['coupon_sno'] = $data['couponcd'];
				$ins_data['m_no']		= $this->m_no;
				$query = $this->db->_query_print('INSERT INTO gd_coupon_check_member SET [cv], check_date=now()', $ins_data);
				$this->db->query($query);

				$data['name'] = $this->name;
				$rtn_datas[] = $data;
			}
		}
		// 페이지 캐시처리시 스크립트단에서 쿠폰발급 팝업창이 떴었는지 여부를 확인하기 위해 쿠키에 쿠폰번호 저장
		foreach ($rtn_datas as $data) $couponSno[] = $data['couponcd'];
		setcookie('cache_csno', implode(',', $couponSno), time() + (60 * 60 * 24), '/');
		return $rtn_datas;
	}
}
?>
