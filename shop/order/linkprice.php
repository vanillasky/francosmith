<?
### linkprice ½ÇÀû
if($sess[m_id]){
	$query = "
	select LPINFO,name,m_id from
		".GD_MEMBER."
	where
		m_id='{$sess[m_id]}'";
		$mbr_row = $db->fetch($query,1);
}
$tmp_linkprice_code = "";

if($mbr_row[LPINFO] != "" || $_COOKIE[LPINFO] != ""){

	$it_cnt = ""; $p_cd = ""; $p_nm = ""; $tmp="";
	$sales = "";
	$query = "select * from ".GD_ORDER_ITEM." where ordno='$_GET[ordno]'";
	$res = $db->query($query);
	while($item_row = $db->fetch($res)){

		$p_cd .= "{$item_row[goodsno]}||";
		$p_nm .= "{$item_row[goodsnm]}||";
		$it_cnt .= "{$item_row[ea]}||";
		$sales .= $item_row[price]*$item_row[ea]."||";

		if($mbr_row[LPINFO] != ""){
			$t_LPINFO = $mbr_row[LPINFO];
			$t_ID = $mbr_row[m_id];
			$t_NAME = $mbr_row['name'];
		}else{
			$t_LPINFO = $_COOKIE[LPINFO];
			$t_ID = 'guest';
			$t_NAME = $data[nameOrder];
		}
		$query = "select count(sno) ccnt from ".LINKPRICE_ORDER." where ORDER_CODE = '$_GET[ordno]' and PRODUCT_CODE = '$item_row[goodsno]'";
		$cc_row = $db->fetch($query);
		if($cc_row[ccnt] == 0){
			$query = "insert into ".LINKPRICE_ORDER." (`HHMMISS`,`LPINFO`,`ORDER_CODE`,`PRODUCT_CODE`,`COUNT`,`PRICE`,`PRODUCT_NAME`,`ID`,`NAME`)
			values(now(),'$t_LPINFO','$_GET[ordno]','$item_row[goodsno]','$item_row[ea]','$item_row[price]','$item_row[goodsnm]','$t_ID','$t_NAME')";
			$db->query($query);
		}
	}
	$p_cd = substr($p_cd,0,-2);
	$p_nm = substr($p_nm,0,-2);
	$it_cnt = substr($it_cnt,0,-2);
	$sales = substr($sales,0,-2);

	$tmp = "{$linkprice[joburl]}?a_id={$t_LPINFO}&m_id=".$linkprice['sid']."&mbr_id={$t_ID}({$t_NAME})&o_cd={$_GET[ordno]}&p_cd={$p_cd}&it_cnt={$it_cnt}&sales={$sales}&p_nm={$p_nm}";
	require_once("../partner/lpbase64.php");
	$code = $linkprice[code];
	$pad = $linkprice[pad];
	$linkprice_url = lp_url_trt($tmp, $code, $pad);

	$query = "select count(sno) ccnt from ".LINKPRICE_ORDER." where ORDER_CODE = '$_GET[ordno]' and sendyn = 'Y'";
	$cc_row = $db->fetch($query);
	if($cc_row[ccnt] == 0){
		$tmp_linkprice_code =  "<script src=\"{$linkprice_url}\"></script>";
		$query = "update ".LINKPRICE_ORDER." set sendyn = 'Y' where ORDER_CODE = '$_GET[ordno]'";
		$db->query($query);
	}
}
if($tmp_linkprice_code) $linkprice_code .= $tmp_linkprice_code;
?>