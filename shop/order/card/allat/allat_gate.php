<?

### All@Pay�� Plus 2.0

//include "../conf/pg.allat.php";

$ordno			= $_POST[ordno];
$settlekind		= $_POST[settlekind];
$settleprice	= $_POST[settleprice];

$pg	= array(
	'id'		=> '',
	'formkey'	=> '',
	'crosskey'	=> '',
	);

$pg['zerofee']	= "N";								// ������ ���� (Y/N)
$pg['quota']	= "0:2:3:4:5:6:7:8:9:10:11:12";		// �ҺαⰣ
$pg['bonus']	= "N";								// ���ʽ�����Ʈ ��뿩�� (Y/N)
$pg['cert']		= "N";								// ī�� �������� (Y:����,N:����������,X:���������)
$pg['tax']		= "N";								// �������� (Y/N) - ���ݿ��������� �ʿ� (N:�̻���)
$pg['test']		= "N";								// �׽�Ʈ ���� (Y:�׽�Ʈ,N:�Ǽ���)
$pg['real']		= "Y";								// ��ǰ �ǹ����� (Y:�ǹ�,N:�ǹ��ƴ�)
$pg['escrow']	= "N";								// ����ũ�� ��뿩�� (Y:���,N:�̻��)

?>

<script language=JavaScript src="https://tx.allatpay.com/common/allatpayX.js"></script>
<!--//----------------------�ʱ�ȭ------------------------------//-->
<script language=JavaScript>
// ���� �ʿ�(ShopId,������ ����[Y/N])
ALLAT_INIT_FUNC("<?=$pg['id']?>","<?=$pg['zerofee']?>");
</script>
<!--//-------------------- ISP ���� -----------------------------//-->
<script language=JavaScript src="http://www.vpay.co.kr/KVPplugin.js"></script>
<script language=JavaScript>
StartSmartUpdate();
</script>
<!--//-------------------- 3D ����  -----------------------------//-->
<script language=JavaScript src="https://www.isaackorea.net/update/ILKactx.js"></script>
<script language=JavaScript ID=ALLAT_3D_JS></script>s
<!--//-------------------- ������ü ----------------------------//--->
<script language=javascript src="http://www.bankpay.or.kr/KFTCWallet.js"></script>
<script language=javascript>InstallCertManager()</script>
<script language=javascript>SmartUpdate()</script>
<script language=Javascript>
function ftn_app(dfm) {
	var ret;
	var app_type = "";
	<? if ($settlekind=="c"){ // ī����� ?>
	for( i=0; i< dfm.chkapp.length; i++ ){
		if( dfm.chkapp[i].checked == true ){
			app_type = dfm.chkapp[i].value;
			break;
		}
	}
	<? } else { ?>
	app_type = dfm.chkapp.value;
	<? } ?>

	// �Ϲ� ī�� �������� ����/���� ����
	if( dfm.allat_business[0].checked == true ){
		dfm.allat_business_type.value = 0;
	}
	if( dfm.allat_business[1].checked == true ){
		dfm.allat_business_type.value = 1;
	}

	<? if ($pg['tax'] == "Y"){ // ���ݿ����� ��û ���� ?>
	// ������ü �������� ���ݿ����� ��� ���� ����
	if( dfm.allat_cash[0].checked == true ){
		dfm.allat_cash_yn.value = "Y";
	}
	if( dfm.allat_cash[1].checked == true ){
		dfm.allat_cash_yn.value = "N";
	}
	<? } ?>

	if(app_type == "ISP"){
		ret = invisible_ISP(dfm);		//Function ���ο��� submit�� �ϰ� �Ǿ�����.
	}else if(app_type == "C3D"){
		ret = invisible_3D(dfm);		//Function ���ο��� submit�� �ϰ� �Ǿ�����.
	}else if(app_type == "NOR"){
		ret = invisible_NOR(dfm);		//Function ���ο��� submit�� �ϰ� �Ǿ�����.
	}else if(app_type == "ABANK"){
		ret = invisible_ABANK(dfm);		//Function ���ο��� submit�� �ϰ� �Ǿ�����.
	}else{
		alert("app_type Error"+app_type);
	return;
	}

	if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
		// ���� �ڵ� : 0001~9998 �� ������ ���ؼ� ������ ó���� ���ֽñ� �ٶ��ϴ�.
		alert(ret.substring(4,ret.length));		// Message ��������
	}
	if( ret.substring(0,4)=="9999" ){
		// ���� �ڵ� : 9999 �� ������ ���ؼ� ������ ó���� ���ֽñ� �ٶ��ϴ�.
		alert(ret.substring(8,ret.length));	    // Message ��������
	}
}

