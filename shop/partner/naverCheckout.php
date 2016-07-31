<?php
/**************************************************
 * 네이버 체크아웃에서 요청한 상품정보 전송
 *************************************************/

include "../lib/library.php";
require "../conf/config.php";
require "../lib/load.class.php";
require "../lib/partner.class.php";
require "../lib/naverCheckout.class.php";
@include "../conf/naverCheckout.cfg.php";

function get_img($img,$rootDir){
    $tmp = explode('|',$img);
    $img = $tmp[0];
    if(!$img)return false;
    if(!preg_match('/http:\/\//',$img)){
	$img1 = "/data/goods/".$img;
	$img2 = dirname(__FILE__)."/../data/goods/".$img;
	if(file_exists($img2)) {
	    $imgUrl = "http://".$_SERVER['HTTP_HOST'].$rootDir.$img1;
	}else{
	    return false;
	}
	return $imgUrl;
    }else{
	return $img;
    }
}

if($checkoutCfg['useYn']!='y') exit;

$Partner = Core::loader('Partner');
$NaverCheckout = Core::loader('NaverCheckout');
$arr_category = $Partner-> getCatnm();
$nl = chr(10);

header('Content-Type: application/xml;charset=euc-kr');
echo ('<?xml version="1.0" encoding="euc-kr"?>'.$nl);


$tmp = explode('&',$_SERVER['QUERY_STRING']);
foreach($tmp as $v){
	parse_str($v);
	$arrItem[] = $ITEM_ID;
}

$shopDomain = 'http://'.$_SERVER['HTTP_HOST'];
$arrCategoryTags = array('first','second','third','fourth');
echo('<response>'.$nl);
foreach($arrItem as $goodsno)
{
    // 상품정보 전송
    $table1 = GD_GOODS;
    $table2 = GD_GOODS_OPTION;
    $table3 = GD_GOODS_LINK;
    $table4 = GD_CATEGORY;
    $arr_Category = array();

    $query = "SELECT a.goodsnm,a.img_s,a.img_i,a.img_m,a.img_l,a.longdesc,a.optnm,b.price,a.totstock,a.usestock,a.open,a.runout FROM $table1 a,$table2 b WHERE a.goodsno=b.goodsno and a.goodsno='$goodsno' and b.link and go_is_deleted <> '1' and go_is_display = '1' ";
    $res = $db->query($query);
    while($data = $db->fetch($res))
    {
	// 상품명의 금지어 체크
	if(!$NaverCheckout->check_banWords($data['goodsnm'])) continue;

	unset($thumb,$arrCategory,$img,$opt,$optnm);
	$query = "SELECT ".getCategoryLinkQuery('category', null, 'max')." FROM $table3 WHERE goodsno='$goodsno' order by sno limit 1";
	list($category) = $db->fetch($query);

	for($i=0;$i<strlen($category);$i=$i+3)
	{
		$end = $i+3;
		$tmp = substr($category,0,$end);
		$arrCategory[] = $arr_category[$tmp];
	}

	$optnm = explode('|',$data[optnm]);
	if(!$optnm[0])$optnm[0] = "옵션1";
	if(!$optnm[1])$optnm[1] = "옵션2";

	$res1 = $db->query("SELECT opt1 FROM $table2 WHERE goodsno='$goodsno' and go_is_deleted <> '1' and go_is_display = '1'  group by opt1");
	while($dataOpt1 = $db->fetch($res1)) if($dataOpt1['opt1']) $opt[0][]=$dataOpt1['opt1'];
	$res2 = $db->query("SELECT opt2 FROM $table2 WHERE goodsno='$goodsno' and go_is_deleted <> '1' and go_is_display = '1'  group by opt2");
	while($dataOpt2 = $db->fetch($res2)) if($dataOpt2['opt2']) $opt[1][]=$dataOpt2['opt2'];

	$url = $shopDomain.$cfg['rootDir']."/goods/goods_view.php?inflow=naverCheckout&amp;goodsno=".$goodsno;

	$img_l = get_img($data['img_l'],$cfg['rootDir']);
	$img_m = get_img($data['img_m'],$cfg['rootDir']);
	$img_s = get_img($data['img_s'],$cfg['rootDir']);
	$img_i = get_img($data['img_i'],$cfg['rootDir']);

	if($img_i) $img = $img_m;
	if($img_s) $img = $img_s;
	if($img_m) $img = $img_m;
	if($img_l) $img = $img_l;

	if($img_l) $thumb = $img_l;
	if($img_m) $thumb = $img_m;
	if($img_i) $thumb = $img_m;
	if($img_s) $thumb = $img_s;

	if(!$data['usestock']) $data['totstock']=10000;
	if(!$data['open'] || $data['runout']) $data['totstock']=0;
	if(!$img) continue;
	if(!$data['goodsnm']) continue;
	if(!$data['price']) continue;
	if(!$arrCategory[0]) continue;
	if(!$data['longdesc']) $data['longdesc'] = $data['goodsnm'];

	echo('<item id="'.$goodsno.'">'.$nl);
	echo('<name><![CDATA['.$data[goodsnm].']]></name>'.$nl);
	echo('<url>'.$url.'</url>'.$nl);
	echo('<description><![CDATA['.$data[longdesc].']]></description>'.$nl);
	echo('<image><![CDATA['.$img.']]></image>'.$nl);
	echo('<thumb><![CDATA['.$thumb.']]></thumb>'.$nl);
	echo('<price>'.$data['price'].'</price>'.$nl);
	echo('<quantity>'.$data['totstock'].'</quantity>'.$nl);
	if($opt):
	    echo('<options>'.$nl);
	    foreach($opt as $k1=>$v1)
	    {
		echo('<option name="'.$optnm[$k1].'">'.$nl);
		foreach($v1 as $k2=>$v2)
		{
		    echo('<select><![CDATA['.$v2.']]></select>'.$nl);
		}
		echo('</option>'.$nl);
	    }
	    echo('</options>'.$nl);
	endif;
	echo('<category>'.$nl);
	foreach($arrCategory as $k=>$v)
	{
	    echo('<'.$arrCategoryTags[$k].'><![CDATA['.$v.']]></'.$arrCategoryTags[$k].'>'.$nl);
	}
	echo('</category>'.$nl);
	echo('</item>'.$nl);

    }
}
echo('</response>');
?>
