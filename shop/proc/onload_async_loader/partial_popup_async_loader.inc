<?php

// PC ÆË¾÷
if (in_array('popupWindow', $schedule) || in_array('popupLayer', $schedule) || in_array('popupMoveLayer', $schedule)) {
	$tpl->include_('dataPopup');
	$returnData['popup'] = array();
	foreach (dataPopup() as $popup) {
		if ($_COOKIE['blnCookie_'.$popup['code']]) continue;
		if ($popup['type'] === 'layer' || $popup['type'] === 'layerMove') {
			$tpl->define($popup['file'], $popup['file']);
			$_GET['code'] = 'blnCookie_'.$popup['code'];
			$popup['content'] = iconv('EUC-KR', 'UTF-8', $tpl->fetch($popup['file']));
		}
		$returnData['popup'][] = $popup;
	}
}

// ¸ð¹ÙÀÏ ÆË¾÷
if (in_array('popupMobile', $schedule)) {
	include dirname(__FILE__).'/../../..'.$cfgMobileShop['mobileShopRootDir'].'/lib/lib.func.php';
	$today = date('Y-m-d H:i:s');
	$query = "SELECT * FROM ".GD_MOBILEV2_POPUP." 
			WHERE open=1 
			AND (
				open_type=0
				OR (
					open_type=1
					AND ('$today' between concat(start_date, ' ',if(length(start_time) = 1,concat('0',start_time),start_time),':00:00') 
					AND concat(end_date, ' ',if(length(end_time) = 1,concat('0',end_time),end_time),':59:59'))
				)
			)
			LIMIT 0,1";
	$popupData = $db->fetch($query, true);

	if($popupData['popup_type'] == '0') {
		$src = '../m/upload_img/'.$popupData['popup_img'];
		$size = getimagesize($src);
		if ($size[0] > 320)  $width = '320';
		else $width = $size[0];
		$popupData['popup_img'] = goodsimgMobile($src, $width, '', 1);
	}
	$popupData['popup_title'] = iconv('EUC-KR', 'UTF-8', $popupData['popup_title']);
	$popupData['popup_img'] = iconv('EUC-KR', 'UTF-8', $popupData['popup_img']);
	$popupData['popup_body'] = iconv('EUC-KR', 'UTF-8', $popupData['popup_body']);
	$returnData['mobilePopup'] = $popupData;
}