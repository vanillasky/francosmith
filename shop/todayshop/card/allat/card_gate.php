<?

### All@Pay™ Plus 2.0

//include "../conf/pg.allat.php";
@include "../conf/pg.escrow.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

$pg['tax']		= "N";		// 과세여부 (Y/N) - 현금영수증사용시 필요 (N:미사용시)
$pg['test']		= "N";		// 테스트 여부 (Y:테스트,N:실서비스)
$pg['real']		= "Y";		// 상품 실물여부 (Y:실물,N:실물아님)

### 결제금액 5만원 이상시 할부가능
if ($_POST[settleprice]<50000) $pg['quota'] = "0";

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}else{
	if ($data[settleprice]<50000) $pg['quota'] = "0";
}
$i=0;

foreach($item as $v){
	$i++;
	if($i == 1){
		$ordnm = $v[goodsnm];
		$ordgoodsno = $v[goodsno];
	}
}
if($i > 1)$ordnm .= " 외".($i-1)."건";
?>