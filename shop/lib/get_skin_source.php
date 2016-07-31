<?
@include_once dirname(__FILE__).'/lib.func.php';
@include_once dirname(__FILE__).'/../conf/config.php';
@include_once dirname(__FILE__).'/../conf/config.mobileShop.php';

header("Content-type: text/html; charset=euc-kr");

$arg = &$_POST;
if (get_magic_quotes_gpc()) $arg = strip_slashes($arg);

$dir = '';
$rootDir = str_replace('//', '/', '/'.strtolower($cfg['rootDir']).'/');
$mobileShopRootDir = str_replace('//', '/', '/'.strtolower($cfgMobileShop['mobileShopRootDir']).'/');
$cr_len = strlen($rootDir);
$cm_len = strlen($mobileShopRootDir);

$skin_nm = $_GET['skin_nm'];
$referer = preg_replace('/^http:\/\/[^\/]*\/*/i', '/', $_SERVER['HTTP_REFERER']);
$designdir = 'design';
if ($rootDir == substr($referer, 0, $cr_len)) {
	if (!$skin_nm) $skin_nm = (($cfg['tplSkinWork'])? $cfg['tplSkinWork'] : $cfg['tplSkin']);
	$dir = dirname(__FILE__)."/../data/skin/".$skin_nm;
}
else if ($mobileShopRootDir == substr($referer, 0, $cm_len)) {
	### 적용된 모바일버전 (1.0, 2.0) 을 확인하고, 네이게이션 메뉴의 URL 경로를 지정해준다.
	$version2_apply_file_name = ".htaccess";
	 ## 현재 적용버전을 확인하다
	if ( file_exists(dirname(__FILE__)."/../../m/".$version2_apply_file_name) ) {

		$aFileContent = file(dirname(__FILE__)."/../../m/".$version2_apply_file_name);

		for ($i=0; $i<count($aFileContent); $i++) {
			if (preg_match("/RewriteRule/i", $aFileContent[$i])) {
				break;
			}
		}
		if ($i < count($aFileContent)) {
			$mobile_dir = "skin_mobileV2";
			$designdir = 'mobileShop2';
		} else {
			$mobile_dir = "skin_mobile";
			$designdir = 'mobileShop';
		}
	} else {
		$mobile_dir = "skin_mobile";
		$designdir = 'mobileShop';
	}

	if (!$skin_nm) $skin_nm = (($cfg['tplSkinMobileWork'])? $cfg['tplSkinMobileWork'] : $cfgMobileShop['tplSkin']);
	$dir = dirname(__FILE__)."/../data/".$mobile_dir."/".$skin_nm;
}

$param = explode(',', $arg['srcinfo']);
for($i = 0; $i < count($param); ++$i) {
	$srcinfo = explode(':', $param[$i]);
	$fname = $dir.$srcinfo[1];

	if (!$fname || !file_exists($fname)) {
		$src[] = 'src:'.$srcinfo[1].'|'.$srcinfo[0].'|'.$designdir.chr(10).'스킨 소스를 찾을 수 없습니다.';
	}
	else {
		$html = file_get_contents($fname);
		$html = explode(chr(10), $html);

		if ($srcinfo[2] == 0 && $srcinfo[3] == 0) {
			$srcinfo[2] = 1;
			$srcinfo[3] = count($html);
		}

		$res_html = array();
		for($j = $srcinfo[2] - 1; $j < count($html) && $j < $srcinfo[3]; ++$j) {
			$res_html[] = ($j+1).' : '.htmlspecialchars($html[$j]);
		}
		$src[] = 'src:'.$srcinfo[0].'|'.$srcinfo[1].'|'.$designdir.chr(10).implode(chr(10), $res_html);
	}
}
$res = implode(chr(10), $src);

echo $res;
?>
