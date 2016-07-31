<?
	/* 작업스킨이 없는경우 */
	if(!$cfg['tplSkinWork']) $cfg['tplSkinWork']	= $cfg['tplSkin'];
	if(!$cfg['tplSkinTodayWork']) $cfg['tplSkinTodayWork']	= $cfg['tplSkinToday'];
	if(!$cfg['tplSkinMobileWork']) $cfg['tplSkinMobileWork']	= $cfg['tplSkinMobile'];

	/* 각 스킨별 사이즈 */
	/* 샵폴더/admin/todayshop/ 형태 접속은 투데이샵 스킨 설정을 불러옴*/
	// 모바일 스킨별 설정 추가로 투데이샵 , PC , 모바일V2 , 모바일V1 을 별도로 불러오게 변경
    $_design_basic_file = dirname(__FILE__);
    if(strpos( strtolower(dirname($_SERVER['PHP_SELF'])),'admin/todayshop') !== false){ // strpos( strtolower( dirname($_SERVER['PHP_SELF']) ) , 'admin/todayshop' ) 비교대상에서 앞은 로우했는데 뒤는 안한 비교?
        $_design_basic_file .= "/../conf/design_basicToday_".$cfg['tplSkinTodayWork'].".php";
    } else if(strpos( strtolower(dirname($_SERVER['PHP_SELF'])), strtolower('admin/mobileShop2') ) !== false){
        $_design_basic_file .= "/../conf/design_basicMobileV2_".$cfg['tplSkinMobileWork'].".php";
    } else if(strpos( strtolower(dirname($_SERVER['PHP_SELF'])), strtolower('admin/mobileShop') ) !== false){
        $_design_basic_file .=  "/../conf/design_basicMobile_".$cfg['tplSkinMobileWork'].".php";
    } else {
        $_design_basic_file .= "/../conf/design_basic_".$cfg['tplSkinWork'].".php";
    }
	if(is_file($_design_basic_file)){
		include $_design_basic_file;
	}else{
		// 해당 스킨의 기본 값 화일이 없는 경우 인트로는 기본 사용안함으로 전환
		if($cfg['tplSkinWork'] != $cfg['tplSkin']){
			$cfg['introUseYN'] = "N";
		}
		if($cfg['tplSkinMobileWork'] != $cfg['tplSkinMobile']){
			$cfg['introUseYNMobile'] = "N";
		}
	}

	/* 링크버튼 설정 */
	$workSkinLink1	= "<a href=\"../design/codi.php\" target=\"_blank\" class=\"workSkinTitle1\">[작업스킨 변경하기]</a>";
	$workSkinLink2	= "<a href=\"../design/codi.php\" target=\"_blank\" class=\"workSkinTitle2\">[작업스킨 변경하기]</a>";

	/* 현재 작업스킨 표시 */
	if($cfg['tplSkinWork'] == $cfg['tplSkin']){
		$workSkinStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinStr	.= "<div class=\"workSkinTitle1\">작업중인 스킨은 사용중인 스킨과 동일한 ".$cfg['tplSkinWork']." 스킨입니다. ".$workSkinLink1."</div>";
		$workSkinStr	.= "</td></tr>";
		$workSkinStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "</table>";
	}else{
		$workSkinStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinStr	.= "<div class=\"workSkinTitle2\">작업중인 스킨은 ".$cfg['tplSkinWork']." 스킨입니다. ".$workSkinLink2."</div>";
		$workSkinStr	.= "</td></tr>";
		$workSkinStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "</table>";
	}

	/* 링크버튼 설정 */
	$aRequestURI = explode("/", $_SERVER['REQUEST_URI']);
	for($i=0; $i<count($aRequestURI); $i++) {
		if ($aRequestURI[$i] == 'mobileShop2') break; 
	} 
	if ($i==count($aRequestURI)) $link_sub_path = "mobileShop"; 
	else $link_sub_path = "mobileShop2"; 

	$workSkinMobileLink1	= "<a href=\"../".$link_sub_path."/codi.php\" target=\"_blank\" class=\"workSkinTitle1\">[작업스킨 변경하기]</a>";
	$workSkinMobileLink2	= "<a href=\"../".$link_sub_path."/codi.php\" target=\"_blank\" class=\"workSkinTitle2\">[작업스킨 변경하기]</a>";

	/* 현재 작업스킨 표시 (모바일샵) */
	if($cfg['tplSkinMobileWork'] == $cfg['tplSkinMobile']){
		$workSkinMobileStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinMobileStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinMobileStr	.= "<div class=\"workSkinTitle1\">작업중인 스킨은 사용중인 스킨과 동일한 ".$cfg['tplSkinMobileWork']." 스킨입니다. ".$workSkinMobileLink1."</div>";
		$workSkinMobileStr	.= "</td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "</table>";
	}else{
		$workSkinMobileStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinMobileStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinMobileStr	.= "<div class=\"workSkinTitle2\">작업중인 스킨은 ".$cfg['tplSkinMobileWork']." 스킨입니다. ".$workSkinMobileLink2."</div>";
		$workSkinMobileStr	.= "</td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "</table>";
	}

	/* 링크버튼 설정 */
	$workSkinTodayLink1	= "<a href=\"../todayshop/codi.php\" target=\"_blank\" class=\"workSkinTitle1\">[작업스킨 변경하기]</a>";
	$workSkinTodayLink2	= "<a href=\"../todayshop/codi.php\" target=\"_blank\" class=\"workSkinTitle2\">[작업스킨 변경하기]</a>";

	/* 현재 작업스킨 표시 (투데이샵) */
	if($cfg['tplSkinTodayWork'] == $cfg['tplSkinToday']){
		$workSkinTodayStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinTodayStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinTodayStr	.= "<div class=\"workSkinTitle1\">작업중인 스킨은 사용중인 스킨과 동일한 ".$cfg['tplSkinTodayWork']." 스킨입니다. ".$workSkinTodayLink1."</div>";
		$workSkinTodayStr	.= "</td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "</table>";
	}else{
		$workSkinTodayStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinTodayStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinTodayStr	.= "<div class=\"workSkinTitle2\">작업중인 스킨은 ".$cfg['tplSkinTodayWork']." 스킨입니다. ".$workSkinTodayLink2."</div>";
		$workSkinTodayStr	.= "</td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "</table>";
	}

	/* 메인 페이지 상품 진열 설정 */
	if($cfg['shopMainGoodsConf'] == "E"){
		$strMainGoodsTitle	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$strMainGoodsTitle	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$strMainGoodsTitle	.= "<div class=\"workSkinTitle2\">스킨별로 개별 적용되며, 현재는 ".$cfg['tplSkinWork']." 스킨에만 적용이 됩니다. ".$workSkinLink2."</div>";
		$strMainGoodsTitle	.= "</td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "</table>";
		$strSQLWhere		= " and tplSkin = '".$cfg['tplSkinWork']."'";
	}else{
		$strMainGoodsTitle	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$strMainGoodsTitle	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$strMainGoodsTitle	.= "<div class=\"workSkinTitle1\">스킨별로 통합 적용됩니다. (각 스킨별 설정시 통합설정으로 되있는 스킨만 적용) ".$workSkinLink1."</div>";
		$strMainGoodsTitle	.= "</td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "</table>";
		$strSQLWhere		= " and (tplSkin = '' OR tplSkin IS NULL)";
	}
?>