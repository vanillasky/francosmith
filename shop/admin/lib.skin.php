<?
	/* �۾���Ų�� ���°�� */
	if(!$cfg['tplSkinWork']) $cfg['tplSkinWork']	= $cfg['tplSkin'];
	if(!$cfg['tplSkinTodayWork']) $cfg['tplSkinTodayWork']	= $cfg['tplSkinToday'];
	if(!$cfg['tplSkinMobileWork']) $cfg['tplSkinMobileWork']	= $cfg['tplSkinMobile'];

	/* �� ��Ų�� ������ */
	/* ������/admin/todayshop/ ���� ������ �����̼� ��Ų ������ �ҷ���*/
	// ����� ��Ų�� ���� �߰��� �����̼� , PC , �����V2 , �����V1 �� ������ �ҷ����� ����
    $_design_basic_file = dirname(__FILE__);
    if(strpos( strtolower(dirname($_SERVER['PHP_SELF'])),'admin/todayshop') !== false){ // strpos( strtolower( dirname($_SERVER['PHP_SELF']) ) , 'admin/todayshop' ) �񱳴�󿡼� ���� �ο��ߴµ� �ڴ� ���� ��?
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
		// �ش� ��Ų�� �⺻ �� ȭ���� ���� ��� ��Ʈ�δ� �⺻ ���������� ��ȯ
		if($cfg['tplSkinWork'] != $cfg['tplSkin']){
			$cfg['introUseYN'] = "N";
		}
		if($cfg['tplSkinMobileWork'] != $cfg['tplSkinMobile']){
			$cfg['introUseYNMobile'] = "N";
		}
	}

	/* ��ũ��ư ���� */
	$workSkinLink1	= "<a href=\"../design/codi.php\" target=\"_blank\" class=\"workSkinTitle1\">[�۾���Ų �����ϱ�]</a>";
	$workSkinLink2	= "<a href=\"../design/codi.php\" target=\"_blank\" class=\"workSkinTitle2\">[�۾���Ų �����ϱ�]</a>";

	/* ���� �۾���Ų ǥ�� */
	if($cfg['tplSkinWork'] == $cfg['tplSkin']){
		$workSkinStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinStr	.= "<div class=\"workSkinTitle1\">�۾����� ��Ų�� ������� ��Ų�� ������ ".$cfg['tplSkinWork']." ��Ų�Դϴ�. ".$workSkinLink1."</div>";
		$workSkinStr	.= "</td></tr>";
		$workSkinStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "</table>";
	}else{
		$workSkinStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinStr	.= "<div class=\"workSkinTitle2\">�۾����� ��Ų�� ".$cfg['tplSkinWork']." ��Ų�Դϴ�. ".$workSkinLink2."</div>";
		$workSkinStr	.= "</td></tr>";
		$workSkinStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinStr	.= "</table>";
	}

	/* ��ũ��ư ���� */
	$aRequestURI = explode("/", $_SERVER['REQUEST_URI']);
	for($i=0; $i<count($aRequestURI); $i++) {
		if ($aRequestURI[$i] == 'mobileShop2') break; 
	} 
	if ($i==count($aRequestURI)) $link_sub_path = "mobileShop"; 
	else $link_sub_path = "mobileShop2"; 

	$workSkinMobileLink1	= "<a href=\"../".$link_sub_path."/codi.php\" target=\"_blank\" class=\"workSkinTitle1\">[�۾���Ų �����ϱ�]</a>";
	$workSkinMobileLink2	= "<a href=\"../".$link_sub_path."/codi.php\" target=\"_blank\" class=\"workSkinTitle2\">[�۾���Ų �����ϱ�]</a>";

	/* ���� �۾���Ų ǥ�� (����ϼ�) */
	if($cfg['tplSkinMobileWork'] == $cfg['tplSkinMobile']){
		$workSkinMobileStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinMobileStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinMobileStr	.= "<div class=\"workSkinTitle1\">�۾����� ��Ų�� ������� ��Ų�� ������ ".$cfg['tplSkinMobileWork']." ��Ų�Դϴ�. ".$workSkinMobileLink1."</div>";
		$workSkinMobileStr	.= "</td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "</table>";
	}else{
		$workSkinMobileStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinMobileStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinMobileStr	.= "<div class=\"workSkinTitle2\">�۾����� ��Ų�� ".$cfg['tplSkinMobileWork']." ��Ų�Դϴ�. ".$workSkinMobileLink2."</div>";
		$workSkinMobileStr	.= "</td></tr>";
		$workSkinMobileStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinMobileStr	.= "</table>";
	}

	/* ��ũ��ư ���� */
	$workSkinTodayLink1	= "<a href=\"../todayshop/codi.php\" target=\"_blank\" class=\"workSkinTitle1\">[�۾���Ų �����ϱ�]</a>";
	$workSkinTodayLink2	= "<a href=\"../todayshop/codi.php\" target=\"_blank\" class=\"workSkinTitle2\">[�۾���Ų �����ϱ�]</a>";

	/* ���� �۾���Ų ǥ�� (�����̼�) */
	if($cfg['tplSkinTodayWork'] == $cfg['tplSkinToday']){
		$workSkinTodayStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinTodayStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinTodayStr	.= "<div class=\"workSkinTitle1\">�۾����� ��Ų�� ������� ��Ų�� ������ ".$cfg['tplSkinTodayWork']." ��Ų�Դϴ�. ".$workSkinTodayLink1."</div>";
		$workSkinTodayStr	.= "</td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "</table>";
	}else{
		$workSkinTodayStr	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$workSkinTodayStr	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$workSkinTodayStr	.= "<div class=\"workSkinTitle2\">�۾����� ��Ų�� ".$cfg['tplSkinTodayWork']." ��Ų�Դϴ�. ".$workSkinTodayLink2."</div>";
		$workSkinTodayStr	.= "</td></tr>";
		$workSkinTodayStr	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$workSkinTodayStr	.= "</table>";
	}

	/* ���� ������ ��ǰ ���� ���� */
	if($cfg['shopMainGoodsConf'] == "E"){
		$strMainGoodsTitle	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$strMainGoodsTitle	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$strMainGoodsTitle	.= "<div class=\"workSkinTitle2\">��Ų���� ���� ����Ǹ�, ����� ".$cfg['tplSkinWork']." ��Ų���� ������ �˴ϴ�. ".$workSkinLink2."</div>";
		$strMainGoodsTitle	.= "</td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "</table>";
		$strSQLWhere		= " and tplSkin = '".$cfg['tplSkinWork']."'";
	}else{
		$strMainGoodsTitle	= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
		$strMainGoodsTitle	.= "<tr><td height=\"20\"><img src=\"../img/codi/bar_work_skin.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"22\" style=\"border-left:1px solid #f64c01;border-right:1px solid #f64c01;\">";
		$strMainGoodsTitle	.= "<div class=\"workSkinTitle1\">��Ų���� ���� ����˴ϴ�. (�� ��Ų�� ������ ���ռ������� ���ִ� ��Ų�� ����) ".$workSkinLink1."</div>";
		$strMainGoodsTitle	.= "</td></tr>";
		$strMainGoodsTitle	.= "<tr><td height=\"4\" style=\"padding-bottom:5px;\"><img src=\"../img/codi/bg_work_skin_bottom.gif\" align=\"absmiddle\" /></td></tr>";
		$strMainGoodsTitle	.= "</table>";
		$strSQLWhere		= " and (tplSkin = '' OR tplSkin IS NULL)";
	}
?>