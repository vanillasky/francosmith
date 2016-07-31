<?

/* 메인 페이지이고 REFERER가 관리자 모드인 경우 처리 */
if( $sess['level'] >= 80 && ($_SERVER['HTTP_REFERER'] || $_GET['gd_preview'] == 1) ){
	if( eregi( "/admin/" , $_SERVER['HTTP_REFERER'] ) || $_GET['gd_preview'] == 1 ){
		if( $_GET['tplSkin'] ){
			$_SESSION['tplSkin']	= $_GET['tplSkin'];
		}
		elseif($_GET['tplSkinToday']) {
			$_SESSION['tplSkinToday']	= $_GET['tplSkinToday'];
		}
		else{
			unset($_SESSION['tplSkin']);
			unset($_SESSION['tplSkinToday']);
		}
	}
}

/* 미리보기 세션이 없는경우 세션 비우기 */
if( !$_SESSION['tplSkin'] && !$_SESSION['tplSkinToday']){
	unset($_SESSION['tplSkin']);
	unset($_SESSION['tplSkinToday']);
}

/* PC 보기로 한경우 세션 비우기 */
if( isset($_GET['pc']) ){
	unset($_SESSION['tplSkin']);
	unset($_SESSION['tplSkinToday']);
}

/* 설정된 스킨을 미리보기 스킨으로 변경 */
if( $_SESSION['tplSkin'] ){
	// 인트로는 기본 사용안함으로 전환
	if( ( !$cfg['tplSkinWork'] || $cfg['tplSkinWork'] != $cfg['tplSkin'] ) && $cfg['tplSkin'] != $_SESSION['tplSkin'] ){
		$cfg['introUseYN'] = "N";
	}
	$cfg['tplSkin'] = $_SESSION['tplSkin'];
}

/* 설정된 투데이샵 스킨을 미리보기 스킨으로 변경 */
if( $_SESSION['tplSkinToday'] ){
	// 인트로는 기본 사용안함으로 전환
	if( ( !$cfg['tplSkinTodayWork'] || $cfg['tplSkinTodayWork'] != $cfg['tplSkinToday'] ) && $cfg['tplSkinToday'] != $_SESSION['tplSkinToday'] ){
		$cfg['introUseYN'] = "N";
	}
	$cfg['tplSkinToday'] = $_SESSION['tplSkinToday'];
}

$_tmp['folder']	= explode("/",str_replace($_SERVER['DOCUMENT_ROOT'],"", $_SERVER['SCRIPT_FILENAME']));
$_tmp['defaultFolder'] = $_SERVER['DOCUMENT_ROOT']."/".$_tmp['folder'][1];
if(is_file( $_tmp['defaultFolder']."/conf/design_basic_".$cfg['tplSkin'].".php")){
	include $_tmp['defaultFolder']."/conf/design_basic_".$cfg['tplSkin'].".php";
}
?>