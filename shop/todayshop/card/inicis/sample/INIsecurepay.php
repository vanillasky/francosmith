<?php


/* INIsecurepay.php
 *
 * �̴����� �÷������� ���� ��û�� ������ ó���Ѵ�.
 * ���� ��û�� ó���Ѵ�.
 * �ڵ忡 ���� �ڼ��� ������ �Ŵ����� �����Ͻʽÿ�.
 * <����> �������� ������ �ݵ�� üũ�ϵ����Ͽ� �����ŷ��� �����Ͽ� �ֽʽÿ�.
 * �ǽð� ������ü ������ ��� ��ü��� ���� WEB���α׷�(INIpayresult.php)�� �̿��Ͽ�
 * ���Ұ��(��ü���) ����Ÿ�� ���Ź޵��� �Ͻʽÿ�
 *  
 * http://www.inicis.com
 * Copyright (C) 2004 Inicis Co., Ltd. All rights reserved.
 */

	/**************************
	 * 1. ���̺귯�� ��Ŭ��� *
	 **************************/
	require("INIpay41Lib.php");
	
	
	/***************************************
	 * 2. INIpay41 Ŭ������ �ν��Ͻ� ���� *
	 ***************************************/
	$inipay = new INIpay41;



	/*********************
	 * 3. ���� ���� ���� *
	 *********************/
	$inipay->m_inipayHome = "/usr/local/INIpay41"; // �̴����� Ȩ���͸�
	$inipay->m_type = "securepay"; // ����
	$inipay->m_pgId = "INIpay".$pgid; // ����
	$inipay->m_subPgIp = "203.238.3.10"; // ����
	$inipay->m_keyPw = "1111"; // Ű�н�����(�������̵� ���� ����)
	$inipay->m_debug = "true"; // �α׸��("true"�� �����ϸ� �󼼷αװ� ������.)
	$inipay->m_mid = $mid; // �������̵�
	$inipay->m_uid = $uid; // INIpay User ID
	$inipay->m_uip = getenv("REMOTE_ADDR"); // ����
	$inipay->m_goodName = $goodname;
	$inipay->m_currency = $currency;
	$inipay->m_price = $price;
	$inipay->m_buyerName = $buyername;
	$inipay->m_buyerTel = $buyertel;
	$inipay->m_buyerEmail = $buyeremail;
	$inipay->m_payMethod = $paymethod;
	$inipay->m_encrypted = $encrypted;
	$inipay->m_sessionKey = $sessionkey;
	$inipay->m_url = "http://www.your_domain.co.kr"; // ���� ���񽺵Ǵ� ���� SITE URL�� �����Ұ�
	$inipay->m_cardcode = $cardcode; // ī���ڵ� ����
	$inipay->m_ParentEmail = $parentemail; // ��ȣ�� �̸��� �ּ�(�ڵ��� , ��ȭ�����ÿ� 14�� �̸��� ���� �����ϸ�  �θ� �̸��Ϸ� ���� �����뺸 �ǹ�, �ٸ����� ���� ���ÿ� ���� ����)
	/*-----------------------------------------------------------------*
	 * ������ ���� *                                                   *
	 *-----------------------------------------------------------------*
	 * �ǹ������ �ϴ� ������ ��쿡 ���Ǵ� �ʵ���̸�               *
	 * �Ʒ��� ������ INIsecurepay.html ���������� ����Ʈ �ǵ���        *
	 * �ʵ带 ����� �ֵ��� �Ͻʽÿ�.                                  *
	 * ������ ������ü�� ��� �����ϼŵ� �����մϴ�.                   *
	 *-----------------------------------------------------------------*/
	$inipay->m_recvName = $recvname;	// ������ ��
	$inipay->m_recvTel = $recvtel;		// ������ ����ó
	$inipay->m_recvAddr = $recvaddr;	// ������ �ּ�
	$inipay->m_recvPostNum = $recvpostnum;  // ������ �����ȣ
	$inipay->m_recvMsg = $recvmsg;		// ���� �޼���
	
	
	/****************
	 * 4. ���� ��û *
	 ****************/
	$inipay->startAction();
	
	
	/****************************************************************************************************************
	 * 5. ����  ���                                                    						*
	 *      													*
	 *  ��. ��� ���� ���ܿ� ����Ǵ� ���� ��� ����                                                        	*
	 * 	�ŷ���ȣ : $inipay->m_tid                                       					*
	 * 	����ڵ� : $inipay->m_resultCode ("00"�̸� ���� ����)           					*
	 * 	������� : $inipay->m_resultMsg (���Ұ���� ���� ����)          					*
	 * 	���ҹ�� : $inipay->m_payMethod (�Ŵ��� ����)  								*
	 * 	�����ֹ���ȣ : $inipay->m_moid										*
	 *														*
	 *  ��. �ſ�ī��,ISP,�ڵ���, ��ȭ ����, ���������ü, OK CASH BAG Point �����ÿ��� ���� ��� ���� 		*
	 *              (�������Ա� , ��ȭ ��ǰ��) 								*
	 * 	�̴Ͻý� ���γ�¥ : $inipay->m_pgAuthDate (YYYYMMDD)            					*
	 * 	�̴Ͻý� ���νð� : $inipay->m_pgAuthTime (HHMMSS)              					*	 
	 *  														*
	 *  ��. �ſ�ī��  ���������� �̿�ÿ���  ������� ����          						*
         *														*
	 * 	�ſ�ī�� ���ι�ȣ : $inipay->m_authCode                         					*
	 * 	�ҺαⰣ : $inipay->m_cardQuota                                 					*
	 * 	�������Һ� ���� : $inipay->m_quotaInterest ("1"�̸� �������Һ�) 					*
	 * 	�ſ�ī��� �ڵ� : $inipay->m_cardCode (�Ŵ��� ����)             					*
	 * 	ī��߱޻� �ڵ� : $inipay->m_cardIssuerCode (�Ŵ��� ����)       					*
	 * 	�������� ���࿩�� : $inipay->m_authCertain ("00"�̸� ����)      					*
	 *      ���� �̺�Ʈ ���� ���� : $inipay->m_eventFlag                    					*	
	 *														*	
	 *      �Ʒ� ������ "�ſ�ī�� �� OK CASH BAG ���հ���" �Ǵ�"�ſ�ī�� ���ҽÿ� OK CASH BAG����"�ÿ� �߰��Ǵ� ����* 
	 * 	OK Cashbag ���� ���ι�ȣ : $inipay->m_ocbSaveAuthCode           					*	
	 * 	OK Cashbag ��� ���ι�ȣ : $inipay->m_ocbUseAuthCode            					*
	 * 	OK Cashbag �����Ͻ� : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)   					*
	 * 	OCB ī���ȣ : $inipay->m_ocbcardnumber			   						*
	 * 	OK Cashbag ���հ���� �ſ�ī�� ���ұݾ� : $inipay->m_price1     					*
	 * 	OK Cashbag ���հ���� ����Ʈ ���ұݾ� : $inipay->m_price2       					*
	 *														*
	 * ��. OK CASH BAG ���������� �̿�ÿ���  ������� ����	 ���							*
	 * 	OK Cashbag ���� ���ι�ȣ : $inipay->m_ocbSaveAuthCode           					*	
	 * 	OK Cashbag ��� ���ι�ȣ : $inipay->m_ocbUseAuthCode            					*
	 * 	OK Cashbag �����Ͻ� : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)   					*
	 * 	OCB ī���ȣ : $inipay->m_ocbcardnumber			   						*
	 *														*
         * ��. ������ �Ա� ���������� �̿�ÿ���  ���� ��� ����							*
	 * 	������� ä���� ���� �ֹι�ȣ : $inipay->m_perno              					*
	 * 	������� ��ȣ : $inipay->m_vacct                                					*
	 * 	�Ա��� ���� �ڵ� : $inipay->m_vcdbank                           					*
	 * 	�Աݿ����� : $inipay->m_dtinput (YYYYMMDD)                      					*
	 * 	�۱��� �� : $inipay->m_nminput                                  					*
	 * 	������ �� : $inipay->m_nmvacct                                  					*
	 *														*	
	 * ��. �ڵ���, ��ȭ�����ÿ���  ���� ��� ���� ( "���� ���� �ڼ��� ����"���� �ʿ� , ���������� �ʿ���� ������)  *
         * 	��ȭ���� ����� �ڵ� : $inipay->m_codegw                        					*
	 *														*	
	 * ��. �ڵ��� ���������� �̿�ÿ���  ���� ��� ����								*
	 * 	�޴��� ��ȣ : $inipay->m_nohpp (�ڵ��� ������ ���� �޴�����ȣ)       					*
	 *														*
	 * ��. ��ȭ ���������� �̿�ÿ���  ���� ��� ����								*	
         * 	��ȭ��ȣ : $inipay->m_noars (��ȭ������  ���� ��ȭ��ȣ)      						*
         * 														*		
         * ��. ��ȭ ��ǰ�� ���������� �̿�ÿ���  ���� ��� ����							*
         * 	���� ���� ID : $inipay->m_cultureid	                           					*
         *														*
         * ��. ��� ���� ���ܿ� ���� ���� ���нÿ��� ���� ��� ���� 							*
         * 	�����ڵ� : $inipay->m_resulterrcode                             					*
         *														*
         ****************************************************************************************************************/

	
	
	/*******************************************************************
	 * 7. �������                                                     *
	 *                                                                 *
	 * ���� ����� DB � �����ϰų� ��Ÿ �۾��� �����ϴٰ� �����ϴ�  *
	 * ���, �Ʒ��� �ڵ带 �����Ͽ� �̹� ���ҵ� �ŷ��� ����ϴ� �ڵ带 *
	 * �ۼ��մϴ�.                                                     *
	 *******************************************************************/
	/*
	var $cancelFlag = "false";

	// $cancelFlag�� "ture"�� �����ϴ� condition �Ǵ��� ����������
	// �����Ͽ� �ֽʽÿ�.

	if($cancelFlag == "true")
	{
		$inipay->m_type = "cancel"; // ����
		$inipay->m_msg = "DB FAIL"; // ��һ���
		$inipay->startAction();
		if($inipay->m_resultCode == "00")
		{
			$inipay->m_resultCode = "01";
			$inipay->m_resultMsg = "DB FAIL";
		}
	}
	*/
		
	
