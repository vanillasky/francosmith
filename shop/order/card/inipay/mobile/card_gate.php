<?php
/**
 * �̴Ͻý� PG ��� ������
 * �̴Ͻý� PG ���� : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inipay.php";

// ����� ������ ó��
$pg_mobile	= $pg;

// �Ϲ��ҺαⰣ (01:02:03:04:05:06:07:08:09:10:11:12 (������ : ))
// Ex) ����:�Ͻú�:2����:3����:4����:5����:6���� -> 1:2:3:4:5:6
$quota_mobile = preg_replace(array('/(^����:)|(����)/', '/�Ͻú�/'), array('', '1'), $pg_mobile['quota']);

// �����ڿ��� (merc_noint=Y (�Ϲݰ��� N, �����ڰ��� Y))
// �����ڱⰣ (noint_quota=12-2:3^14-2:3 (ī��-������:������^ī��-������))
// Ex) 12-2:3,14-3:4 -> 12-2:3^14-3:4
if ($pg_mobile['zerofee'] == 'yes' && $pg_mobile['zerofee_period'] != '') {
	$period = str_replace(',', '^', $pg_mobile['zerofee_period']);
	$zerofee_mobile = 'merc_noint=Y&noint_quota='.$period;
}
else {
	$zerofee_mobile = '';
}

// ��ǰ�� ����
if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
	$item	= $cart -> item;
}
foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = str_replace("`", "'", $v[goodsnm]);
}
if($i > 1)$ordnm .= " ��".($i-1)."��";
$ordnm	= pg_text_replace(strip_tags($ordnm));

// �������� �� URL
switch ($_POST['settlekind']){
	case "c":	// �ſ�ī��
		$actionURL		= "https://mobile.inicis.com/smart/wcard/";
		break;
	case "o":	// ������ü
		$actionURL		= "https://mobile.inicis.com/smart/bank/";
		break;
	case "v":	// �������
		$actionURL		= "https://mobile.inicis.com/smart/vbank/";
		break;
	case "h":	// �ڵ���
		$actionURL		= "https://mobile.inicis.com/smart/mobile/";
		break;
}

$url_noti = parse_url($_SERVER['HTTP_HOST']);
if($url_noti['path']) {
	$url_host = $url_noti['path'];
} else {
	$url_host = $url_noti['host'];
}
// ���� URL ����
$P_NEXT_URL		= ProtocolPortDomain().$cfg['rootDir']."/order/card/inipay/mobile/card_return.php?ordno=".$_POST['ordno']."&settlekind=".$_POST['settlekind'];
$P_NOTI_URL		= "http://".$url_host.$cfg['rootDir']."/order/card/inipay/mobile/vacctinput.php";
$P_RETURN_URL	= ProtocolPortDomain().$cfgMobileShop['mobileShopRootDir']."/ord/order_return_url.php?ordno=".$_POST['ordno'];

// �ڵ��� ��ȣ ó��
if (is_array($_POST['mobileOrder'])) {
	$mobileOrder	= implode('-', $_POST['mobileOrder']);
} else {
	$mobileOrder	= $_POST['mobileOrder'];
}
?>
<script language="javascript">
function on_card() {
	myform	= document.btpg_form;
	myform.action	= "<?php echo $actionURL;?>";
	myform.submit();
}
</script>

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>����� INIPay Mobile ����ȭ������ �̵��մϴ�.</b></strong></div>

<form name="btpg_form" method="post">
<input type="hidden" name="P_MID"			value="<?php echo $pg_mobile['id'];?>">				<!-- �������̵� -->
<input type="hidden" name="P_OID"			value="<?php echo $_POST['ordno'];?>">				<!-- �ֹ���ȣ -->
<input type="hidden" name="P_AMT"			value="<?php echo $_POST['settleprice'];?>">		<!-- �ŷ��ݾ� -->
<input type="hidden" name="P_UNAME"			value="<?php echo $_POST['nameOrder'];?>">			<!-- ���������� -->
<input type="hidden" name="P_NOTI"			value="<?php echo http_build_query(array('P_AMT'=>$_POST['settleprice']))?>">											<!-- ��Ÿ�ֹ����� -->

<input type="hidden" name="P_NEXT_URL"		value="<?php echo $P_NEXT_URL;?>">					<!-- ���� ����/���п� ���� ��� URL (VISA3D, ��Ÿ ���� ������ �ʼ�, ISP,������ü�� ��� ����) -->
<input type="hidden" name="P_NOTI_URL"		value="<?php echo $P_NOTI_URL;?>">					<!-- ���� ���� DB �� ���� URL (ISP / ������� / ������ü ������ ���Ǹ� �ʼ�) -->
<input type="hidden" name="P_RETURN_URL"	value="<?php echo $P_RETURN_URL;?>">				<!-- ���������� ������ ���� �״�� ��ȯ URL (ISP / ������ü �����ÿ��� ���Ǹ� �ʼ�) -->

<input type="hidden" name="P_GOODS"			value="<?php echo $ordnm;?>">						<!-- ������ǰ�� -->
<input type="hidden" name="P_MOBILE"		value="<?php echo $mobileOrder;?>">					<!-- ����� moblie ��ȣ -->
<input type="hidden" name="P_EMAIL"			value="<?php echo $_POST['email'];?>">				<!-- ����� e-mail ���� -->

<?php if ($_POST['settlekind'] == 'h') {?>
<input type="hidden" name="P_HPP_METHOD"	value="2">				<!-- ��ǰ ������ ���� (�޴��� ���� �� ��� �մϴ�. ��1�� : ������ ��2�� : �ǹ�) -->
<?php }?>
<input type="hidden" name="P_VBANK_DT"		value="">				<!-- ������� �Աݱ��� (�⺻ 10��) -->
<input type="hidden" name="P_CARD_OPTION"	value="">				<!-- ī�� ���� �ɼ� (���� �� ���õ� ī�尡 �켱������ �����˴ϴ�, ��)selcode=14 ) -->
<?php if ($_POST['settlekind'] == 'o') {?>
<input type="hidden" name="P_APP_BASE"		value="ON">				<!-- ������ü �� �ʼ� (��ON�� (����)) -->
<?php }?>
<input type="hidden" name="P_MLOGO_IMAGE"	value="">				<!-- ���� �ΰ� �̹��� -->
<input type="hidden" name="P_GOOD_IMAGE"	value="">				<!-- ��ǰ �̹��� -->
<input type="hidden" name="P_QUOTABASE"		value="<?php echo $quota_mobile;?>">				<!-- �Ϲ��ҺαⰣ -->
<input type="hidden" name="P_RESERVED"		value="<?php echo $zerofee_mobile;?>&disable_kpay=Y&block_isp=Y">				<!-- ���� parameter ���� -->
<input type="hidden" name="P_TAX"			value="">				<!-- �ΰ��� -->
<input type="hidden" name="P_TAXFREE"		value="">				<!-- ����� -->
</form>