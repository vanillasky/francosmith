<?php

// ¿À´Ãº» »óÇ°
if (in_array('todayGoods', $schedule)) {
	if ((int)$_GET['todayGoods']['imageSize'] > 0) {
		$imageSize = $_GET['todayGoods']['imageSize'];
	}
	else {
		$imageSize = 70;
	}
	$returnData['todayGoods'] = unserialize(stripslashes($_COOKIE['todayGoods']));
	$todayGoodsList_num = count($returnData['todayGoods']);
	for($index = 0; $index < $todayGoodsList_num; $index++){
		$returnData['todayGoods'][$index]['goodsnm'] = iconv('EUC-KR', 'UTF-8', $returnData['todayGoods'][$index]['goodsnm']);
		$returnData['todayGoods'][$index]['img'] = goodsimg($returnData['todayGoods'][$index]['img'], $imageSize);
		$row = $db->fetch('SELECT use_only_adult FROM '.GD_GOODS.' WHERE goodsno='.$todayGoodsList[$index]['goodsno'], true);
		if($row['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			$returnData['todayGoods'][$index]['img'] = goodsimg('http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/skin/'.$cfg['tplSkin'].'/img/common/19.gif', $imageSize);
		}
	}
}