<?php
include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include("../callback.php");

$P_STATUS;
$P_TR_NO;
$P_AUTH_DT;
$P_AUTH_NO;
$P_TYPE;
$P_MID;
$P_OID;
$P_AMT;
$P_HASH;
$P_DATA;

$P_STATUS    = get_param(PStateCd);   // 거래상태 : 0021(성공), 0031(실패), 0051(입금대기중)
$P_TR_NO     = get_param(PTrno);      // 거래번호
$P_AUTH_DT   = get_param(PAuthDt);    // 승인시간 
$P_AUTH_NO   = get_param(PAuthNo);    // 승인번호
$P_TYPE      = get_param(PType);      // 거래종류 (CARD, BANK, MOBILE, VBANK)
$P_MID       = get_param(PMid);       // 회원사아이디
$P_OID       = get_param(POid);       // 주문번호
$P_AMT       = get_param(PAmt);       // 거래금액
$P_HASH      = get_param(PHash);      // HASH 코드값
$P_FNNM		 = iconv('UTF-8','EUC-KR',get_param(PFnNm));		// 결제 은행
$P_DATA      = $P_STATUS.$P_TR_NO.$P_AUTH_DT.$P_TYPE.$P_MID.$P_OID.$P_AMT;

$ordno = $P_OID;

$query = "
select * from
	".GD_ORDER." a
	left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
where
	a.ordno='$ordno'
";
$data = $db->fetch($query);

include "../../../lib/cart.class.php";

$cart = new Cart($_COOKIE[gd_isDirect]);
$cart->chkCoupon();
$cart->delivery = $data['delivery'];
$cart->dc = $member[dc]."%";
$cart->calcu();
$cart -> totalprice += $data['price'];
### 주문확인메일
$data[cart] = $cart;
$data[str_settlekind] = $r_settlekind[$data[settlekind]];
$data['zipcode'] = ($data['zonecode']) ? $data['zonecode'] : $data['zipcode'];
	$dataSms = $data;

if($P_TYPE != 'VBANK' && $P_STATUS == "0021"){
	### 주문확인메일
	sendMailCase($data[email],0,$data);
	sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS

	### SMS 변수 설정
	sendMailCase($data[email],1,$data);			### 입금확인메일
	sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS

}else if($P_TYPE == 'VBANK' && $P_STATUS == "0021"){
	### SMS 변수 설정
	sendMailCase($data[email],1,$data);			### 입금확인메일
	sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
}else if($P_TYPE == 'VBANK' && $P_STATUS == "0051"){

	sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
	sendMailCase($data[email],0,$data);
}

if( ($data['step'] == 0 || $data['step'] == 1) && $data['step2'] == 0) {	//가상계좌 입금대기 또는 입금확인 시
	$url = "http://".$_SERVER['SERVER_NAME'].$cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=".$P_OID."&card_nm=".$P_FNNM;
}
else{
	$url = "http://".$_SERVER['SERVER_NAME'].$cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=".$P_OID;
}

?>
<html>
<head><title></title>
<script language="JavaScript">
<!--
	location.replace('<?=$url?>');
	self.close();
-->
</script>
</head>
<body>


<br><br>

</body>
</html>