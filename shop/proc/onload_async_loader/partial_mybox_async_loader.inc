<?php

// 회원정보(마이페이지 레이어)
if ($sess) {
	if (in_array('mypageLayerGroupName', $schedule)) {
		list($grpnm) = $db->fetch('SELECT grpnm FROM '.GD_MEMBER_GRP.' WHERE level='.$sess['level']);
		$returnData['member']['grpnm'] = iconv('EUC-KR', 'UTF-8', $grpnm);
	}

	if (in_array('mypageLayerSumSale', $schedule) || in_array('mypageLayerEmoney', $schedule)) {
		list($sumSale, $emoney) = $db->fetch('SELECT sum_sale, emoney FROM '.GD_MEMBER.' WHERE m_no='.$sess['m_no']);
		$returnData['member']['sumSale'] = number_format($sumSale);
		$returnData['member']['emoney'] = number_format($emoney);
	}

	if (in_array('mypageLayerCouponCount', $schedule)) {
		// 쿠폰 갯수
		$query = "
			SELECT
				COUNT(cp.couponcd)
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
				WHERE a.membertype = 1 AND a.member_grp_sno = '".$sess[groupsno]."'

				UNION

				SELECT c.couponcd,c.coupon,c.summa,c.priodtype,c.sdate,c.edate, a.sno as applysno, a.regdt
				FROM ".GD_COUPON_APPLY." AS a
				STRAIGHT_JOIN ".GD_COUPON." AS c
				ON c.couponcd = a.couponcd
				INNER JOIN ".GD_COUPON_APPLYMEMBER." AS b
				ON a.sno = b.applysno
				WHERE a.membertype = 2 AND b.m_no = ".$sess[m_no]."
			) as cp

			LEFT JOIN ".GD_COUPON_ORDER." AS d
			ON ( cp.applysno = d.applysno AND d.m_no = '".$sess[m_no]."' )

			WHERE
				d.sno IS NULL AND
				(
					( cp.priodtype = '0' AND cp.sdate <= '".date("Y-m-d H:i:s",$now)."' AND cp.edate >= '".date("Y-m-d H:i:s",$now)."' )
					OR
					( cp.priodtype = '1' AND ADDDATE( cp.regdt, INTERVAL cp.sdate DAY) >= '".date("Y-m-d", $now).' 00:00:00'."' )
				)
		";
		list($_couponCnt) = $db->fetch($query);
		$couponCnt = (int)$couponCnt + (int)$_couponCnt;

		$today = date("YmdH",$now);
		$query = "
		SELECT coupon.*,down.sno download_sno
		FROM gd_offline_coupon coupon,gd_offline_download down
		WHERE down.coupon_sno=coupon.sno
			AND	coupon.`status`!='disuse'
			AND	concat(coupon.start_year,coupon.start_mon,coupon.start_day,coupon.start_time) <= '$today'
			AND concat(coupon.end_year,coupon.end_mon,coupon.end_day,coupon.end_time) >= '$today'
			AND	down.m_no='$sess[m_no]'
		ORDER BY coupon.sno DESC";
		$result = $db->_select($query);
		if($result)foreach($result as $data){
			$query = "select count(*) from gd_coupon_order where
				download_sno='$data[download_sno]'
				AND m_no='$sess[m_no]'";
			list($ordercnt) = $db->fetch($query);
			if($ordercnt==0) $couponCnt++;
		}
		$returnData['member']['couponCount'] = number_format($couponCnt);
	}

	if (in_array('mypageLayerCartCount', $schedule)) {
		$returnData['member']['cartCount'] = number_format(count(Core::loader('Cart')->item));
	}

	if (in_array('mypageLayerWishCount', $schedule)) {
		list($wishCount) = $db->fetch('SELECT COUNT(*) FROM '.GD_MEMBER_WISHLIST.' WHERE m_no='.$sess['m_no']);
		$returnData['member']['wishCount'] = number_format($wishCount);
	}
}