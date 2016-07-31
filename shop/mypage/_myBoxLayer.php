<?
global $db, $sess;

if ($sess[m_no]){

	$now = time();

	### 회원정보 가져오기
	$query = "
		select
				emoney,sum_sale,cnt_sale,
				a.level,
				b.sno as grp_sno,
				b.grpnm,
				b.grpnm_icon,
				b.grpnm_disp_type,
				wish.wish_count

		from ".GD_MEMBER." a

		left join ".GD_MEMBER_GRP." b on a.level=b.level

		left join (SELECT m_no, count(m_no) as wish_count FROM ".GD_MEMBER_WISHLIST." GROUP BY m_no) AS wish
		ON a.m_no = wish.m_no

		where a.m_no='$sess[m_no]'
	";
	$tmp = $db->fetch($query,1);


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
	$tmp[cnt_coupon] = $couponCnt;


	// 장바구니 갯수
	$cart = Core::loader('Cart');
	$tmp['cart_count'] = sizeof($cart->item);


	// 합치기
	$sess = array_merge($sess,$tmp);

}

$tpl = &$this;
$tpl->define('tpl', 'mypage/_myBoxLayer.htm');
$tpl->print_('tpl');
?>
