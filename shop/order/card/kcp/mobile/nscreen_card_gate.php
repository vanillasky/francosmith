<?php
 ### kcp ����� ����

	@include dirname(__FILE__)."/../../../../conf/pg.kcp.php";
	@include dirname(__FILE__)."/../../../../conf/pg_mobile.kcp.php";
	@include dirname(__FILE__)."/../../../../conf/pg.escrow.php";

	$page_type = $_GET['page_type'];

	if(is_array($pg_mobile)) {
		$pg_mobile = array_merge($pg_mobile, $pg);
	}
	else {
		$pg_mobile = $pg;
	}

	if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
	}

	foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
	$good_info .= "seq=".$i.chr(31);
	$good_info .= "ordr_numb=".$ordno.$i.chr(31);
	$good_info .= "good_name=".addslashes(substr($v[goodsnm],0,30)).chr(31);
	$good_info .= "good_cntx=".$v[ea].chr(31);
	$good_info .= "good_amtx=".$v[price].chr(30);
	}
	//��ǰ�� Ư������ �� �±� ����
	$ordnm	= pg_text_replace(strip_tags($ordnm));
	if($i > 1)$ordnm .= " ��".($i-1)."��";

	## ������ ������
	if( $pg_mobile[zerofee] == 'yes' ){ $pg_mobile[zerofeeFl] = 'Y'; }
	else if( $pg_mobile[zerofee] == 'admin' ) { $pg_mobile[zerofeeFl] = ''; }
	else { $pg_mobile[zerofeeFl] = 'N';}

?>
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   ȯ�� ���� ���� Include END                                               = */
    /* ============================================================================== */

	$g_conf_home_dir  = $_SERVER['DOCUMENT_ROOT'].$cfg[rootDir]."/order/card/kcp/mobile/receipt";     // BIN ������ �Է� (bin������)
	$g_conf_gw_url    = "paygw.kcp.co.kr";
    $g_conf_site_cd   = $pg[id];
	$g_conf_site_key  = $pg[key];
	$g_conf_site_name = "KCP SHOP";
	$g_conf_gw_port   = "8090";        // ��Ʈ��ȣ(����Ұ�)
	$module_type      = "01";          // ����Ұ�
	/* ============================================================================== */
    /* = ����Ʈ�� SOAP ��� ����                                                     = */
    /* =----------------------------------------------------------------------------= */
    /* = �׽�Ʈ �� : KCPPaymentService.wsdl                                         = */
    /* = �ǰ��� �� : real_KCPPaymentService.wsdl                                    = */
    /* ============================================================================== */
    $g_wsdl           = "real_KCPPaymentService.wsdl";
