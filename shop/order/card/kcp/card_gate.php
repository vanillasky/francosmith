<?
include "../conf/pg.kcp.php";
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
//상품명에 특수문자 및 태그 제거
$ordnm	= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " 외".($i-1)."건";

if($pg[receipt] == 'Y' && $_POST[settleprice] > 0 && ($_POST[settlekind]=="o" || $_POST[settlekind]=="v"))$pg[receipt] = 'Y';
else $pg[receipt] = 'N';

## 무이자 설정값
if( $pg[zerofee] == 'yes' ){ $pg[zerofeeFl] = 'Y'; }
else if( $pg[zerofee] == 'admin' ) { $pg[zerofeeFl] = ''; }
else { $pg[zerofeeFl] = 'N';}

?>
