<?

include "../conf/pg.inicis.php";
@include "../conf/pg.escrow.php";

### ����ũ�� ������ pgId ����
if ($_POST[escrow]=="Y") $pg[id] = $escrow[id];

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = str_replace("`", "'", $v[goodsnm]);
}
//��ǰ�� Ư������ �� �±� ����
$ordnm	= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " ��".($i-1)."��";
?>