?>
<?
    /* kcp�� ����� kcp �������� ���۵Ǵ� ���� ��û ����*/
    $req_tx          = $_POST[ "req_tx"         ]; // ��û ����
    $res_cd          = $_POST[ "res_cd"         ]; // ���� �ڵ�
    $tran_cd         = $_POST[ "tran_cd"        ]; // Ʈ����� �ڵ�
    $ordr_idxx       = $_POST[ "ordno"      ]; // ���θ� �ֹ���ȣ
    $good_name       = $ordnm					; // ��ǰ��
    $good_mny        = $_POST[ "settleprice"       ]; // ���� �ѱݾ�
    $buyr_name       = $_POST[ "nameOrder"      ]; // �ֹ��ڸ�
    $buyr_tel1       = implode("-",$_POST['phoneOrder']); // �ֹ��� ��ȭ��ȣ
    $buyr_tel2       = implode("-",$_POST['mobileOrder']); // �ֹ��� �ڵ��� ��ȣ
    $buyr_mail       = $_POST[ "email"      ]; // �ֹ��� E-mail �ּ�
    $enc_info        = $_POST[ "enc_info"       ]; // ��ȣȭ ����
    $enc_data        = $_POST[ "enc_data"       ]; // ��ȣȭ ������

	/*
     * ��Ÿ �Ķ���� �߰� �κ� - Start -
     */
    $param_opt_1     = $_POST[ "param_opt_1"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    $param_opt_2     = $_POST[ "param_opt_2"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    $param_opt_3     = $_POST[ "param_opt_3"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    /*
     * ��Ÿ �Ķ���� �߰� �κ� - End -
     */

  $tablet_size     = "1.0"; // ȭ�� ������ ���� - ���ȭ�鿡 �°� ����(��������,�����е� - 1.85, ����Ʈ�� - 1.0)
	 ### �������� ���

	 $ipgm_date = date("Ymd",strtotime("now"."+3 days"));

	 switch ($_POST[settlekind]){	// ���� ���
		case "c":	// �ſ�ī��
			$use_pay_method		= "100000000000";
			$pay_method = "CARD";
			$paynm			= "�ſ�ī��";
			break;
//		case "o":	// ������ü
//			$use_pay_method		= "SC0030";
//			$pay_method = "";
//			$paynm			= "������ü";
//			break;
		case "v":	// �������
			$use_pay_method		= "001000000000";
			$pay_method = "VCNT";
			$paynm			= "�������";
			break;
		case "h":	// �ڵ���
			$use_pay_method		= "000010000000";
			$pay_method = "MOBX";
			$paynm			= "�ڵ���";
			break;
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
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma" content="No-Cache">
<meta name="viewport" content="width=device-width, user-scalable=<?=$tablet_size?>, initial-scale=<?=$tablet_size?>, maximum-scale=<?=$tablet_size?>, minimum-scale=<?=$tablet_size?>">
<link href="css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>
<!-- �ŷ���� �ϴ� kcp ������ ����� ���� ��ũ��Ʈ-->
<script language="javascript" src="<?=$cfg[rootDir]?>/order/card/kcp/mobile/approval_key.js" type="text/javascript"></script>

<style type="text/css">
	.LINE { background-color:#afc3ff }
	.HEAD { font-family:"����","����ü"; font-size:9pt; color:#065491; background-color:#eff5ff; text-align:left; padding:3px; }
	.TEXT { font-family:"����","����ü"; font-size:9pt; color:#000000; background-color:#FFFFFF; text-align:left; padding:3px; }
	    B { font-family:"����","����ü"; font-size:13pt; color:#065491;}
	INPUT { font-family:"����","����ü"; font-size:9pt; }
	SELECT{font-size:9pt;}
	.COMMENT { font-family:"����","����ü"; font-size:9pt; line-height:160% }
</style>

<script type="text/javascript">
  var controlCss = "css/style_mobile.css";
  var isMobile = {
    Android: function() {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  if( isMobile.any() )
    document.getElementById("cssLink").setAttribute("href", controlCss);
</script>

<script language="javascript">
	self.name = "tar_opener";

	/* kcp web ����â ȣ�� (����Ұ�)*/
    function call_pay_form()
    {

       var v_frm = document.sm_form;

        v_frm.action = PayUrl;

		if(v_frm.Ret_URL.value == "")
		{
			/* Ret_URL���� �� �������� URL �Դϴ�. */
			alert("������ Ret_URL�� �ݵ�� �����ϼž� �˴ϴ�.");
			return false;
		}
		else
        {
			v_frm.submit();
		}
	}


	/* kcp ����� ���� ���� ��ȣȭ ���� üũ �� ���� ��û*/
    function chk_pay()
    {
        /*kcp ������������ ������ �ֹ��������� ������ ���������� ����(����Ұ�)*/
        self.name = "tar_opener";

        var pay_form = document.pay_form;

        if (pay_form.res_cd.value == "3001" )
        {
            alert("����ڰ� ����Ͽ����ϴ�.");
            pay_form.res_cd.value = "";
            return false;
        }
        else if (pay_form.res_cd.value == "3000" )
        {
            alert("30���� �̻� ���� �Ҽ� �����ϴ�.");
            pay_form.res_cd.value = "";
            return false;
        }
		  if (pay_form.enc_info.value)
      {
        pay_form.submit();
      }
    }

</script>

<div id="content">

<form name="sm_form" method="POST">

<input type="hidden" name='good_name' maxlength="100" value='<?=strip_tags($good_name)?>'>
<input type="hidden" name='good_mny' size="9" maxlength="9" value='<?=$good_mny?>' >
<input type="hidden" name='buyr_name' size="20" maxlength="20" value="<?=$buyr_name?>">
<input type="hidden" name='buyr_tel1' size="20" maxlength="20" value='<?=$buyr_tel1?>'>
<input type="hidden" name='buyr_tel2' size="20" maxlength="20" value='<?=$buyr_tel2?>'>
<input type="hidden" name='buyr_mail' size="20" maxlength="30" value='<?=$buyr_mail?>'>

<!-- �ʼ� ���� -->

<!-- ��û ���� -->
<input type='hidden' name='req_tx'       value='pay'>
<!-- ����Ʈ �ڵ� -->
<input type="hidden" name='site_cd'      value="<?=$g_conf_site_cd?>">
<!-- ����Ʈ Ű -->
<input type='hidden' name='site_key'     value='<?=$g_conf_site_key?>'>
 <!-- ����Ʈ �̸� -->
<input type="hidden" name='shop_name'    value="<?=$g_conf_site_name?>">
<!-- ��������-->
<input type="hidden" name='pay_method'   value="<?=$pay_method?>">
<!-- �ֹ���ȣ -->
<input type="hidden"   name='ordr_idxx'    value="<?=$_POST['ordno']?>">
<!-- �ִ� �Һΰ����� -->
<input type="hidden" name='quotaopt'     value="12">
<!-- ��ȭ �ڵ� -->
<input type="hidden" name='currency'     value="410">
<!-- ������� Ű -->
<input type="hidden" name='approval_key' id="approval">
<!-- ���� URL (kcp�� ����� ������ ��û�� �� �ִ� ��ȣȭ �����͸� ���� ���� �������� �ֹ������� URL) -->
<!-- �ݵ�� ������ �ֹ��������� URL�� �Է� ���ֽñ� �ٶ��ϴ�. -->
<input type="hidden" name='Ret_URL'      value="<?=$Protocol.$Host.$Port?><?=$cfg['rootDir']?>/order/card/kcp/mobile/nscreen_card_return.php?page_type=<?=$page_type?>">
<!-- ������ �ʿ��� �Ķ����(����Ұ�)-->
<input type='hidden' name='ActionResult' value='<?=strtolower($pay_method)?>'>
<input type="hidden" name='approval_url' value="<?=$cfg[rootDir]?>/order/card/kcp/mobile/order_approval.php"/>
<!-- ������ �ʿ��� �Ķ����(����Ұ�)-->
<input type="hidden" name='escw_used'    value="N">
<!-- ������� ���� -->
<input type="hidden" name="ipgm_date"       value="<?=$ipgm_date?>"/>
<!-- ȭ�� ũ������ -->
<input type="hidden" name="tablet_size"     value="<?=$tablet_size?>">

<!-- ��Ÿ �Ķ���� �߰� �κ� - Start - -->
<input type="hidden" name='param_opt_1'	 value="<?=$param_opt_1?>"/>
<input type="hidden" name='param_opt_2'	 value="<?=$param_opt_2?>"/>
<input type="hidden" name='param_opt_3'	 value="<?=$param_opt_3?>"/>
<!-- ��Ÿ �Ķ���� �߰� �κ� - End - -->
<?php
	if ($use_pay_method	== "100000000000"){	// �ſ�ī�� �� ��
?>
<!-- ��� ī�� ���� //-->
<input type="hidden" name='used_card'    value="">

<!-- ������ �ɼ�
		�� �����Һ�    (������ ������ �������� ���� �� ������ ������ ������)                             - "" �� ����
		�� �Ϲ��Һ�    (KCP �̺�Ʈ �̿ܿ� ���� �� ��� ������ ������ �����Ѵ�)                           - "N" �� ����
		�� ������ �Һ� (������ ������ �������� ���� �� ������ �̺�Ʈ �� ���ϴ� ������ ������ �����Ѵ�)   - "Y" �� ���� //-->
<input type="hidden" name="kcp_noint"       value="<?=$pg_mobile['zerofeeFl']?>"/>

<!-- ������ ����
		�� ���� 1 : �Һδ� �����ݾ��� 50,000 �� �̻��� ��쿡�� ����
		�� ���� 2 : ������ �������� ������ �ɼ��� Y�� ��쿡�� ���� â�� ����
		��) �� ī�� 2,3,6���� ������(����,��,����,�Ｚ,����,����,�Ե�,��ȯ) : ALL-02:03:04
		BC 2,3,6����, ���� 3,6����, �Ｚ 6,9���� ������ : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04 //-->
<input type="hidden" name="kcp_noint_quota" value="<?=$pg_mobile['zerofee_period']?>"/>
<?php	 } ?>
</form>
</div>

<form name="pay_form" method="POST" action="<?=$cfg[rootDir]?>/order/card/kcp/mobile/nscreen_card_return.php?page_type=<?=$page_type?>">
    <input type="hidden" name="req_tx"         value="pay">      <!-- ��û ����          -->
    <input type="hidden" name="res_cd"         value="<?=$res_cd?>">      <!-- ��� �ڵ�          -->
    <input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">     <!-- Ʈ����� �ڵ�      -->
    <input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">   <!-- �ֹ���ȣ           -->
    <input type="hidden" name="good_mny"       value="<?=$good_mny?>">    <!-- �޴��� �����ݾ�    -->
    <input type="hidden" name="good_name"      value="<?=$good_name?>">   <!-- ��ǰ��             -->
    <input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">   <!-- �ֹ��ڸ�           -->
    <input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">   <!-- �ֹ��� ��ȭ��ȣ    -->
    <input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">   <!-- �ֹ��� �޴�����ȣ  -->
    <input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">   <!-- �ֹ��� E-mail      -->
    <input type="hidden" name="enc_info"       value="<?=$enc_info?>">    <!-- ��ȣȭ ����        -->
    <input type="hidden" name="enc_data"       value="<?=$enc_data?>">    <!-- ��ȣȭ ������      -->
    <input type="hidden" name="use_pay_method" value="<?=$use_pay_method?>">      <!-- ��û�� ���� ����   -->
	<input type="hidden" name="param_opt_1"	   value="<?=$param_opt_1?>">
	<input type="hidden" name="param_opt_2"	   value="<?=$param_opt_2?>">
	<input type="hidden" name="param_opt_3"	   value="<?=$param_opt_3?>">
</form>