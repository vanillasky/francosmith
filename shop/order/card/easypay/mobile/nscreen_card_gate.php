<?php
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
$shopdir=dirname(__FILE__)."/../../../../";
include($shopdir.'/conf/config.php');
include($shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
require_once($shopdir.'/order/card/easypay/easypay_client.php');


$page_type = $_GET['page_type'];

// ����� ������ ó��
$pg_mobile	= $pg;

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


// �������� �� �ڵ�
switch ($_POST['settlekind']){
	case "c":	// �ſ�ī��
		$pay_type="11";
		break;
	case "v":	// �������
		$pay_type="22";
		break;
	case "h":	// �ڵ���
		$pay_type="31";
		break;
}

// �ڵ��� ��ȣ ó��
if (is_array($_POST['mobileOrder'])) {
	$mobileOrder	= implode('-', $_POST['mobileOrder']);
} else {
	$mobileOrder	= $_POST['mobileOrder'];
}

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
<script language="javascript">

</script>

<!--<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>����� Easypay Mobile ����ȭ������ �̵��մϴ�.</b></strong></div>-->

<!-- ������û URL �Դϴ�.�ݵ�� �׽�Ʈ/������ �����Ͻñ� �ٶ��ϴ�. -->
<form name="frm_pay" method="post" action="https://sp.easypay.co.kr/main/MainAction.do">   <!-- ���� -->

<!-- text �ʵ� START -->
<!-- [����]������ �� �̸� �� �Է½� KICC�� ��ϵ� ������ �� ���-->
<input type='hidden' name="sp_mall_nm"  			value='00'>
<!-- [�ʼ�]��ȭ�ڵ� (��ȭ:'00', �޷�:'01' ) -->
<input type='hidden' name="sp_currency"  			value='00'>
<!-- [�ʼ�]�������� ����  -->
<input type="hidden" name="sp_agent_ver"         	value="PHP">
<!-- [�ʼ�]����� IP   -->
<input type='hidden' name="sp_client_ip"  			value='<?=$_SERVER['REMOTE_ADDR']?>'>
<!-- [�ʼ�]����ó�� ����(�����Ұ�) -->
<input type='hidden' name="sp_tr_cd"			    value='00101000'>
<!-- [�ʼ�]�ſ�ī�� �������� (�����Ұ�) -->
<input type='hidden' name="sp_card_txtype"			value='20'>
<!-- [�ʼ�]���� URL(sample �ҽ��� �ִ� easypay_request.php ȣ��)-->
<input type='hidden' name="sp_return_url"  			value='<?=$Protocol.$Host.$Port?>/shop/order/card/easypay/mobile/nscreen_card_return.php?page_type=<?=$page_type?>'>
<!-- [����]��밡��ī�� ����Ʈ  -->
<input type="hidden" name="sp_usedcard_code">
<!-- [����]������ CI URL  -->
<input type="hidden" name="sp_ci_url"         		value="sp_ci_url">
<!-- [����]����(�ѱ���/���� ����)  -->
<input type="hidden" name="sp_lang_flag"         	value="KOR">
<!-- [����] ����� ������ �����ʵ� 1  -->
<input type="hidden" name="sp_mobilereserved1"      value="MobileReserved1">
<!-- [����] ����� ������ �����ʵ� 2  -->
<input type="hidden" name="sp_mobilereserved2"      value="MobileReserved2">
<!-- [����] ������ �����ʵ� 1  -->
<input type="hidden" name="sp_reserved1"         	value="Reserved1">
<!-- [����] ������ �����ʵ� 2  -->
<input type="hidden" name="sp_reserved2"         	value="Reserved2">
<!-- [����] ������ �����ʵ� 3  -->
<input type="hidden" name="sp_reserved3"         	value="Reserved3">
<!-- [����] ������ �����ʵ� 4  -->
<input type="hidden" name="sp_reserved4"         	value="Reserved4">
<!-- text �ʵ� END   -->
<input type="hidden" name="sp_mall_id"			value="<?php echo $pg_mobile['id'];?>">				<!-- �������̵� -->
<input type="hidden" name="sp_order_no"			value="<?php echo $_POST['ordno'];?>">				<!-- �ֹ���ȣ -->
<input type='hidden' name="sp_user_id"	  value='<?php	echo  $_SESSION['sess']['m_id']; ?>'>
<input type="hidden" name="sp_user_nm"			value="<?php echo $_POST['nameOrder'];?>">			<!-- ���������� -->
<input type="hidden" name="sp_user_mail"			value="<?php echo $_POST['email'];?>">				<!-- ����� e-mail ���� -->
<input type="hidden" name="sp_product_nm"			value="<?php echo $ordnm;?>">						<!-- ������ǰ�� -->
<input type="hidden" name="sp_pay_mny"			value="<?php echo $_POST['settleprice'];?>">		<!-- �ŷ��ݾ� -->
<input type="hidden" name="sp_pay_type"  value="<?=$pay_type;?>" >
<input type="hidden" name="sp_product_type"	value="0">				<!-- ��ǰ ������ ����   -->
<input type="hidden" name="sp_tcode"		value="SKT">			<!--��Ż� ����Ʈ-->
 <input type="hidden" name="usedcard_code" value="029"  >
<input type="hidden" name="usedcard_code" value="027"  >
<input type="hidden" name="usedcard_code" value="031"  >
<input type="hidden" name="usedcard_code" value="008"  >
<input type="hidden" name="usedcard_code" value="026"  >
<input type="hidden" name="usedcard_code" value="016"  >
<input type="hidden" name="usedcard_code" value="047"  >
<input type="hidden" name="usedcard_code" value="018"  >
<input type="hidden" name="usedcard_code" value="006"  >
<input type="hidden" name="usedcard_code" value="022"  >
<input type="hidden" name="usedcard_code" value="021"  >
<input type="hidden" name="usedcard_code" value="002"  >
<input type="hidden" name="sp_noint_yn"	value="<?php echo $pg_mobile['zerofee'];?>">				<!-- ������ ��뿩��-->
<input type="hidden" name="sp_noinst_term"	value="<?php echo $pg_mobile['zerofee_period'];?>">				<!-- ������ ����-->

<input type="hidden"   name="sp_quota" value="<?php echo $pg_mobile['quota'];?>" size="35" /><!-- �Һ� ���� -->
<input type="hidden" name="sp_user_phone2"		value="<?php echo str_replace("-","",$mobileOrder);?>">					<!-- ����� moblie ��ȣ -->
<input type="hidden" name="sp_version" value="0" /><!--��-->
<input type="hidden" name="sp_user_type" value="1" /><!--����ڱ���-->
</form>
<script type="text/javascript">
<!--
function f_submit() {
	var frm_pay = document.frm_pay;

	/* ���������ī�帮��Ʈ */
	var usedcard_code = "";
	for( var i=0; i < frm_pay.usedcard_code.length; i++) {

			usedcard_code += frm_pay.usedcard_code[i].value + ":";

	}
	frm_pay.sp_usedcard_code.value = usedcard_code;
	frm_pay.submit();
}
//-->
</script>