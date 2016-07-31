<?php

include "../lib.php";
include SHOPROOT."/lib/qfile.class.php";
include SHOPROOT."/lib/upload.lib.php";
include SHOPROOT."/conf/config.php";
include SHOPROOT."/conf/config.mobileShop.php";

$qfile = new qfile();
$cfgMobileShop = (array)$cfgMobileShop;
$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
$cfgMobileShop = array_map("addslashes",$cfgMobileShop);
$cfgMobileShop['vtype_goods'] = 1;
$qfile->open("../../conf/config.mobileShop.php");
$qfile->write("<? \n");
$qfile->write("\$cfgMobileShop = array( \n");
foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
$qfile->write(") \n;");
$qfile->write("?>");
$qfile->close();

$db_table = "";
$where = array();

switch ( $_POST['set_open_mobile'] )
{
	case "true":case "false":case "same":
		if($_POST['range_type1']=='query_all'){
		## 조회쿼리 작성
			$db_table = "".GD_GOODS." a
				left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
			";
			if ($_POST[cate]){
				$category = array_notnull($_POST[cate]);
				$category = $category[count($category)-1];
			}
			if ($category){
				$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
				$where[] = "category like '$category%'";
			}
			if ($_POST[m_sword]) $where[] = "$_POST[m_skey] like '%$_POST[m_sword]%'";
			if ($_POST[m_price_0] && $_POST[m_price_1]) $where[] = "price between {$_POST[m_price_0]} and {$_POST[m_price_1]}";
			if ($_POST[m_brandno]) $where[] = "brandno='$_POST[m_brandno]'";
			if ($_POST[m_regdt_0] && $_POST[m_regdt_1]) $where[] = "regdt between date_format({$_POST[m_regdt_0]},'%Y-%m-%d 00:00:00') and date_format({$_POST[m_regdt_1]},'%Y-%m-%d 23:59:59')";
			if ($_POST[m_open]) $where[] = "open=".substr($_POST[m_open],-1);
			if (strlen($_POST[m_open_mobile])>0) $where[] = "open_mobile=".$_POST[m_open_mobile];

			$_POST['chk'] = array();
			if(count($where)){
				$query = "select * from {$db_table} where ".implode(" and ",$where)." group by a.goodsno";
				$res = $db->query($query);
				while ($data=$db->fetch($res)) $_POST['chk'][] = $data['goodsno'];
			}
		}

		if(count($_POST['chk'])){
			if($_POST['set_open_mobile']=='true') $open_mobile = 1;
			elseif($_POST['set_open_mobile']=='false') $open_mobile = 0;
			elseif($_POST['set_open_mobile']=='same') $open_mobile = "`open`";
			else break;

			foreach($_POST['chk'] as $goodsno){
				$query = "update gd_goods set open_mobile={$open_mobile} where goodsno={$goodsno}";
				$db->query($query);
			}
		}

	break;
}

go($_SERVER[HTTP_REFERER]);

?>