//-----����â ���� Script -------//
function chk_app(what){
	if(what == "ISP"){
		NOR_3D.style.display	= "none";
		NOR.style.display		= "none";
		ABANK.style.display		= "none";
	}else if(what == "C3D"){
		NOR_3D.style.display	= "";
		NOR.style.display		= "none";
		ABANK.style.display		= "none";
	}else if(what == "NOR"){
		NOR_3D.style.display	= "";
		NOR.style.display		= "";
		ABANK.style.display		= "none";
	}else if(what == "ABANK"){
		NOR_3D.style.display	= "none";
		NOR.style.display		= "none";
		ABANK.style.display		= "";
	}else{
		return;
	}
}
</script>

<!------------- HTML : Form ���� --------------//-->
<form name="fm" method=POST action="/shop/card/allat/allat_approval.php"> 			<!--���ο�û �� ������������� ���� //-->
<input type=hidden name=allat_shop_id value="<?=$pg[id]?>">							<!-- ���� ID -->
<input type=hidden name=allat_order_no value="<?=$ordno?>">							<!-- �ֹ���ȣ -->
<input type=hidden name=allat_amt value="<?=$settleprice?>">						<!-- ���αݾ� -->
<input type=hidden name=allat_pmember_id value="<?=$sess[m_id]?>">					<!-- ȸ��ID : ���θ��� ȸ��ID (�ִ� 20 bytes) -->
<input type=hidden name=allat_product_cd value="<?=$good_code?>">					<!-- ��ǰ�ڵ� : ������ǰ�� ��� ��ǥ��ǰ�� ��� (�ִ� 50 bytes) -->
<input type=hidden name=allat_product_nm value="<?=$getProd?>">						<!-- ��ǰ�� : ������ǰ�� ��� ��ǥ��ǰ�� ��� (�ִ� 100 bytes)-->
<input type=hidden name=allat_buyer_nm value="<?=$ordernm?>">						<!-- �����ڼ��� : (�ִ� 20 bytes)-->
<input type=hidden name=allat_email_addr value="<?=$email?>">						<!-- ������ Email -->
<input type=hidden name=allat_recp_nm value="<?=$recevernm?>">						<!-- �����μ��� -->
<input type=hidden name=allat_recp_addr value="<?=$address1?> <?=$exaddress1?>">	<!-- �������ּ� -->

<input type=hidden name=allat_test_yn value="<?=$g_cardsettle_test?>">				<!-- �׽�Ʈ ���� : �׽�Ʈ(Y),����(N) -->
<input type=hidden name=allat_real_yn value="<?=$g_cardsettle_real?>">				<!-- ��ǰ �ǹ� ���� : ��ǰ�� �ǹ��� ��� (Y), ��ǰ�� �ǹ��� �ƴҰ�� (N) -->
<input type=hidden name=allat_escrow_yn value="<?=$escrowUseYN?>">					<!-- ��ǰ ����ũ�� ���� : ����(Y),������(N)-->

<input type=hidden name=etc_CardMode value="<?=$etc_CardMode?>">					<!-- �Ϲݰ���, �����, ��Ÿ������ ���� �ʵ� - �߰����ذ��� -->

