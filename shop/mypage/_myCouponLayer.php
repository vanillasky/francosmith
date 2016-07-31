<?
global $db, $sess, $coupon_check;

$coupon_datas = $coupon_check->getCoupon();

$tpl = &$this;
$tpl->define('tpl', 'mypage/_myCouponLayer.htm');
$tpl->assign('mycouponinfo', $coupon_datas);
$tpl->print_('tpl');
?>