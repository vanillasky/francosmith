<?
include "../conf/pg.easypay.php";
@include "../conf/pg.escrow.php";

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
	$good_info .= "seq=".$i.chr(31);
	$good_info .= "ordr_numb=".$ordno.$i.chr(31);
	$good_info .= "good_name=".addslashes(substr($v[goodsnm],0,30)).chr(31);
	$good_info .= "good_cntx=".$v[ea].chr(31);
	$good_info .= "good_amtx=".$v[price].chr(30);
}
if($i > 1)$ordnm .= " 외".($i-1)."건";
$ordnm	= pg_text_replace(strip_tags($ordnm));
//회원아이디
$m_id = $sess['m_id'];

?>