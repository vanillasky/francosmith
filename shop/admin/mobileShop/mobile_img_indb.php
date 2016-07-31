<?php

include "../lib.php";
include SHOPROOT."/lib/qfile.class.php";
include SHOPROOT."/lib/upload.lib.php";
include SHOPROOT."/conf/config.php";
include SHOPROOT."/conf/config.mobileShop.php";
include "./mobile_img_conver_api.php";

set_time_limit(0);

$img_list_org = SHOPROOT."/data/goods/";
$img_list_fullpath = SHOPROOT."/data/m/goods/";
$img_list = "/shop/data/m/goods/";
$img_editor_fullpath = SHOPROOT."/data/m/editor/";
$img_editor = "/shop/data/m/editor/";

$qfile = new qfile();
$cfgMobileShop = (array)$cfgMobileShop;
$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
$cfgMobileShop = array_map("addslashes",$cfgMobileShop);
$cfgMobileShop['vtype_mlongdesc'] = $_POST['vtype_mlongdesc'];
$qfile->open("../../conf/config.mobileShop.php");
$qfile->write("<? \n");
$qfile->write("\$cfgMobileShop = array( \n");
foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
$qfile->write(") \n;");
$qfile->write("?>");
$qfile->close();

if($_POST['vtype_mlongdesc']==0){
	go($_SERVER[HTTP_REFERER]);
}
elseif($_POST['vtype_mlongdesc']){

	$db_table = "";
	$where = array();

	switch ( $_POST['m_mode'] )
	{

	case "depend":	## ��ó����ǰ�� PC�󼼼���� �����ϰ� ó��
		$where[] = "length(a.mlongdesc) = 0";
		break;
	case "force":	## ��ó����ǰ�� PC�󼼼���� �����ϰ� ó��
		break;
	}

	if ($_POST['range_type2'] == 'query_select') {
		## üũ�� ��ǰ�� �������� ���� ������ �����.
		if (count($_POST['chk']) == 0) {
			msg("���õ� ��ǰ�� �����ϴ�.", -1);
			exit;
		}
		## �Ķ���� validation
		foreach($_POST['chk'] as $v) {
			if ( preg_match("/[a-z]/i", $_POST['goodsno'] ) ) {
				msg("���ڰ� �ƴѰ��� �ֽ��ϴ�.", -1);
				exit;
			}
		}
		$goodsno_tmp = "'".implode ("','", $_POST['chk'])."'";
		$where[] = "a.goodsno in (".$goodsno_tmp.")";
		$query = "select * from ".GD_GOODS." a where 1 ";
		if (count($where)>0) $query.= " and ".implode(" and ", $where);
	} else if ($_POST['range_type2'] == 'query_all') {
		## �Ķ���� validation
		if ( preg_match("/[a-z]/i", $_POST['m_cate'] ) ) {
			msg("ī�װ��� ���ڰ� �ƴѰ��� �ֽ��ϴ�.", -1);
			exit;
		}

		## ��ȸ���� �ۼ�
		$db_table = "".GD_GOODS." a
			left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
			";
		if ($_POST['cate']){
			$category = array_notnull($_POST[cate]);
			$category = $category[count($category)-1];
		}
		if ($category){
			$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
			$where[] = sprintf("category like '%s%%'", $category);
		}

		if ($_POST[m_sword]) $where[] = "$_POST[m_skey] like '%$_POST[m_sword]%'";
		if ($_POST[m_price_0] && $_POST[m_price_1]) $where[] = "price between {$_POST[m_price_0]} and {$_POST[m_price_1]}";
		if ($_POST[m_brandno]) $where[] = "brandno='$_POST[m_brandno]'";
		if ($_POST[m_regdt_0] && $_POST[m_regdt_1]) $where[] = "regdt between date_format({$_POST[m_regdt_0]},'%Y-%m-%d 00:00:00') and date_format({$_POST[m_regdt_1]},'%Y-%m-%d 23:59:59')";
		if ($_POST[m_open]) $where[] = "open=".substr($_POST[m_open],-1);
		if (strlen($_POST[m_open_mobile])>0) $where[] = "open_mobile=".$_POST[m_open_mobile];

		$cnt_query = "select count(*) as cnt from ".$db_table." where 1 "; ;
		if (count($where)>0) 	$cnt_query.= " and ".implode(" and ", $where);

		list($cnt_prod) = $db->fetch($cnt_query);
		if ($cnt_proc>300) {
			msg("ó����� ��ǰ��(".$cnt_prod.") �Դϴ�.  300 �̻��� ó������ �ʽ��ϴ�.", -1);
			exit;
		}

		$query = "select distinct a.goodsno, a.img_l, a.img_m, a.longdesc from ".$db_table." where 1 "; ;
		if (count($where)>0) 	$query.= " and ".implode(" and ", $where);
	} else {
		msg("������������� �߸��� ������ ���޵Ǿ����ϴ�.", -1);
		exit;
	}

	$goods_res = $db->query($query);

	$err_list_img = 0;
	$err_editor_img = 0;
	$all_count = 0;
	while ($goods_data = $db->fetch($goods_res))
	{
		$tmp = explode("|",$goods_data['img_l']);
		$img_l = $tmp[0];
		$longdesc = $goods_data['longdesc'];

		## LIST �̹��� ����
		$div = explode(".",$goods_data['img_l']);
		$mimg = time()."_".$goods_data['goodsno']."_e.".$div[count($div)-1];
		$result = img_convert($img_l, $mimg, 90);
		if ($result == 0)
			$err_list_img = 1;

		$mlongdesc = longdesc_img_convert($longdesc) ;

		$upt_query = "update ".GD_GOODS." SET ";
		$upt_query.= " img_mobile ='".$mimg."' ";
		$upt_query.= " , mlongdesc='".addslashes($mlongdesc)."'";
		$upt_query.= " where goodsno=".$goods_data['goodsno'];
		$db->query($upt_query);

		$all_count ++;
	}

	msg("�� ".$all_count." �� ��ǰ �̹��� ó�� �Ϸ�!", -1);
}

?>
