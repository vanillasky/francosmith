<?
/*------------------------------------------------------------------------------
�� Copyright 2005,  Flyfox All right reserved.
@���ϳ���: All@Pay�� Plus 2.0 (Version 1.0.0.5) ����ũ�� ���� ��۵�� [2006-04-06]
@��������/������/������:
------------------------------------------------------------------------------*/

# ���̺귯�� ȭ�� ȣ��
include_once "../../lib/library.php";

$curr_path=rootpath()."shop/";
$rootpath_cut1=substr($rootpath,sizeOf($rootpath)-1,-1);

session_start();

//-- ���θ��⺻��������
include $curr_path."conf/public_configure.php"; 
?>
<?=$license_topview?>
<html>
<title><?=$g_malltitle?></title>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<?if($getUseSTYLESHEET[stylesheet]==""){?>
<link rel="styleSheet" Href="<?=$rootpath?>shop/css/css.css">
<?}else{?>
<style type="text/css">
<?=$getUseSTYLESHEET[stylesheet];?>
</style>
<?}?>
</META>
</head>
<?
{	# All@Pay�� Plus �⺻ ����
	$strSQL		= "SELECT allat FROM tb_addmallinfo WHERE sno='1'";
	$getData	= getinfo($strSQL);
	$g_cardsettle_ID			=$getData[allat];				# All@Pay ID
	$g_cardsettle_test			="N";							# �׽�Ʈ ���� - �׽�Ʈ(Y),����(N)
	
	$Domain_url	="http://".$_SERVER['HTTP_HOST']."/";
	$reconPage	=$Domain_url."shop/card/allat/allat_approval_escrow.php";		// ����ó�� ������
}

{	# �ֹ� ���� ����
	
	# �ֹ� ���� ȣ��
	$strSQL = "SELECT TidNo,escrowInvno,escrowTrans FROM tb_order WHERE ordno='".$_GET['ordno']."'";
	$getData=getinfo( $strSQL );
	
	# ����¡ by Baberina 2005.09.05
	$PageIndexing		= "tic=".time()."&ordno=$ordno";
	$PageIndexing		= $_SERVER['PHP_SELF']."?".$PageIndexing;
}

//	if(dfm.allat_seq_no.value == ""){
//		alert("�ŷ���ȣ�� �������ϴ�.")
//		return;
//	}
//	if(dfm.allat_escrow_send_no.value == ""){
//		alert("������ȣ�� �������ϴ�.")
//		return;
//	}
//	if(dfm.allat_escrow_express_nm.value == ""){
//		alert("�ù���̸��� �������ϴ�.")
//		return;
//	}
//
?>
<script language=JavaScript src="https://tx.allatpay.com/common/allatpayX.js"></script>
<script language="Javascript">
function ftn_escrowcheck(dfm) {
	var ret;
	
	ret = invisible_eschk(dfm);//Function ���ο��� submit�� �ϰ� �Ǿ�����.
	if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
		// ���� �ڵ� : 0001~9998 �� ������ ���ؼ� ������ ó���� ���ֽñ� �ٶ��ϴ�.
		alert(ret.substring(4,ret.length));		// Message ��������
	}
	if( ret.substring(0,4)=="9999" ){
		// ���� �ڵ� : 9999 �� ������ ���ؼ� ������ ó���� ���ֽñ� �ٶ��ϴ�.
		alert(ret.substring(8,ret.length));	    // Message ��������	  	
	}
}
</script>
<table width="100%" border=0 align="center" cellpadding=6 cellspacing=0>
<form name=fm method=post action="<?=$reconPage?>">
<input type=hidden name=allat_shop_id value="<?=$g_cardsettle_ID?>">	<!--���� ���̵�-->
<input type=hidden name=allat_order_no value="<?=$_GET['ordno']?>">		<!--�ֹ���ȣ : ��۵���� ���ŷ����� �ֹ���ȣ-->
<input type=hidden name=allat_pay_type value="ABANK">					<!--���ŷ����� ������� : ī��(CARD), ������ü(ABANK) -> ����, ����ũ�δ� ������ü�� �����-->
<input type=hidden name=allat_enc_data value="">						<!--�ֹ�������ȣȭ�ʵ�-->
<input type=hidden name=allat_opt_pin value="NOVIEW">					<!--�þ������ʵ�-->
<input type=hidden name=allat_opt_mod value="WEB">						<!--�þ������ʵ�-->
<input type=hidden name=allat_test_yn value="<?=$g_cardsettle_test?>">	<!-- �׽�Ʈ ���� : �׽�Ʈ(Y),����(N) -->
<input type=hidden name=returnOrderUrl value="<?=$PageIndexing?>">		<!--���Ͻ�ũ��Ʈ : �߰��Ѱ���-->
<input type=hidden name=etc_CardMode value="<?=$etc_CardMode?>">					<!-- �Ϲݰ���, �����, ��Ÿ������ ���� �ʵ� - �߰����ذ��� -->
  <tr> 
    <td height="40" colspan="6"  class="m_a"><img src="<?=$curr_path?>admin/images_prime/no1.gif" width="23" height="17" align="absbottom"><font color="595147"><b>����ũ�� ��۵��</b></font></td>
  </tr>
  <tr><td height="1" colspan="6" bgcolor="#DDDDDD"></td></tr>
  <tr> 
    <td width="15%" bgcolor="#F5F2F0">&nbsp;<img src="<?=$curr_path?>admin/images_prime/icon_03.gif" width="10" height="5" align="absmiddle"> �ŷ���ȣ</td>
    <td width="15%"> <input name="allat_seq_no" type="text" size="13" class=xx value="<?=$getData['TidNo']?>"></td>
    <td width="15%" bgcolor="#F5F2F0">&nbsp;<img src="<?=$curr_path?>admin/images_prime/icon_03.gif" width="10" height="5" align="absmiddle"> ������ȣ</td>
    <td width="15%"> <input name="allat_escrow_send_no" type="text" size="13" class=xx value="<?=$getData['escrowInvno']?>"></td>
    <td width="15%" bgcolor="#F5F2F0">&nbsp;<img src="<?=$curr_path?>admin/images_prime/icon_03.gif" width="10" height="5" align="absmiddle"> �ù��</td>
    <td width="15%"> <input name="allat_escrow_express_nm" type="text" size="13" class=xx value="<?=$getData['escrowTrans']?>"></td>
  </tr>
  <tr><td height="1" colspan="6" bgcolor="#DDDDDD"></td></tr>
</table>
<table width="100%" border=0 cellPadding=0 cellSpacing=0>
  <TR> 
    <td height=30 align="center"><input type=button value=" ������ȣ ��Ͽ�û " name=app_btn onClick="javascript:ftn_escrowcheck(document.fm);"></TD>
  </tr>
</form>
</TABLE>