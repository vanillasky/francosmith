<?php

// 쿠폰발급알림 레이어
if (in_array('myCouponLayer', $schedule)) {
	if ($_COOKIE['cache_csno']) {
		$returnData['myCouponLayer'] = $db->_select('SELECT couponcd, coupon, summa FROM '.GD_COUPON.' WHERE couponcd IN('.$_COOKIE['cache_csno'].')');
		foreach ($returnData['myCouponLayer'] as $index => $value) {
			$returnData['myCouponLayer'][$index]['coupon'] = iconv('EUC-KR', 'UTF-8', $value['coupon']);
			$returnData['myCouponLayer'][$index]['summa'] = iconv('EUC-KR', 'UTF-8', $value['summa']);
		}
		setcookie('cache_csno', '', -1, '/');
	}
}