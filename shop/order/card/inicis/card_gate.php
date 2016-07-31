<?

include "../conf/pg.inicis.php";
@include "../conf/pg.escrow.php";

### 에스크로 결제시 pgId 변경
if ($_POST[escrow]=="Y") $pg[id] = $escrow[id];

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = str_replace("`", "'", $v[goodsnm]);
}
//상품명에 특수문자 및 태그 제거
$ordnm	= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " 외".($i-1)."건";
?>