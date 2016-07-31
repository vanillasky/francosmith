<?php
	@include dirname(__FILE__) . "/lib/library.php";
	@include $shopRootDir . "/Template_/Template_.class.php";
	@include_once $shopRootDir . "/lib/tplSkinMobileView.php";

	$cfgMobileShop = array_map("slashes",$cfgMobileShop);

	if(!$cfgMobileShop['tplSkinMobile']) $cfgMobileShop['tplSkinMobile'] = 'default';

	$cfgMobileShop['tplSkinMobile'] = 'default'; //기본으로 할당 dn 2012-03-13

	### 메타태그 변수 할당
	$meta_title = $cfg[title];
	$meta_keywords = $cfg[keywords];

	$tpl = new Template_;
	$tpl->template_dir	= $shopRootDir."/data/skin_shopTouch/".$cfgMobileShop['tplSkinMobile'];
	$tpl->compile_dir	= $shopRootDir."/Template_/_compiles/skin_shopTouch/".$cfgMobileShop['tplSkinMobile'];
	$tpl->prefilter		= "adjustPath|include_file|capture_print";

	{ // File Key

	$key_file = preg_replace( "'^.*$mobileRootDir/'si", "", $_SERVER['SCRIPT_NAME'] );
	$key_file = preg_replace( "'\.php$'si", ".htm", $key_file );

	if ( $key_file == 'html.htm' && $_GET['htmid'] != '' ) $key_file = $_GET['htmid'];

	$data_file		= $design_skin[ $key_file ];		# File Data
	}

	$tpl->define( array(
		'tpl'			=> $key_file,
		'header'		=> 'shopTouch_outline/_header.htm',
		'footer'		=> 'shopTouch_outline/_footer.htm',
		'sub_header'	=> 'shopTouch_outline/_sub_header.htm',
		) );
	
	$tpl->assign( array(
		pfile	=> basename($_SERVER[PHP_SELF]),
		pdir	=> basename(dirname($_SERVER[PHP_SELF])),
		) );

?>