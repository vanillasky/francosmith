<?

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inicis.php";

$pg_mobile = $pg;

### ����ũ�� ������ pgId ����
if ($_POST[escrow]=="Y") $pg_mobile[id] = $escrow[id];

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

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
//��ǰ�� Ư������ �� �±� ����
$ordnm	= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " ��".($i-1)."��";

switch ($_POST[settlekind]){
	case "c":	// �ſ�ī��
		$actionURL		= "https://mobile.inicis.com/smart/wcard/";
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
?>
<script language="javascript">
function on_load()
{
	curr_date = new Date();
	year = curr_date.getYear();
	month = curr_date.getMonth();
	day = curr_date.getDay();
	hours = curr_date.getHours();
	mins = curr_date.getMinutes();
	secs = curr_date.getSeconds();
	/****************************************************************************
	ȸ���翡�� ����ϴ� �ֹ���ȣ�� �̿��ϴ� ��쿡�� ������ �ּ�ó�� �ϼ���
	�ּ�ó���Ͻ� ��쿡�� form tag�� P_OID�� ���� �Ѱ��ּž� �մϴ� ****************************************************************************/
//	document.btpg_form.P_OID.value = year.toString() + month.toString() + day.toString() + hours.toString() + mins.toString() + secs.toString();
}

function on_card() {
	myform = document.btpg_form;
	/****************************************************************************
	�ſ�ī�� action url�� �Ʒ��� ���� �����մϴ�
	****************************************************************************/
	myform.action = "<?=$actionURL;?>";
	myform.submit();
}
</script>

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>����� INIPay Mobile ����ȭ������ �̵��մϴ�.</b></strong></div>

<form name="btpg_form" method="post">

<!-- VISA3D ���� -->
<input type="hidden" name="P_NEXT_URL" value="<?=ProtocolPortDomain()?><?=$cfg['rootDir']?>/order/card/inicis/mobile/card_return.php<?php echo '?ordno='.$_POST['ordno'].'&settlekind='.$_POST['settlekind']; ?>">

<!-- ISP ���� -->
<input type="hidden" name="P_NOTI_URL" value="http://<?=$url_host?><?=$cfg['rootDir']?>/order/card/inicis/mobile/vacctinput.php">
<input type="hidden" name="P_RETURN_URL" value="<?=ProtocolPortDomain()?><?=$cfgMobileShop['mobileShopRootDir']?>/ord/order_return_url.php?ordno=<?=$_POST['ordno']?>">

<input type="hidden" name="P_EMAIL" value="<?=$_POST["email"]?>">
<input type="hidden" name="P_MOBILE" value="<?=$_POST['mobileOrder']?>">
<input type="hidden" name="P_GOODS" value="<?=$ordnm?>">
<input type="hidden" name="P_OID" value="<?=$_POST['ordno']?>">
<input type="hidden" name="P_NOTI"	value="<?php echo http_build_query(array('P_AMT'=>$_POST['settleprice']))?>">
<input type="hidden" name="P_UNAME" value="<?=$_POST["nameOrder"]?>">
<input type="hidden" name="P_MID" value="<?=$pg_mobile['id']?>">
<input type="hidden" name="P_AMT" value="<?=$_POST['settleprice']?>">
<input type="hidden" name="P_HPP_METHOD" value="2">
<input type="hidden" name="P_QUOTABASE" value="<?php echo $quota_mobile;?>"> <!-- �Ϲ��ҺαⰣ -->
<input type="hidden" name="P_RESERVED" value="<?php echo $zerofee_mobile;?>&disable_kpay=Y&block_isp=Y"> <!-- ���� parameter ���� -->
<input type="hidden" name="P_TAX" value=""> <!-- �ΰ��� -->
<input type="hidden" name="P_TAXFREE" value=""> <!-- ����� -->
</form>

<script>$(document).ready(on_load);</script>