<!-- ��ȣȭ �ʵ� -->
<input type=hidden name=allat_enc_data value="">									<!-- �ڵ����� (�Ŀ� ��ȣȭ�� ���Ϲ޴� �ʵ�) -->
<input type=hidden name=allat_opt_pin value="NOVIEW">								<!-- ������ NOVIEW : �þ������ʵ� -->
<input type=hidden name=allat_opt_mod value="WEB">									<!-- ������ WEB : �þ������ʵ� -->

<!-- ī�� ������ �ʼ� �ʵ�, ���� ��ü�ÿ��� ������� ���� -->
<input type=hidden name=allat_zerofee_yn value="<?=$g_cardsettle_ZeroFee?>">		<!-- �Ϲ�/������ �Һ� ��뿩�� : �Ϲ�(N), ������ �Һ�(Y) -->
<input type=hidden name=KVP_QUOTA value="">											<!-- �Һΰ����� : �ڵ����� -->
<input type=hidden name=allat_bonus_yn value="<?=$g_cardsettle_Bonus?>">			<!-- ���ʽ�����Ʈ ��� ���� : ���(Y), ��� ����(N) - Default : N -->
<input type=hidden name=allat_cardcert_yn value="<?=$g_cardsettle_Cert?>">			<!-- ī�� ���� ���� :  : ����(Y), ���� ������(N), ������ ���(X) -->

<!------- ISP : ISP �������̽� ȣ��� �ʿ��� ���� �� ���ϰ� -->
<input type=hidden name="KVP_QUOTA_INF" value="<?=$g_cardsettle_Quota?>">			<!-- �Ϲ��Һ� ���� �� : (0:2:3:4:5:6:7:8:9:10:11:12)-->
<input type=hidden name="KVP_PGID" value="A0024">									<!-- ����ڵ� : ������ (A0024) -->
<input type=hidden name="KVP_CURRENCY" value="WON">									<!-- ȭ����� : ������ (WON)-->
<input type=hidden name="KVP_OACERT_INF" value="NONE">								<!-- ���� ������ ���� �ּ� �ݾ� -->
<input type=hidden name="KVP_NOINT_INF" value="">									<!-- ������ �Һ����� : �ڵ����� -->
<input type=hidden name="KVP_GOODNAME" value="">									<!-- ��ǰ�� : �ڵ����� -->
<input type=hidden name="KVP_PRICE" value="">										<!-- ���αݾ� : �ڵ����� -->
<input type=hidden name="KVP_NOINT" value="">										<!-- ������ �Һΰ� : �ڵ����� -->
<input type=hidden name="KVP_CARDCODE" value="">									<!-- KVP ī���ڵ� : �ڵ����� -->
<input type=hidden name="KVP_CONAME" value="">										<!-- ���޻�� : �ڵ����� -->
<input type=hidden name="KVP_IMGURL" value="">										<!-- ������ -->
<input type=hidden name="KVP_CARD_PREFIX" value="">									<!-- ī�� PREFIX : �ڵ����� -->
<input type=hidden name="KVP_SESSIONKEY" value="">									<!-- Session Key : �ڵ����� -->
<input type=hidden name="KVP_ENCDATA" value="">										<!-- Encryption Data : �ڵ����� -->
<input type=hidden name="KVP_RESERVED1" value="">									<!-- �����ʵ�1 -->
<input type=hidden name="KVP_RESERVED2" value="">									<!-- �����ʵ�2 -->
<input type=hidden name="KVP_RESERVED3" value="">									<!-- �����ʵ�3 -->

<!-- ������ü ���񽺽� �߰� -->

