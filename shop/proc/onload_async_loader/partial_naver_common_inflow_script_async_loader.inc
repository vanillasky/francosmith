<?php

if (in_array('naverCommonInflowScriptParam', $schedule)) {
	// 네이버 공통유입 스크립트
	@include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
	$naverCommonInflowScript = new NaverCommonInflowScript();
	if ($naverCommonInflowScript->useNaverService) {
		$param = array();
		@include dirname(__FILE__).'/../../conf/config.mobileShop.php';
		$removeDir = array(str_replace('/', '\/', $naverCommonInflowScript->shopConfig['rootDir']));
		if (isset($cfgMobileShop) && isset($cfgMobileShop['mobileShopRootDir'])) $removeDir[] = str_replace('/', '\/', $cfgMobileShop['mobileShopRootDir']);
		$patternRootDir = '/^('.implode('|', $removeDir).')\//';
		$param['Path'] = preg_replace($patternRootDir, '', $_GET['sn']);
		$param['Referer'] = urlencode($_GET['rf']);
		$param['AccountID'] = $naverCommonInflowScript->config['accountId'];
		$param['Inflow'] = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
		foreach($naverCommonInflowScript->whiteList as $whiteList)
		{
			if(strlen(trim($whiteList))>0) $param['WhiteList'][] = $whiteList;
		}
		$returnData['naverCommonInflowScriptParam'] = $param;
	}
}