<?

/* 메인 페이지이고 REFERER가 관리자 모드인 경우 처리 */
if( $sess['level'] >= 80 && ($_SERVER['HTTP_REFERER'] || $_GET['gd_preview'] == 1) ){
	if( eregi( "/admin/" , $_SERVER['HTTP_REFERER'] ) || $_GET['gd_preview'] == 1 ){
		if( $_GET['tplSkin'] ){
			$_SESSION['tplSkin']	= $_GET['tplSkin'];
		}else{
			unset($_SESSION['tplSkin']);
		}
	}
}

/* 미리보기 세션이 없는경우 세션 비우기 */
if( !$_SESSION['tplSkin'] ){
	unset($_SESSION['tplSkin']);
}

/* 설정된 스킨을 미리보기 스킨으로 변경 */
if( $_SESSION['tplSkin'] ){
	$cfgMobileShop['tplSkinMobile'] = $_SESSION['tplSkin'];
}

$_tmp['folder']	= explode("/",str_replace($_SERVER['DOCUMENT_ROOT'],"", $_SERVER['SCRIPT_FILENAME']));
$_tmp['defaultFolder'] = $_SERVER['DOCUMENT_ROOT']."/".$_tmp['folder'][1];
?>