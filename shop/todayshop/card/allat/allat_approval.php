<?
/*------------------------------------------------------------------------------
�� Copyright 2005,  Flyfox All right reserved.
@���ϳ���: All@Pay�� Plus 2.0 (Version 1.0.0.5) [2006-04-06]
@��������/������/������:
------------------------------------------------------------------------------*/

	include "../../../lib/library.php";
	//include "../../../conf/pg.allat.php";
	// �����̼� ������� ��� PG ���� ��ü
	resetPaymentGateway();
	include "./allatutil.php";

	### �����������̽��� ����� Get : ���� �ֹ��������������� Request Get
	$at_data	= "allat_shop_id=".urlencode($pg[id])."&allat_amt=$_POST[allat_amt]&allat_enc_data=$_POST[allat_enc_data]&allat_cross_key=$pg[crosskey]";
	$at_txt		= ApprovalReq($at_data,"NOSSL");	// ���� �ʿ� SSL
														// NOSSL - ���� �ڵ� 0212 �ϰ�� �����.
	$REPLYCD	= getValue("reply_cd",$at_txt);			//����ڵ�
	$REPLYMSG	= getValue("reply_msg",$at_txt);			//����޼���

	debug($_POST);
	debug($REPLYCD);
	debug($REPLYMSG);

	exit;
	/************************************************************************************************************/
	
	// ȯ�溯�� ����
	include "../../lib/library.php";
	$dbconn = connectdb();
	$curr_path=rootpath()."shop/";
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
	
	// Request Value Define
	$at_cross_key	= $g_cardsettle_CrossKey;	//�����ʿ�
	$at_shop_id		= $g_cardsettle_ID;			//�����ʿ�
	$at_amt			= $_POST["allat_amt"];		// [�߿�]���αݾ�(allat_amt) �ٽ� Setting 
												// allat_amt input���� �״�� Setting �ϴ� �� ���� 
												// ��ŷ ������ ���Ͽ� ��ٱ����� Session ���� �̿��ϴ� ���� ����
	
	// �����������̽��� ����� Get : ���� �ֹ��������������� Request Get
	//-------------------------------------------------------------------------
	$at_data	= "allat_shop_id=".urlencode($at_shop_id).                          
				"&allat_amt=".$at_amt. 
				"&allat_enc_data=".$_POST["allat_enc_data"].
				"&allat_cross_key=".$at_cross_key;
	$at_txt		= ApprovalReq($at_data,"SSL");		// ���� �ʿ� SSL
													// NOSSL - ���� �ڵ� 0212 �ϰ�� �����.
	
	$REPLYCD	=getValue("reply_cd",$at_txt);		//����ڵ�
	$REPLYMSG	=getValue("reply_msg",$at_txt);		//����޼���
	
	
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
	
	# ����ũ�� ����
	$escrowUseYN	= $_POST['allat_escrow_yn'];	// ����ũ�� ����
	
	# �ֹ� ��ȣ
	$orderNumber	=$_POST['allat_order_no'];
	
	if( !strcmp($REPLYCD,"0000") ){
		// reply_cd "0000" �϶��� ����
		$ORDER_NO       =getValue("order_no",$at_txt);			//�ֹ���ȣ
		$AMT            =getValue("amt",$at_txt);				//���αݾ�
		$PAY_TYPE       =getValue("pay_type",$at_txt);			//���Ҽ��� - 3D, ISP, NOR, ABANK
		$APPROVAL_YMDHMS=getValue("approval_ymdhms",$at_txt);	//�����Ͻ�
		$SEQ_NO         =getValue("seq_no",$at_txt);			//�ŷ��Ϸù�ȣ
		$APPROVAL_NO    =getValue("approval_no",$at_txt);		//���ι�ȣ
		$CARD_ID        =getValue("card_id",$at_txt);			//ī��ID - ī�������ڵ�(��:01,02,�� �� )
		$CARD_NM	    =getValue("card_nm",$at_txt);			//ī��� - ī��������(��:�Ｚ, ����, �� �� )
		$SELL_MM	    =getValue("sell_mm",$at_txt);			//�Һΰ���
		$ZEROFEE_YN	    =getValue("zerofee_yn",$at_txt);		//������(Y),�Ͻú�(N)
		$CERT_YN	    =getValue("cert_yn",$at_txt);			//�������� - ����(Y),������(N)
		$CONTRACT_YN	=getValue("contract_yn",$at_txt);		//�����Ϳ��� - 3�ڰ�����(Y),��ǥ������(N)
		$BANK_ID	    =getValue("bank_id",$at_txt);			//����ID
		$BANK_NM	    =getValue("bank_nm",$at_txt);			//�����
		$CASH_BILL_NO	=getValue("cash_bill_no",$at_txt);		//���ݿ������Ϸù�ȣ - ���ݿ����� ��Ͻ�
		$ESCROW_YN      =getValue("escrow_yn",$at_txt);			//����ũ�ο��� - Y(����ũ��), N(������)
		
		$Memo=getinfo("SELECT settlememo FROM ".$OrdTable." WHERE ordno='".$orderNumber."'");
		
		$msgadmemos=$Memo['settlememo']."�����ڵ�Ȯ�� : ����Ȯ�νð�(".date("Y-m-d H:i:s").")".chr(10);
		
		# ī�����
		if( $PAY_TYPE != "ABANK"){
			$msgadmemos.="�ŷ���ȣ : ".$SEQ_NO.chr(10);
			$msgadmemos.="����ڵ� : ".$REPLYCD." (0000�̸� ���� ����)".chr(10);
			$msgadmemos.="������� : ".$REPLYMSG.chr(10);
			$msgadmemos.="���ҹ�� : ".$PAY_TYPE.chr(10);
			$msgadmemos.="���ι�ȣ : ".$APPROVAL_NO.chr(10);
			$msgadmemos.="�ҺαⰣ : ".$SELL_MM.chr(10);
			$msgadmemos.="�������Һ� ���� : ".$ZEROFEE_YN." (������(Y),�Ͻú�(N))".chr(10);
			$msgadmemos.="�ſ�ī��� �ڵ� : ".$CARD_ID.chr(10);
			$msgadmemos.="�ſ�ī��� : ".$CARD_NM.chr(10);
			$msgadmemos.="�����Ͻ� : ".$APPROVAL_YMDHMS.chr(10);
			$msgadmemos.="�������� : ".$CERT_YN.chr(10);
			$msgadmemos.="����ũ�ο��� : ".$ESCROW_YN.chr(10);
			$msgadmemos.="��ǰ �ֹ���ȣ : ".$orderNumber.chr(10).chr(10);
		}
		#������ü
		if( $PAY_TYPE == "ABANK"){
			$msgadmemos.="�ŷ���ȣ : ".$SEQ_NO.chr(10);
			$msgadmemos.="����ڵ� : ".$REPLYCD." (0000�̸� ���� ����)".chr(10);
			$msgadmemos.="������� : ".$REPLYMSG.chr(10);
			$msgadmemos.="���ҹ�� : ".$PAY_TYPE.chr(10);
			$msgadmemos.="�����̸� : ".$BANK_NM.chr(10);
			$msgadmemos.="�����ڵ� : ".$BANK_ID.chr(10);
			$msgadmemos.="�����Ͻ� : ".$APPROVAL_YMDHMS.chr(10);
			$msgadmemos.="����ũ�ο��� : ".$ESCROW_YN.chr(10);
			$msgadmemos.="���ݿ����� �Ϸ� ��ȣ : ".$CASH_BILL_NO.chr(10);
			$msgadmemos.="��ǰ �ֹ���ȣ : ".$orderNumber.chr(10).chr(10);
		}
		
		# ����ũ�� ����
		$escrowUseYN	= $ESCROW_YN;
		
		# ����Ʈ ����
		if( !$etc_CardMode || $etc_CardMode == "" ){	// �Ϲݰ���
			include $rootpath."shop/proc/order_pointdown.php";
		}
		
		# �Ա� Ȯ��ó��
		$delstatuscd	= "03";
		
		# ī���������
		$ApprNo		= $APPROVAL_NO;		// ���ι�ȣ
		$TidNo		= $SEQ_NO;			// �ŷ���ȣ
		include $rootpath."shop/card/cardComplete.php";
		
		@mysql_Close();
		
		//echo "����ڵ�			: ".$REPLYCD."<br>";	
		//echo "����޼���		: ".$REPLYMSG."<br>";	    
		//echo "�ֹ���ȣ			: ".$ORDER_NO."<br>";	    
		//echo "���αݾ�			: ".$AMT."<br>";	    
		//echo "���Ҽ���			: ".$PAY_TYPE."<br>";	    
		//echo "�����Ͻ�			: ".$APPROVAL_YMDHMS."<br>";	    
		//echo "�ŷ��Ϸù�ȣ		: ".$SEQ_NO."<br>";	 
		//echo "=========== �ſ� ī�� ===========<br>";
		//echo "���ι�ȣ			: ".$APPROVAL_NO."<br>";
		//echo "ī��ID			: ".$CARD_ID."<br>";
		//echo "ī���			: ".$CARD_NM."<br>";
		//echo "�Һΰ���			: ".$SELL_MM."<br>";
		//echo "�����ڿ���		: ".$ZEROFEE_YN."<br>";
		//echo "��������			: ".$CERT_YN."<br>";
		//echo "�����Ϳ���		: ".$CONTRACT_YN."<br>";
		//echo "=========== ���� ��ü ===========<br>";
		//echo "����ID			: ".$BANK_ID."<br>";
		//echo "�����			: ".$BANK_NM."<br>";
		//echo "���ݿ����� �Ϸ� ��ȣ	: ".$CASH_BILL_NO."<br>";
		//echo "����ũ�� ���� ����	: ".$ESCROW_YN."<br>";		
		
	}else{
		
		$Memo=getinfo("SELECT settlememo FROM ".$OrdTable." WHERE ordno='".$orderNumber."'");
		
		$msgadmemos=$Memo['settlememo']."�������з� ���� �ڵ���� : �ڵ���ҽð�(".date("Y-m-d H:i:s").")".chr(10);
		$msgadmemos.="����ڵ� : ".$REPLYCD." (00�̸� ���� ����)".chr(10);
		$msgadmemos.="������� : ".$REPLYMSG.chr(10);
		
		# ī���������
		$FailMeg	= $REPLYMSG;		// ���и޽���
		$TidNo		= $SEQ_NO;			// �ŷ���ȣ
		include $rootpath."shop/card/cardComplete_fail.php";
		
		@mysql_Close();
		
		// reply_cd �� "0000" �ƴҶ��� ���� (�ڼ��� ������ �Ŵ�������)
		// reply_msg �� ���п� ���� �޼���
		//echo "����ڵ�  : ".$REPLYCD."<br>";	
		//echo "����޼���: ".$REPLYMSG."<br>";
	}
?>