?>


<!-------------------------------------------------------------------------------------------------------
 *  													*
 *       												*
 *        												*
 *	�Ʒ� ������ ���� ����� ���� ��� ������ �����Դϴ�. 				                *
 *	���� ���� ���ܺ� ��������� ������ ��µǵ��� �Ǿ� �����Ƿ� �ҽ� �ľ��� ����� ���             *
 *      ������ ��� ������ ���� �ִ� �����ڿ� ���������� (INIsecurepay_dev.php)�� ���� �Ͻñ� �ٶ��ϴ�.	*
 *													*
 *													*
 *													*
 -------------------------------------------------------------------------------------------------------->
 
<html>
<head>
<title>INIpay41 ���������� ����</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="stylesheet" href="css/group.css" type="text/css">
<style>
body, tr, td {font-size:10pt; font-family:����,verdana; color:#433F37; line-height:19px;}
table, img {border:none}

/* Padding ******/ 
.pl_01 {padding:1 10 0 10; line-height:19px;}
.pl_03 {font-size:20pt; font-family:����,verdana; color:#FFFFFF; line-height:29px;}

/* Link ******/ 
.a:link  {font-size:9pt; color:#333333; text-decoration:none}
.a:visited { font-size:9pt; color:#333333; text-decoration:none}
.a:hover  {font-size:9pt; color:#0174CD; text-decoration:underline}

.txt_03a:link  {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:visited {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:hover  {font-size: 8pt;line-height:18px;color:#EC5900; text-decoration:underline}
</style>

<script>
	var openwin=window.open("childwin.html","childwin","width=300,height=160");
	openwin.close();
	
	/*-------------------------------------------------------------------------------------------------------
         * 1. $inipay->m_resultCode 										*
         *       ��. �� �� �� ��: "00" �� ��� ���� ����[�������Ա��� ��� - ������ �������Ա� ��û�� �Ϸ�]	*
         *       ��. �� �� �� ��: "00"���� ���� ��� ���� ����  						*
         -------------------------------------------------------------------------------------------------------*/
	
	function show_receipt(tid) // ������ ���
	{
		if("<?php echo ($inipay->m_resultCode); ?>" == "00")
		{
			var receiptUrl = "https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=" + "<?php echo($inipay->m_tid); ?>" + "&noMethod=1";
			window.open(receiptUrl,"receipt","width=430,height=700");
		}
		else
		{
			alert("�ش��ϴ� ���������� �����ϴ�");
		}
	}
		
	function errhelp() // �� �������� ���
	{
		var errhelpUrl = "http://www.inicis.com/ErrCode/Error.jsp?result_err_code=" + "<?php echo($inipay->m_resulterrcode); ?>" + "&mid=" + "<?php echo($inipay->m_mid); ?>" + "&tid=<?php echo($inipay->m_tid); ?>" + "&goodname=" + "<?php echo($inipay->m_goodName); ?>" + "&price=" + "<?php echo($inipay->m_price); ?>" + "&paymethod=" + "<?php echo($inipay->m_payMethod); ?>" + "&buyername=" + "<?php echo($inipay->m_buyerName); ?>" + "&buyertel=" + "<?php echo($inipay->m_buyerTel); ?>" + "&buyeremail=" + "<?php echo($inipay->m_buyerEmail); ?>" + "&codegw=" + "<?php echo($inipay->m_codegw); ?>";
		window.open(errhelpUrl,"errhelp","width=520,height=150, scrollbars=yes,resizable=yes");
	}
	
</script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>
<body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0><center> 
<table width="632" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="85" background=<?php 

/*-------------------------------------------------------------------------------------------------------
 * ���� ����� ���� ��� �̹����� ���� �ȴ�								*
 * 	 ��. ���� ���� �ÿ� "img/spool_top.gif" �̹��� ���						*
 *       ��. ���� ����� ���� ��� �̹����� ����							*
 *       	��. �ſ�ī�� 	- 	"img/card.gif"							*
 *		��. ISP		-	"img/card.gif"							*
 *		��. �������	-	"img/bank.gif"							*
 *		��. �������Ա�	-	"img/bank.gif"							*
 *		��. �ڵ���	- 	"img/hpp.gif"							*
 *		��. ��ȭ���� (ars��ȭ ����)	-	"img/phone.gif"					*
 *		��. ��ȭ���� (�޴���ȭ����)	-	"img/phone.gif"					*
 *		��. OK CASH BAG POINT		-	"img/okcash.gif"				*
 *		��. ��ȭ��ǰ��	-	"img/ticket.gif"						*
 -------------------------------------------------------------------------------------------------------*/
    					
    				if($inipay->m_resultCode == "01"){
					echo "img/spool_top.gif";
				}
				else{
					
    					switch($inipay->m_payMethod){
	
						case(Card): // �ſ�ī��
							echo "img/card.gif";
							break;
						case(VCard): // ISP
							echo "img/card.gif";
							break;
						case(HPP): // �޴���
							echo "img/hpp.gif";
							break;
						case(Ars1588Bill): // 1588
							echo "img/phone.gif";
							break;
						case(PhoneBill): // ����
							echo "img/phone.gif";
							break;
						case(OCBPoint): // OKCASHBAG
							echo "img/okcash.gif";
							break;
						case(DirectBank):  // ���������ü
							echo "img/bank.gif";
							break;		
						case(VBank):  // ������ �Ա� ����
							echo "img/bank.gif";
							break;
						case(Culture):  // ��ȭ��ǰ�� ����
							echo "img/ticket.gif";
							break;
						default: // ��Ÿ ���Ҽ����� ���
							echo "img/card.gif";
							break;

					}
				}
					
    				?> style="padding:0 0 0 64">
    				
<!-------------------------------------------------------------------------------------------------------
 *													*
 *  �Ʒ� �κ��� ��� ���������� �������� ����޼��� ��� �κ��Դϴ�.					*
 *  													*
 *	1. $inipay->m_resultCode 	(�� �� �� ��) 							*
 *  	2. $inipay->m_resultMsg		(��� �޼���)							*
 *  	3. $inipay->m_payMethod		(�� �� �� ��)							*
 *  	4. $inipay->m_tid		(�� �� �� ȣ)							*
 *  	5. $inipay->m_moid  		(�� �� �� ȣ)							*
 -------------------------------------------------------------------------------------------------------->
 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="3%" valign="top"><img src="img/title_01.gif" width="8" height="27" vspace="5"></td>
          <td width="97%" height="40" class="pl_03"><font color="#FFFFFF"><b>�������</b></font></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td align="center" bgcolor="6095BC">
      <table width="620" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td bgcolor="#FFFFFF" style="padding:0 0 0 56">
		  <table width="510" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="7"><img src="img/life.gif" width="7" height="30"></td>
                <td background="img/center.gif"><img src="img/icon03.gif" width="12" height="10">
                
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_resultCode 										*	
                 *       ��. �� �� �� ��: "00" �� ��� ���� ����[�������Ա��� ��� - ������ �������Ա� ��û�� �Ϸ�]	*
                 *       ��. �� �� �� ��: "00"���� ���� ��� ���� ����  						*
                 --------------------------------------------------------------------------------------------------------> 
                  <b><?php if($inipay->m_resultCode == "00" && $inipay->m_payMethod == "VBank"){ echo "������ �������Ա� ��û�� �Ϸ�Ǿ����ϴ�.";}
                  	   else if($inipay->m_resultCode == "00"){ echo "������ ������û�� �����Ǿ����ϴ�.";}
                           else{ echo "������ ������û�� ���еǾ����ϴ�.";} ?> </b></td>
                <td width="8"><img src="img/right.gif" width="8" height="30"></td>
              </tr>
            </table>
            <br>
            <table width="510" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="407"  style="padding:0 0 0 9"><img src="img/icon.gif" width="10" height="11"> 
                  <strong><font color="433F37">��������</font></strong></td>
                <td width="103">&nbsp;</td>                
              </tr>
              <tr> 
                <td colspan="2"  style="padding:0 0 0 23">
		  <table width="470" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    
                <!-------------------------------------------------------------------------------------------------------
                 * 2. $inipay->m_payMethod 										*	
                 *       ��. ���� ����� ���� ��									*
                 *       	��. �ſ�ī�� 	- 	Card								*
                 *		��. ISP		-	VCard								*	
                 *		��. �������	-	DirectBank							*
                 *		��. �������Ա�	-	VBank								*
                 *		��. �ڵ���	- 	HPP								*
                 *		��. ��ȭ���� (ars��ȭ ����)	-	Ars1588Bill					*
                 *		��. ��ȭ���� (�޴���ȭ����)	-	PhoneBill					*
                 *		��. OK CASH BAG POINT		-	OCBPoint					*
                 *		��. ��ȭ��ǰ��	-	Culture								*
                 -------------------------------------------------------------------------------------------------------->
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">�� �� �� ��</td>
                      <td width="343"><?php echo($inipay->m_payMethod); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="26">�� �� �� ��</td>
                      <td width="343"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td><?php echo($inipay->m_resultCode); ?></td>
                            <td width='142' align='right'>
                          
                <!-------------------------------------------------------------------------------------------------------
                 * 2. $inipay->m_resultCode ���� ���� "������ ����" �Ǵ� "���� ���� �ڼ��� ����" ��ư ���		*
                 *       ��. ���� �ڵ��� ���� "00"�� ��쿡�� "������ ����" ��ư ���					*
                 *       ��. ���� �ڵ��� ���� "00" ���� ���� ��쿡�� "���� ���� �ڼ��� ����" ��ư ���			*
                 -------------------------------------------------------------------------------------------------------->
		<!-- ���а�� �� ���� ��ư ��� -->
                            	<?php
                            		if($inipay->m_resultCode == "00"){
                				echo "<a href='javascript:show_receipt();'><img src='img/button_02.gif' width='94' height='24' border='0'></a>";
                			}
                			else{
                            			echo "<a href='javascript:errhelp();'><img src='img/button_01.gif' width='142' height='24' border='0'></a>";
                            		}
                                                    	
                            	?>                    </td>
                          </tr>
                        </table></td>
                    </tr>
                
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_resultMsg 										*
                 *       ��. ��� ������ ���� �ش� ���нÿ��� "[�����ڵ�] ���� �޼���" ���·� ���� �ش�.                *
                 *		��> [9121]����Ȯ�ο���									*
                 -------------------------------------------------------------------------------------------------------->
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">�� �� �� ��</td>
                      <td width="343"><?php echo($inipay->m_resultMsg); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_tid											*
                 *       ��. �̴Ͻý��� �ο��� �ŷ� ��ȣ -��� �ŷ��� ������ �� �ִ� Ű�� �Ǵ� ��			*
                 -------------------------------------------------------------------------------------------------------->
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">�� �� �� ȣ</td>
                      <td width="343"><?php echo($inipay->m_tid); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    
                <!-------------------------------------------------------------------------------------------------------
                 * 1. $inipay->m_moid											*
                 *       ��. �������� �Ҵ��� �ֹ���ȣ 									*
                 -------------------------------------------------------------------------------------------------------->
                    <tr> 
                      <td width="18" align="center"><img src="img/icon02.gif" width="7" height="7"></td>
                      <td width="109" height="25">�� �� �� ȣ</td>
                      <td width="343"><?php echo($inipay->m_moid); ?></td>
                    </tr>
                    <tr> 
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>


<?php                    
                    

	/*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  1.  �ſ�ī�� , ISP ���� ��� ��� (OK CASH BAG POINT ���� ���� ���� )				*
	 -------------------------------------------------------------------------------------------------------*/

	if($inipay->m_payMethod == "Card" || $inipay->m_payMethod == "VCard" ){
		
		echo "		
				<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�ſ�ī���ȣ</td>
                    		  <td width='343'>".$inipay->m_cardNumber."****</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
				<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ¥</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ��</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>                	    
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�� �� �� ȣ</td>
                    		  <td width='343'>".$inipay->m_authCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�� �� �� ��</td>
                    		  <td width='343'>".$inipay->m_cardQuota."����&nbsp;<b><font color=red>".$interest."</font></b></td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>ī �� �� ��</td>
                    		  <td width='343'>".$inipay->m_cardCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>ī��߱޻�</td>
                    		  <td width='343'>".$inipay->m_cardIssuerCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3'>&nbsp;</td>
                    		</tr>
                    		<tr> 
                		  <td style='padding:0 0 0 9' colspan='3'><img src='img/icon.gif' width='10' height='11'> 
        	          	  <strong><font color='433F37'>OK CASHBAG ���� �� ��볻��</font></strong></td>
                		</tr>
                		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>ī �� �� ȣ</td>
                    		  <td width='343'>".$inipay->m_ocbcardnumber."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>���� ���ι�ȣ</td>
                    		  <td width='343'>".$inipay->m_ocbSaveAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>��� ���ι�ȣ</td>
                    		  <td width='343'>".$inipay->m_ocbUseAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�� �� �� ��</td>
                    		  <td width='343'>".$inipay->m_ocbAuthDate."</td>
                    		</tr>
                		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>����Ʈ���ұݾ�</td>
                    		  <td width='343'>".$inipay->m_price2."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
                    
          }
        
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  2.  ������°��� ��� ��� 										*
	 -------------------------------------------------------------------------------------------------------*/
	 
          else if($inipay->m_payMethod == "DirectBank"){
          	
          	echo"		
          			<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ¥</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ��</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>";
          }
          
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  3.  �������Ա� �Ա� ���� ��� ��� (���� ������ �ƴ� �Ա� ���� ���� ����)				*
	 -------------------------------------------------------------------------------------------------------*/
	 
          else if($inipay->m_payMethod == "VBank"){
          	
          	echo "		
          			<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�Աݰ��¹�ȣ</td>
                    		  <td width='343'>".$inipay->m_vacct."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�Ա� �����ڵ�</td>
                    		  <td width='343'>".$inipay->m_vcdbank."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>������ ��</td>
                    		  <td width='343'>".$inipay->m_nmvacct."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�۱��� ��</td>
                    		  <td width='343'>".$inipay->m_nminput."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�۱��� �ֹι�ȣ</td>
                    		  <td width='343'>".$inipay->m_perno."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>��ǰ �ֹ���ȣ</td>
                    		  <td width='343'>".$inipay->m_oid."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�۱� ����</td>
                    		  <td width='343'>".$inipay->m_dtinput."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
          }
          
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  4.  �ڵ��� ���� 											*
	 -------------------------------------------------------------------------------------------------------*/
	 
          else if($inipay->m_payMethod == "HPP"){
          	
          	echo "		
          			
          			<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�޴�����ȣ</td>
                    		  <td width='343'>".$inipay->m_nohpp."</td>
                    		</tr>
                    		<tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                    		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ¥</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ��</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
				<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
          }
          
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  4.  ��ȭ ���� 											*
	 -------------------------------------------------------------------------------------------------------*/
	 
         else if($inipay->m_payMethod == "Ars1588Bill" || $inipay->m_payMethod == "PhoneBill"){
                    	
                echo " 		
                		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�� ȭ �� ȣ</td>
                    		  <td width='343'>".$inipay->m_noars."</td>
                    		</tr>
                    		<tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ¥</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ��</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
                		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
         }
         
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  4.  OK CASH BAG POINT ���� �� ���� 									*
	 -------------------------------------------------------------------------------------------------------*/
	 
         else if($inipay->m_payMethod == "OCBPoint"){
         	
                echo"		
                		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>ī �� �� ȣ</td>
                    		  <td width='343'>".$inipay->m_ocbcardnumber."</td>
                    		</tr>
                    		<tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ¥</td>
                                  <td width='343'>".$inipay->m_pgAuthDate."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>
                                <tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>�� �� �� ��</td>
                                  <td width='343'>".$inipay->m_pgAuthTime."</td>
                                </tr>
                                <tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>���� ���ι�ȣ</td>
                    		  <td width='343'>".$inipay->m_ocbSaveAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>��� ���ι�ȣ</td>
                    		  <td width='343'>".$inipay->m_ocbUseAuthCode."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>�� �� �� ��</td>
                    		  <td width='343'>".$inipay->m_ocbAuthDate."</td>
                    		</tr>
                		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>
                    		<tr> 
                    		  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                    		  <td width='109' height='25'>����Ʈ���ұݾ�</td>
                    		  <td width='343'>".$inipay->m_price2."</td>
                    		</tr>
                    		<tr> 
                    		  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                    		</tr>";
         }
         
        /*-------------------------------------------------------------------------------------------------------
	 *													*
	 *  �Ʒ� �κ��� ���� ���ܺ� ��� �޼��� ��� �κ��Դϴ�.    						*	
	 *													*
	 *  4.  ��ȭ ��ǰ��						                			*
	 -------------------------------------------------------------------------------------------------------*/
	 
         else if($inipay->m_payMethod == "Culture"){
         	
                echo"		
                		<tr> 
                                  <td width='18' align='center'><img src='img/icon02.gif' width='7' height='7'></td>
                                  <td width='109' height='25'>���ķ��� ID</td>
                                  <td width='343'>".$inipay->m_cultureid."</td>
                                </tr>
                                <tr> 
                                  <td height='1' colspan='3' align='center'  background='img/line.gif'></td>
                                </tr>";
         }
                		
         
?>
                    
                  </table></td>
              </tr>
            </table>
            <br>
            
<!-------------------------------------------------------------------------------------------------------
 *													*
 *  ���� ������($inipay->m_resultCode == "00"�� ��� ) "�̿�ȳ�"  �����ֱ� �κ��Դϴ�.			*	    
 *  ���� ���ܺ��� �̿������ ���� ���ܿ� ���� ���� ������ ���� �ݴϴ�. 				*
 *  switch , case�� ���·� ���� ���ܺ��� ��� �ϰ� �ֽ��ϴ�.						*
 *  �Ʒ� ������ ��� �մϴ�.										*
 *													*
 *  1.	�ſ�ī�� 											*
 *  2.  ISP ���� 											*
 *  3.  �ڵ��� 												*
 *  4.  ��ȭ ���� (1588Bill)										*
 *  5.  ��ȭ ���� (PhoneBill)										*
 *  6.	OK CASH BAG POINT										*
 *  7.  ���������ü											*
 *  8.  ������ �Ա� ����										*
 *  9.  ��ȭ��ǰ�� ����											*	
 ------------------------------------------------------------------------------------------------------->
 
            <?php
            	
            	if($inipay->m_resultCode == "00"){
            		
            		switch($inipay->m_payMethod){
            		       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  1.  �ſ�ī�� 						                			*
	 			--------------------------------------------------------------------------------------------------------*/
	
				case(Card): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) �ſ�ī�� û������ <b>\"�̴Ͻý�(inicis.com)\"</b>���� ǥ��˴ϴ�.<br>
         					          (2) LGī�� �� BCī���� ��� <b>\"�̴Ͻý�(�̿� ������)\"</b>���� ǥ��ǰ�, �Ｚī���� ��� <b>\"�̴Ͻý�(�̿���� URL)\"</b>�� ǥ��˴ϴ�.</td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
				
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  2.  ISP 						                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(VCard): // ISP
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) �ſ�ī�� û������ <b>\"�̴Ͻý�(inicis.com)\"</b>���� ǥ��˴ϴ�.<br>
         					          (2) LGī�� �� BCī���� ��� <b>\"�̴Ͻý�(�̿� ������)\"</b>���� ǥ��ǰ�, �Ｚī���� ��� <b>\"�̴Ͻý�(�̿���� URL)\"</b>�� ǥ��˴ϴ�.</td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  3. �ڵ��� 						                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(HPP): // �޴���
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) �ڵ��� û������ <b>\"�Ҿװ���\"</b> �Ǵ� <b>\"�ܺ������̿��\"</b>�� û���˴ϴ�.<br>
         					          (2) ������ �� �ѵ��ݾ��� Ȯ���Ͻð��� �� ��� �� �̵���Ż��� �����͸� �̿����ֽʽÿ�.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;				
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  4. ��ȭ ���� (ARS1588Bill)				                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(Ars1588Bill): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) ��ȭ û������ <b>\"������ �̿��\"</b>�� û���˴ϴ�.<br>
                                                          (2) �� �ѵ��ݾ��� ��� ������ �������� ��� ��ϵ� ��ȭ��ȣ ������ �ƴ� �ֹε�Ϲ�ȣ�� �������� å���Ǿ� �ֽ��ϴ�.<br>
                                                          (3) ��ȭ ������Ҵ� ������� �����մϴ�.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  5. ���� ���� (PhoneBill)				                				*
	 			--------------------------------------------------------------------------------------------------------*/
				
				case(PhoneBill): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) ��ȭ û������ <b>\"���ͳ� ������ (����)�����̿��\"</b>�� û���˴ϴ�.<br>
                                                          (2) �� �ѵ��ݾ��� ��� ������ �������� ��� ��ϵ� ��ȭ��ȣ ������ �ƴ� �ֹε�Ϲ�ȣ�� �������� å���Ǿ� �ֽ��ϴ�.<br>
                                                          (3) ��ȭ ������Ҵ� ������� �����մϴ�.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
				
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  6. OK CASH BAG POINT					                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(OCBPoint): 
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) OK CASH BAG ����Ʈ ������Ҵ� ������� �����մϴ�.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  7. ���������ü					                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(DirectBank):  
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) ������ ���忡�� <b>\"�̴Ͻý�\"</b>�� ǥ��˴ϴ�.<br>
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  8. ������ �Ա� ����					                				*
	 			--------------------------------------------------------------------------------------------------------*/		
				case(VBank):  
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          (1) ��� ����� �Աݿ����� �Ϸ�� ���ϻ� ���� �ԱݿϷᰡ �̷���� ���� �ƴմϴ�.<br>
         					          (2) ��� �Աݰ��·� �ش� ��ǰ�ݾ��� �������Ա�(â���Ա�)�Ͻðų�, ���ͳ� ��ŷ ���� ���� �¶��� �۱��� �Ͻñ� �ٶ��ϴ�.<br>
                                                          (3) �ݵ�� �Աݱ��� ���� �Ա��Ͻñ� �ٶ��, ����Աݽ� �ݵ�� �ֹ��Ͻ� �ݾ׸� �Ա��Ͻñ� �ٶ��ϴ�.
                                                          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
					
			       /*--------------------------------------------------------------------------------------------------------
	 			*													*
	 			* ���� ������ �̿�ȳ� �����ֱ� 			    						*	
				*													*
	 			*  9. ��ȭ��ǰ�� ����					                				*
	 			--------------------------------------------------------------------------------------------------------*/
	 			
				case(Culture):  
					echo "<table width='510' border='0' cellspacing='0' cellpadding='0'>
         					<tr> 
         					    <td height='25'  style='padding:0 0 0 9'><img src='img/icon.gif' width='10' height='11'> 
         					      <strong><font color='433F37'>�̿�ȳ�</font></strong></td>
         					  </tr>
         					  <tr> 
         					    <td  style='padding:0 0 0 23'> 
         					      <table width='470' border='0' cellspacing='0' cellpadding='0'>
         					        <tr>          					          
         					          <td height='25'>(1) ��ȭ��ǰ���� �¶��ο��� �̿��Ͻ� ��� �������ο����� ����Ͻ� �� �����ϴ�.<br>
         					                          (2) ����ĳ�� �ܾ��� �����ִ� ���, ������ ����ĳ�� �ܾ��� �ٽ� ����Ͻ÷��� ���ķ��� ID�� ����Ͻñ� �ٶ��ϴ�.
         					          </td>
         					        </tr>
         					        <tr> 
         					          <td height='1' colspan='2' align='center'  background='img/line.gif'></td>
         					        </tr>
         					        
         					      </table></td>
         					  </tr>
         				      </table>";
					break;
			}
		}
		
	    ?>		
            
            <!-- �̿�ȳ� �� -->
            
          </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td><img src="img/bottom01.gif" width="632" height="13"></td>
  </tr>
</table>
</center></body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
