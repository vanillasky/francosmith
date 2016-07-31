<?
$noDemoMsg = 1;
include "../_header.php";

## 쿠폰 설정 정보
@include "../conf/coupon.php";

## 로긴체크
if(!$sess['m_no'])echo "<script>alert('로그인 하셔야 합니다.');self.close();</script>";

## 쿠폰 사용여부
if(!$cfgCoupon['use_yn'])echo "<script>alert('쿠폰사용이 불가 합니다.');self.close();</script>";

$today = date("YmdH");

$query = "
SELECT count(*) cnt
FROM gd_offline_coupon
WHERE `status`!='disuse'
	AND	concat(start_year,start_mon,start_day,start_time) <= '$today'
	AND concat(end_year,end_mon,end_day,end_time) >= '$today'";

list($data) = $db->_select($query);

if($data[cnt] < 1) echo "<script>alert('입력하실 쿠폰이 없습니다.');self.close();</script>";

$tpl->print_('tpl');
?>