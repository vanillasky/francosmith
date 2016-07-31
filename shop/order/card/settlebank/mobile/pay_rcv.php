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

$P_STATUS    = get_param(PStateCd);   // �ŷ����� : 0021(����), 0031(����), 0051(�Աݴ����)
$P_TR_NO     = get_param(PTrno);      // �ŷ���ȣ
$P_AUTH_DT   = get_param(PAuthDt);    // ���νð� 
$P_AUTH_NO   = get_param(PAuthNo);    // ���ι�ȣ
$P_TYPE      = get_param(PType);      // �ŷ����� (CARD, BANK, MOBILE, VBANK)
$P_MID       = get_param(PMid);       // ȸ������̵�
$P_OID       = get_param(POid);       // �ֹ���ȣ
$P_AMT       = get_param(PAmt);       // �ŷ��ݾ�
$P_HASH      = get_param(PHash);      // HASH �ڵ尪
$P_FNNM		 = iconv('UTF-8','EUC-KR',get_param(PFnNm));		// ���� ����
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
### �ֹ�Ȯ�θ���
$data[cart] = $cart;
$data[str_settlekind] = $r_settlekind[$data[settlekind]];
$data['zipcode'] = ($data['zonecode']) ? $data['zonecode'] : $data['zipcode'];
	$dataSms = $data;

if($P_TYPE != 'VBANK' && $P_STATUS == "0021"){
	### �ֹ�Ȯ�θ���
	sendMailCase($data[email],0,$data);
	sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS

	### SMS ���� ����
	sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
	sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS

}else if($P_TYPE == 'VBANK' && $P_STATUS == "0021"){
	### SMS ���� ����
	sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
	sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
}else if($P_TYPE == 'VBANK' && $P_STATUS == "0051"){

	sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
	sendMailCase($data[email],0,$data);
}

if( ($data['step'] == 0 || $data['step'] == 1) && $data['step2'] == 0) {	//������� �Աݴ�� �Ǵ� �Ա�Ȯ�� ��
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