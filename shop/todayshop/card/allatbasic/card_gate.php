<?

### All@Pay™ Basic

//include "../conf/pg.allatbasic.php";
@include "../conf/pg.escrow.php";

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

### 결제사용 여부
if ($_POST[settlekind] == "c") { $pg[CARD]	= "Y"; } else { $pg[CARD]	= "N";} //신용카드
if ($_POST[settlekind] == "o") { $pg[ABANK]	= "Y"; } else { $pg[ABANK]	= "N";} //계좌이체
if ($_POST[settlekind] == "v") { $pg[VBANK]	= "Y"; } else { $pg[VBANK]	= "N";} //가상계좌
if ($_POST[settlekind] == "h") { $pg[HP] = "Y"; }    else { $pg[HP]		= "N";} //핸드폰
//if ($_POST[settlekind] == "a") {	$paymentCode[VBANK] = "Y"; } else { $paymentCode[VBANK] = "N";} //무통장입금
z?>