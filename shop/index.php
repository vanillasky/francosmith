<?
@include dirname(__FILE__) . "/lib/library.php";
include_once dirname(__FILE__) . "/conf/config.php";
@include_once dirname(__FILE__) . "/conf/config.mobileShop.php";
include_once dirname(__FILE__) . "/lib/tplSkinView.php";

# 모바일 접속 체크 : Start #
$arrMobileAgent = array('iPhone','Mobile','UP.Browser','Android','BlackBerry','Windows CE','Nokia','webOS','Opera Mini','SonyEricsson','opera mobi','Windows Phone','IEMobile','POLARIS','lgtelecom','NATEBrowser','AppleWebKit');
$arrExAgent = array('Macintosh','OpenBSD','SunOS','X11','QNX','BeOS', 'OS\/2','Windows NT');
if(preg_match('/('.implode('|',$arrMobileAgent).')/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/('.implode('|',$arrExAgent).')/i', $_SERVER['HTTP_USER_AGENT'])){
	$isMobile = true;
	if(preg_match('/(AppleWebKit)/i',$_SERVER['HTTP_USER_AGENT']) && preg_match('/(Windows;)/i',$_SERVER['HTTP_USER_AGENT'])) $isMobile = false;
	if(preg_match('/(Windows CE)/i',$_SERVER['HTTP_USER_AGENT']) && !preg_match('/(compatible;)/i',$_SERVER['HTTP_USER_AGENT']) && !preg_match('/(IEMobile)/i',$_SERVER['HTTP_USER_AGENT'])) $isMobile = false;
	if(preg_match('/(AppleWebKit)/i',$_SERVER['HTTP_USER_AGENT']) && preg_match('/(Linux;)/i',$_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile')) $isMobile = false;
}

if($cfgMobileShop['useMobileShop'] && $isMobile && is_null($_GET['pc'])){
	$tmpReferer = parse_url($_SERVER['HTTP_REFERER']);
	if(is_null($_GET['pc']) && $tmpReferer['host']!=$_SERVER['HTTP_HOST']){
		header("location:http://".$_SERVER['HTTP_HOST'].$cfgMobileShop['mobileShopRootDir']."/mgate.php?refer=".$_SERVER['REQUEST_URI']);exit;
	}
}
# 모바일 접속 체크 : End #

if ( $cfg['introUseYN'] == 'Y' && (int)$cfg['custom_landingpage'] < 2 ) {
	if(preg_match('/^http(s)?:\/\/'.$_SERVER[SERVER_NAME].'(\/)?(index\.php)?$/',$_SERVER[HTTP_REFERER]) || strpos($_SERVER[HTTP_REFERER],"http://".$_SERVER[SERVER_NAME]) !==0 ){ // 인트로 사용
		$_tmp['chk'] = "intro";
	}else{
		$_tmp['chk'] = "index";
	}
} else {
	$_tmp['chk'] = "index";
}

if ( $_tmp['chk'] == "intro" ){
	header("location:main/intro.php" . ($_SERVER[QUERY_STRING] ? "?{$_SERVER[QUERY_STRING]}" : ""));
}else{
	header("location:main/index.php" . ($_SERVER[QUERY_STRING] ? "?{$_SERVER[QUERY_STRING]}" : ""));
}

?>