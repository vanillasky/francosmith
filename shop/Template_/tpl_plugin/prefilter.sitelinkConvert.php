<?php

function pass_urlreplace($matches) {
	$result = $matches[3];
	if(strncmp($result,'../',3)==0) {
		$result = substr($result,3);
	}
	else if(strncmp($result,'./',2)==0) {
		$result = substr($result,2);
		preg_match('/([a-zA-Z0-8\_\-]+)$/',dirname($_SERVER['PHP_SELF']),$dirname);
		$result = $dirname[1].'/'.$result;
	}
	else {
		preg_match('/([a-zA-Z0-8\_\-]+)$/',dirname($_SERVER['PHP_SELF']),$dirname);
		$result = $dirname[1].'/'.$result;
	}

	$result = '{=url("'.$result.'")}&';

	return $matches[1].$result.$matches[4];
}

function sitelinkConvert($source, $tpl) {
	if(preg_match('/(order|todayshop)\/card\//',$tpl->tpl_path)) {
		return $source;		
	}
	$pattern = array(
		'/((href|action)=\')(?!\/)(?![a-z]+:\/\/)(?!javascript:)([^\']+\.php\??)([^\']*\')/i',
		'/((href|action)=\")(?!\/)(?![a-z]+:\/\/)(?!javascript:)([^\"]+\.php\??)([^\"]*\")/i'
	);
	$source = preg_replace_callback($pattern,'pass_urlreplace',$source);

	return $source;
}

?>
