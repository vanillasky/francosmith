<?

include "../_header.php"; chkMember();
$groupsno = $_SESSION['sess']['groupsno'];

$query = "
SELECT
	cp.*
FROM
(
	SELECT c.*, a.sno as applysno, a.goodsno, a.goodsnm, a.regdt as apply_regdt
	FROM gd_coupon_apply AS a
	STRAIGHT_JOIN gd_coupon AS c
	ON c.couponcd = a.couponcd
	WHERE a.membertype = 0

	UNION

	SELECT c.*, a.sno as applysno, a.goodsno, a.goodsnm, a.regdt as apply_regdt
	FROM gd_coupon_apply AS a
	STRAIGHT_JOIN gd_coupon AS c
	ON c.couponcd = a.couponcd
	WHERE a.membertype = 1 AND a.member_grp_sno = '$groupsno'

	UNION

	SELECT c.*, a.sno as applysno, a.goodsno, a.goodsnm, a.regdt as apply_regdt
	FROM gd_coupon_apply AS a
	STRAIGHT_JOIN gd_coupon AS c
	ON c.couponcd = a.couponcd
	INNER JOIN gd_coupon_applymember AS b
	ON a.sno = b.applysno
	WHERE a.membertype = 2 AND b.m_no = '$sess[m_no]'
) as cp

WHERE

( cp.priodtype = '0' AND cp.sdate <= '".date("Y-m-d H:i:s")."' AND cp.edate >= '".date("Y-m-d H:i:s")."' )
OR
( cp.priodtype = '1' AND ADDDATE( cp.apply_regdt, INTERVAL cp.sdate DAY) >= '".date("Y-m-d")." 00:00:00' )

ORDER BY cp.couponcd
";

$tab01 = "off";//전체
$tab02 = "off";//사용
$tab03 = "off";//미사용

if($_GET[tab] == "used"){
	$_GET[tab] = "사용";
	$tab02 = "on";
}else if($_GET[tab] == "wait"){
	$_GET[tab] = "미사용";
	$tab03 = "on";
}else{
	$_GET[tab] = "전체";
	$tab01 = "on";
}

$res = $db->query($query);
while ($data=$db->fetch($res)){
	$query = "select count(*) from gd_coupon_order where applysno='$data[applysno]' and m_no ='".$sess[m_no]."'";
	list($cnt) = $db->fetch($query);
	if($cnt > 0){
		$data['cnt'] = "사용";
		$data['dataStr'] = substr($data['apply_regdt'],0, 16);
	}else{
		$data['cnt'] = "미사용";
		if($data['priodtype'] == 1){// 발급일로 부터 n 일 단, 최대 사용기간 제한(priod_edate)이 있을 수 있음
			$sdate = strtotime($data['apply_regdt']);	// 사용일 시작
			$edate = strtotime("+$data[sdate] day", $sdate);

			if ($data['edate']) {
				$priod_edate = strtotime($data['edate']);
				if ($edate > $priod_edate) $edate = $priod_edate;
			}

			$data['sdate'] = date('Y-m-d',$sdate);
			$data['edate'] = date('Y-m-d',$edate);
		}
		else {						// 특정 기간동안 사용
			$data['sdate'] = substr($data['sdate'],0,10);
			$data['edate'] = substr($data['edate'],0,10);
		}

		$data['dataStr'] = $data['sdate']."~ <br>".$data['edate'];
	}

	/* 모바일샵 쿠폰 표시 */
	if($data['c_screen']=='m') {
		$data['coupon'] .= '(모바일샵 전용)';
	}

	if($_GET[tab] == "전체"){
		$loop['goods'][] = $data;
	}else{
		if($data['cnt'] == $_GET[tab])$loop['goods'][] = $data;
	}
}

$today = date("YmdH");
$arAbility = array('sale'=>'0','save'=>'1');
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
foreach($result as $data){
	$data['coupon']=$data['coupon_name'];
	$data['sdate']=$data['start_year'].'-'.$data['start_mon'].'-'.$data['start_day'];
	$data['edate']=$data['end_year'].'-'.$data['end_mon'].'-'.$data['end_day'];
	$data['ability']=$arAbility[$data['coupon_type']];
	$data['price']=$data['coupon_price'].$data['currency'];
	$data['priodtype']='2';
  $data['dataStr'] = $data['sdate']."~ <br>".$data['edate'];

	$query = "select count(*) from gd_coupon_order where
		downloadsno='$data[download_sno]'
		AND m_no='$sess[m_no]'";
	list($ordercnt) = $db->fetch($query);

	if($ordercnt > 0)$data['cnt'] = "사용";
	else $data['cnt'] = "미사용";

	if($_GET[tab] == "전체"){
		$loop['goods'][] = $data;
	}else{
		if($data['cnt']==$_GET[tab])$loop['goods'][] = $data;
	}
}
if ($loop) $tpl->assign($loop);
$tpl->print_('tpl');

?>