<input type=hidden name=allat_tax_yn value="EFT">									<!-- �������� -->
<input type=hidden name=hd_pre_msg_type value="EFT">								<!-- ������ : EFT -->
<input type=hidden name=hd_msg_code value="0200">									<!-- ������ : 0200 -->
<input type=hidden name=hd_msg_type value="EFT">									<!-- ��û���� : ������ : EFT-->
<input type=hidden name=hd_ep_type value="">										<!-- ������ �α��� : �ڵ����� -->
<input type=hidden name=hd_pi value="">												<!-- ��ȣȭ�� �� : �ڵ����� -->
<input type=hidden name=hd_approve_no value="20000035">								<!-- ����ڵ� : ������ : 20000035 -->
<input type=hidden name=hd_serial_no value="">										<!-- �ŷ����й�ȣ : �ڵ����� -->
<input type=hidden name=hd_firm_name value="<?=$g_mallnm?>">						<!-- ���θ� �̸� -->
<input type=hidden name=tx_amount value="">											<!-- ���ο�û�ݾ� : �ڵ����� -->

<table width="100%" border="0" cellspacing="0" cellpadding="6" style="border:1px #f3f3f3 solid">
  <tr>
    <td bgcolor="fafafa">
      <table width="100%" border="0" cellspacing="0" cellpadding="8">
        <tr>
          <td width="100" align="right" valign="top"><font color="" size="3"><b><?=$settlekindStr?><?=$escrowUseStr?><br>�����Է�</b></font></td>
          <td valign="top" bgcolor="#FFFFFF" style="padding-left:12px">

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26" valign="top" style="padding-top:4px">��������</td>
                <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <? if($settlekindType == "Y"){	// �ſ�ī�� ?>
                    <tr>
                      <td><input type=radio name="chkapp" value="ISP" onclick="javascript:chk_app('ISP')"> <b>ISP ����</b></td>
                    </tr>
                    <tr>
                      <td style="letter-spacing:-1;padding-left:24px">����, BC, �츮(��ȭ)</td>
                    </tr>
                    <tr>
                      <td><input type=radio name="chkapp" value="C3D" onclick="javascript:chk_app('C3D')"> <b>�Ƚ�Ŭ��</b></td>
                    </tr>
                    <tr>
                      <td style="letter-spacing:-1;padding-left:24px">�Ｚ, LG, ��ȯ, ����, ����, �Ե�, �ϳ�, �ѹ�, ����(����), �ż���, ����, ����, ����, ����, ��Ƽ</td>
                    </tr>
                    <tr>
                      <td><input type=radio name="chkapp" value="NOR" onclick="javascript:chk_app('NOR')"> <b>�Ϲݰ���</b></td>
                    </tr>
                    <tr>
                      <td style="letter-spacing:-1;padding-left:24px">���, ����, ����, �ؿ�(VISA, MASTER, JCB, AMEX, DINERS)</td>
                    </tr>
                    <?}else if($settlekindType == "N"){			// ������ü ?>
                    <tr>
                      <td><input type=radio name="chkapp" value="ABANK" onclick="javascript:chk_app('ABANK')" checked> <b>������ü</b></td>
                    </tr>
                    <?}?>
                    <tr>
                      <td height="8"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <?if($escrowUseYN == "Y"){?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">����ũ�� ������ȣ</td>
                <td><input type="text" name="allat_escrow_no" value="" maxlength="10" size="10" class="box"> ����ũ�� Ȯ�νÿ� ����� (4~10�ڸ� ����,�������)</td>
              </tr>
            </table>
            <?}?>
            <div id="NOR_3D" style="{display:none}">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">ī���ȣ</td>
                <td><input type="text" name="allat_card_no" value="" maxlength="16" size="20" class="box"> ("-"���� ���ڸ� �Է�)</td>
              </tr>
              <tr>
                <td height="26">�Һΰ��� ��</td>
                <td>
                  <select name="allat_sell_mm">
