<?

//include "../conf/pg.inicis.php";
@include "../conf/pg.escrow.php";

// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

### ����ũ�� ������ pgId ����
if ($_POST[escrow]=="Y") $pg[id] = $escrow[id];

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
if($i > 1)$ordnm .= " ��".($i-1)."��";
?>