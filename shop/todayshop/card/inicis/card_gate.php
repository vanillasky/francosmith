<?

//include "../conf/pg.inicis.php";
@include "../conf/pg.escrow.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

### 에스크로 결제시 pgId 변경
if ($_POST[escrow]=="Y") $pg[id] = $escrow[id];

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
if($i > 1)$ordnm .= " 외".($i-1)."건";
?>