<?

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inicis.php";

$pg_mobile = $pg;

### ����ũ�� ������ pgId ����
if ($_POST[escrow]=="Y") $pg_mobile[id] = $escrow[id];

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
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
<input type="hidden" name="P_NEXT_URL" value="http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/todayshop/card/inicis/mobile/card_return.php">

<!-- ISP ���� -->
<input type="hidden" name="P_NOTI_URL" value="http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/todayshop/card/inicis/mobile/vacctinput.php">
<input type="hidden" name="P_RETURL_URL" value="http://<?=$_SERVER['HTTP_HOST']?><?=$cfgMobileShop['mobileShopRootDir']?>/ord/order_end.php">

<input type="hidden" name="P_EMAIL" value="<?=$_POST["email"]?>">
<input type="hidden" name="P_MOBILE" value="<?=$_POST['mobileOrder']?>">
<input type="hidden" name="P_GOODS" value="<?=$ordnm?>">
<input type="hidden" name="P_OID" value="<?=$_POST['ordno']?>">
<input type="hidden" name="P_NOTI" value="">
<input type="hidden" name="P_UNAME" value="<?=$_POST["nameOrder"]?>">
<input type="hidden" name="P_MID" value="<?=$pg_mobile['id']?>">
<input type="hidden" name="P_AMT" value="<?=$_POST['settleprice']?>">
</form>

<script>$(document).ready(on_load);</script>