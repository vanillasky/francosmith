<?
### ��Ʋ��ũ
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.settlebank.php";
@include dirname(__FILE__)."/../../../../conf/conf/pg.escrow.php";

$page_type = $_GET['page_type'];

	// ��ǰ ����
	if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
		$item = $cart -> item;
	}
	foreach($item as $v){
		$i++;
		if($i == 1) $ordnm = $v['goodsnm'];
	}
	//��ǰ�� Ư������ �� �±� ����
	$ordnm	= pg_text_replace(strip_tags($ordnm));
	if($i > 1)$ordnm .= " ��".($i-1)."��";

//ssl ���ȼ��� ���� �߰�
if($_SERVER['SERVER_PORT'] == 80) {
	$Port = "";
} elseif($_SERVER['SERVER_PORT'] == 443) {
	$Port = "";
} else {
	$Port = $_SERVER['SERVER_PORT'];
}

if (strlen($Port)>0) $Port = ":".$Port;

$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';
$host = parse_url($_SERVER['HTTP_HOST']);

if ($host['path']) {
	$Host = $host['path'];
} else {
	$Host = $host['host'];
}

?>
<script>
webbrowser=navigator.appVersion;

var isIPHONE = (navigator.userAgent.match('iPhone') != null ||
		navigator.userAgent.match('iPod') != null);
var isIPAD = (navigator.userAgent.match('iPad') != null);
var isANDROID = (navigator.userAgent.match('Android') != null);

function submitForm()
{
	window.name = "STPG_CLIENT";
	var settle_payinfo = document.SETTLE_PAYINFO;

	<?if ($_POST[settlekind] == 'c') { //ī��?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/card/MbCardAction.do";
	<?}else if ($_POST[settlekind] == 'o') { //������ü?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/bank/MbBankAction.do";
	<?}else if ($_POST[settlekind] == 'h') { //�޴���?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/mobile/MbMobileAction.do";
	<?}else if ($_POST[settlekind] == 'v') { //�������?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/vbank/MbVBankAction.do?equ_gb=MB";
	<?}else {?>
		alert("���� ������ ���õ��� �ʾҽ��ϴ�.");
	<?}?>

	strEncode();//�ѱ����ڵ�

	settle_payinfo.submit();
}

//�Ķ���� ���� �ѱ��� ��� ���⼭ ���ڵ��� ���ش�.
function strEncode()
{
	var settle_payinfo = document.SETTLE_PAYINFO;
	settle_payinfo.PGoods.value = encodeURI(settle_payinfo.t_PGoods.value);
//	settle_payinfo.PNoti.value = encodeURI(settle_payinfo.t_PNoti.value);
	settle_payinfo.PMname.value = encodeURI(settle_payinfo.t_PMname.value);
	settle_payinfo.PUname.value = encodeURI(settle_payinfo.t_PUname.value);

	<?if ($_POST[settlekind] == 'v' || $_POST[settlekind] == 'o') {?>
		settle_payinfo.PBname.value = encodeURI(settle_payinfo.t_PBname.value);
	<?}?>

}
</script>

<form id="SETTLE_PAYINFO" name="SETTLE_PAYINFO" method="POST">
<!-- ���ó���� ���� �Ķ���� -->
<input type="hidden" name="PNoteUrl" value="<?=$Protocol.$Host.$Port?><?=$cfg['rootDir']?>/order/card/settlebank/mobile/nscreen_card_return.php?page_type=<?=$page_type?>"><!--���������� ��Ƽ���۹��� url -->
<input type="hidden" name="PNextPUrl" value="<?=$Protocol.$Host.$Port?><?=$cfg['rootDir']?>/order/card/settlebank/mobile/nscreen_pay_rcv.php?page_type=<?=$page_type?>"><!--��������/������ ȣ��� url-->
<input type="hidden" name="PCancPUrl" value="" ><!--������ҽ� ȣ��� url-->
<input type="hidden" name="PMid" value="<?=$pg['id']?>"><br> <!--<?=$pg['id']?>������id(�ʼ�)-->
<input type="hidden" name="PAmt" value="<?=$_POST['settleprice']?>"><br><!--�ݾ�(�ʼ�)-->
<input type="hidden" name="PPhone" value="<?if (implode('-',$_POST['mobileOrder'])){echo implode('-',$_POST['mobileOrder']); }else{ echo implode('-',$_POST['phoneOrder']); }?>"><br><!--�޴�����ȣ(�ʼ�)-->
<input type="hidden" name="PMobile" value=""><!--��Ż� skt,kt,lgt (�ʼ�)-->
<input type="hidden" name="POid" value="<?=$_POST['ordno']?>"><!--�ֹ���ȣ(�ʼ�)-->
<input type="hidden" name="PEname" value="<?if ($_POST['shopEng']){ echo $_POST['shopEng'];}else{echo "SETTLEBANK";}?>"><!--������������(�ʼ�)-->
<input type="hidden" name="PVtransDt" value="<?=date('Ymd', strtotime('+5 day'))?>"><!--��������Աݴ����ȿ�Ⱓ(�������ä���� �ʼ�)-->
<input type="hidden" name="PEmail" value="<?=$_POST['email']?>"><!--�� email-->
<input type="hidden" name="t_PUname" value="<?=$_POST['nameOrder']?>"> <!-- ������ �̸�-->
<input type="hidden" name="t_PGoods" value="<?=$ordnm?>"> <!-- ��ǰ�� -->
<input type="hidden" name="t_PMname" value="<?=$cfg['compName']?>"> <!-- ȸ���� �ѱ۸� -->
<input type="hidden" name="t_PBname" value="<?if ($cfg['compName']){ echo $cfg['compName'];}else{echo "SETTLEBANK";}?>"> <!--��������(������ü�����ʼ�)-->


<input type="hidden" name="PGoods">
<input type="hidden" name="PNoti">
<input type="hidden" name="PMname">
<input type="hidden" name="PUname">
<input type="hidden" name="PBname">
</form>