<?
		$QuotaArr	= explode(":",$g_cardsettle_Quota);
		$QuotaCnt	= sizeof($QuotaArr);
		if($totalPrice < 50000) $QuotaCnt = 1;

		for($ii = 0; $ii < $QuotaCnt; $ii++){
			if(strlen($QuotaArr[$ii]) < 2){
				$QuotaArr[$ii] = "0".$QuotaArr[$ii];
			}
			if($QuotaArr[$ii] == "00"){
				$QuotaArrStr = "�Ͻú�";
			}else{
				$QuotaArrNum = $QuotaArr[$ii];
				$QuotaArrStr = $QuotaArrNum . " ����";
			}
			echo"<option value='".$QuotaArr[$ii]."'>".$QuotaArrStr."</option>";
		}
?>
                  </select> (5�����̻� ����)
                </td>
              </tr>
            </table>
            </div>
            <div id="NOR" style="{display:none}">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">ī����ȿ�Ⱓ</td>
                <td><input type="text" name="allat_cardvalid_ym" value="" maxlength="4" size="6" class="box"> (��� ��)2006�� 04�� �̸� 0604)</td>
              </tr>
              <tr>
                <td height="26">ī���й�ȣ</td>
                <td><input type="text" name="allat_passwd_no" value="" maxlength="2" size="4" class="box">XX (�� ���ڸ��� �Է��� �ּ���)</td>
              </tr>
              <tr>
                <td height="26">����/���� ����</td>
                <td>
                  <input type="radio" name="allat_business" value="0" checked> ����
                  <input type="radio" name="allat_business" value="1"> ����
                  <input type=hidden name=allat_business_type value="">
                </td>
              </tr>
              <tr>
                <td height="26">�ֹι�ȣ</td>
                <td>&nbsp;XXXXXX - <input type="text" name="allat_registry_no" value="" maxlength="7" size="10" class="box"> (���ڶ� �Է�) �� ������ ���
                </td>
              </tr>
              <tr>
                <td height="26">����ڹ�ȣ</td>
                <td><input type="text" name="allat_biz_no" value="" maxlength="30" size="24" class="box"> ("-"���� ���ڸ� �Է�) �� ������ ���
                </td>
              </tr>
            </table>
            </div>
            <div id="ABANK" style="{display:none}">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">�����ָ�</td>
                <td><input type="text" name="allat_account_nm" value="" maxlength="30" size="16" class="box"></td>
              </tr>
              <? if($g_cardsettle_Tax == "Y"){	// ���ݿ����� ��û ���� ?>
              <tr>
                <td height="26">���ݿ����� ���</td>
                <td>
                  <input type="radio" name="allat_cash" value="Y" checked> ���
                  <input type="radio" name="allat_cash" value="N"> �̵��
                  <input type=hidden name=allat_cash_yn value="">
                </td>
              </tr>
              <?}else{?>
              <input type=hidden name=allat_cash_yn value="N">
              <?}?>
              <tr>
                <td height="26">��������</td>
                <td><input type="text" name="allat_cert_no" value="" maxlength="13" size="16" class="box"> �� �ڵ�����ȣ OR �ֹι�ȣ OR ����ڹ�ȣ ("-"���� ���ڸ� �Է�)</td>
              </tr>
            </table>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="130" height="26">�����ݾ�</td>
                <td><font color="EC6F4F"><strong><?=number_format($totalPrice)?>��</strong></font></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

    </td>
  </tr>
</table>
<table width=100% cellpadding=0 cellspacing=0 border=0 height="60">
  <tr>
    <td align=center>
      <input type=button value="�����ϱ�" Onclick="javascript:ftn_app(document.fm);" class=xx>&nbsp;&nbsp;
      <input type=button value="����ϱ�" Onclick="javascript:location.replace('<?=$curr_path?>card/pg_cancel.php?ordno=<?=$ordno?>&etc_CardMode=<?=$etc_CardMode?>');" class=xx>
    </td>
  </tr>
</table>
</form>
<? if($settlekindScript == "Y"){			// ������ü ?>
<script language=Javascript>
chk_app('ABANK');
</script>
<?}?>