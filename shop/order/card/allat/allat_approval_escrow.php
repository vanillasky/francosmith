<?
/*------------------------------------------------------------------------------
�� Copyright 2005,  Flyfox All right reserved.
@���ϳ���: All@Pay�� Plus 2.0 (Version 1.0.0.5) [2006-04-06]
@��������/������/������:
------------------------------------------------------------------------------*/

	// ȯ�溯�� ����
	include_once "../../lib/library.php";
	$dbconn=connectdb();
	session_start();
	
	// ���θ� �⺻ ���� ȣ��
	$strSQL		= "SELECT * FROM tb_addmallinfo WHERE sno='1'";
	$getData	= getinfo($strSQL);
	$g_cardsettle_ID			=$getData[allat];				# All@Pay ID
	$g_cardsettle_FormKey		=$getData[allat_FormKey];		# All@Pay Form Key
	$g_cardsettle_CrossKey		=$getData[allat_CrossKey];		# All@Pay CrossKey
	
	// �þܰ��� �Լ� Include
	//----------------------
	include "./allatutil.php";
	
	// �����������̽��� ����� Get : ���� �ֹ��������������� Request Get
	//-------------------------------------------------------------------------
	$at_cross_key	= $g_cardsettle_CrossKey;	//�����ʿ�
	$at_shop_id		= $g_cardsettle_ID;			//�����ʿ�
	
	$at_data   = "allat_shop_id=".urlencode($at_shop_id).
                 "&allat_enc_data=".$_POST["allat_enc_data"].
                 "&allat_cross_key=".$at_cross_key ;  
	$at_txt = EscrowChkReq($at_data,"SSL"); //���� �ʿ� https(SSL),http(NOSSL)
	         
	$REPLYCD   =getValue("reply_cd",$at_txt);		//����ڵ�
	$REPLYMSG  =getValue("reply_msg",$at_txt);		//����޼���
	
	# ���� ��庰 ó��
	$etc_CardMode	=$_POST['etc_CardMode'];		// ���� ���
	if( !$etc_CardMode || $etc_CardMode == "" ){	// �Ϲݰ���
		$OrdTable	= "tb_order";
		$reorder	= "n";
	}else if( $etc_CardMode == "re" ){				// �����
		$OrdTable = "tb_order";
		$reorder	= "y";
	}else if( $etc_CardMode == "etc" ){				// ��Ÿ����
		$OrdTable = "tb_order_etc";
		$reorder	= "n";
	}
	
	# �ֹ� ��ȣ
	$orderNumber	=$_POST['allat_order_no'];
	
	$Memo=getinfo("SELECT settlememo FROM ".$OrdTable." WHERE ordno='".$orderNumber."'");
	$msgadmemos	=$Memo['settlememo'].chr(10);
	$msgadmemos.="��۵�� Ȯ�� : ��۵�� Ȯ�νð�(".date("Y-m-d H:i:s").")".chr(10);
	$msgadmemos.="����ڵ� : ".$REPLYCD." (0000�̸� ���� ����)".chr(10);
	$msgadmemos.="������� : ".$REPLYMSG.chr(10);
	
	if( !strcmp($REPLYCD,"0000") ){
		// reply_cd "0000" �϶��� ����
		$ESCROWCHECK_YMDSHMS=getValue("escrow_check_ymdhms",$at_txt);
		$msgadmemos.="����ũ�� ��� ������ : ".$ESCROWCHECK_YMDSHMS.chr(10);
		
		// �ֹ����� ������Ʈ
		$strSQL = "UPDATE ".$OrdTable." SET escrowTrans='".$_POST['allat_escrow_express_nm']."', escrowInvno='".$_POST['allat_escrow_send_no']."', settlememo='$msgadmemos' WHERE ordno='".$orderNumber."'";
		getinfo($strSQL,"handle");
		
		echo "<script>alert('Escrow ��۵���� �Ϸ� �Ǿ����ϴ�.');location.replace('".$_POST['returnOrderUrl']."');</script>";
		
	}else{
		
		// �ֹ����� ������Ʈ
		$strSQL = "UPDATE ".$OrdTable." SET settlememo='$msgadmemos' WHERE ordno='".$orderNumber."'";
		getinfo($strSQL,"handle");
		
		echo "<script>alert('".$REPLYMSG."�� ������ ���� Escrow ��۵���� �����Ͽ����ϴ�.');location.replace('".$_POST['returnOrderUrl']."');</script>";
	}
?>