<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';

	$ordno = $_POST['Order_no'];

	### �ݾ� ����Ÿ ��ȿ�� üũ
	$data = $db->fetch("select * from gd_order where ordno='$ordno'",1);
	if ($set['receipt']['compType'] == '1'){ // �鼼/���̻����
		$data['supply'] = $data['prn_settleprice'];
		$data['vat'] = 0;
	}
	else { // ���������
		$data['supply'] = round($data['prn_settleprice'] / 1.1);
		$data['vat'] = $data['prn_settleprice'] - $data['supply'];
	}
	if ($data['supply']!=$_POST['deal_won'] || $data['vat']!=$_POST['Amttex']) msg('�ݾ��� ��ġ���� �ʽ��ϴ�',-1);
}
else {
	$ordno = $crdata['ordno'];
}
//include dirname(__FILE__).'/../../../conf/pg.agspay.php';
// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

/****************************************************************************
*
* $IsDebug : 1:����,���� �޼��� Print 0:������
* $LOCALADDR : PG������ ����� ����ϴ� ��ȣȭProcess�� ��ġ�� �ִ� IP (220.85.12.74)
* $LOCALPORT : ��Ʈ
* $ENCRYPT : "C" ���ݿ�����
* $CONN_TIMEOUT : ��ȣȭ ����� ���� ConnectŸ�Ӿƿ� �ð�(��)
* $READ_TIMEOUT : ������ ���� Ÿ�Ӿƿ� �ð�(��)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.74";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;

/****************************************************************************
*
* AGSCash.html �� ���� �Ѱܹ��� ����Ÿ
*
****************************************************************************/

$Retailer_id = trim($pg['id']); //�������̵�
$Cat_id = '7005037001'; //�ܸ����ȣ(�ܸ��� ��ȣ�� 7005037001 ������ (�����Ұ�))
$Ord_No = trim($ordno); //�ֹ���ȣ

if ($_POST['Pay_kind'] == 'cash-appr' && isset($_GET['crno']) === false)
{
	$Pay_kind = 'cash-appr'; //��������
	$Pay_type = trim($_POST['Pay_type']); //������� 1.�������ӱ�, 2.������ü
	$Cust_no = trim($_POST['Cust_no']); //ȸ�����̵�
	$Amtcash = trim($_POST['Amtcash']); //�ŷ��ݾ�
	$deal_won = trim($_POST['deal_won']); //���ް���
	$Amttex = trim($_POST['Amttex']); //�ΰ���ġ��
	$Amtadd = '0'; //�����
	$prod_nm = trim($_POST['prod_nm']); //��ǰ��
	$prod_set = ''; //��ǰ����
	$Gubun_cd = trim($_POST['Gubun_cd']); //�ŷ��ڱ���
	$Confirm_no = trim($_POST['Confirm_no']); //�ź�Ȯ�ι�ȣ

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	@include dirname(__FILE__).'/../../../lib/cashreceipt.class.php';
	if (class_exists('cashreceipt'))
	{
		// �߱޻���üũ
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
			exit;
		}

		$indata = array();
		$indata['ordno'] = $ordno;
		$indata['goodsnm'] = $prod_nm;
		$indata['buyername'] = $Cust_no;
		$indata['useopt'] = ($Gubun_cd == '01' ? '0' : '1');
		$indata['certno'] = $Confirm_no;
		$indata['amount'] = $Amtcash;
		$indata['supply'] = $deal_won;
		$indata['surtax'] = $Amttex;

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else if ($crdata['Pay_kind'] == 'cash-appr')
{
	$Pay_kind = 'cash-appr'; //��������
	$Pay_type = '1'; //������� 1.�������ӱ�, 2.������ü
	$Cust_no = trim($crdata['buyername']); //ȸ�����̵�
	$Amtcash = trim($crdata['amount']); //�ŷ��ݾ�
	$deal_won = trim($crdata['supply']); //���ް���
	$Amttex = trim($crdata['surtax']); //�ΰ���ġ��
	$Amtadd = '0'; //�����
	$prod_nm = trim($crdata['goodsnm']); //��ǰ��
	$prod_set = ''; //��ǰ����
	$Gubun_cd = ($crdata['useopt'] == '0' ? '01' : '02'); //�ŷ��ڱ���
	$Confirm_no = trim($crdata['certno']); //�ź�Ȯ�ι�ȣ
	$crno = $_GET['crno'];
}
else if ($crdata['Pay_kind'] == 'cash-cncl')
{
	$Pay_kind = 'cash-cncl'; //��������
	$Pay_type = '1'; //������� 1.�������ӱ�, 2.������ü
	$Cust_no = trim($crdata['buyername']); //ȸ�����̵�
	$Amtcash = trim($crdata['amount']); //�ŷ��ݾ�
	$Amttex = trim($crdata['surtax']); //�ΰ���ġ��
	$Amtadd = '0'; //�����
	$Gubun_cd = ($crdata['useopt'] == '0' ? '01' : '02'); //�ŷ��ڱ���
	$Confirm_no = trim($crdata['certno']); //�ź�Ȯ�ι�ȣ
	$Org_adm_no = trim($crdata['receiptnumber']); //��ҽ� ���ι�ȣ
}

/*******************************************************************************************
*
* Pay_kind = cash-appr" ���ݿ����� ���ο�û��
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-appr" ) == 0 )
{

	/**************************************************************
	* ���ο�û��
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* ���� ���� Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Email."|".
		$prod_nm."|";

	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sSendMsg."<br>";
	}

	/****************************************************************************
	*
	* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
	*
	****************************************************************************/

	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );

	if( !$fp )
	{
		/** ���� ���з� ���� ���ν��� �޼��� ���� **/

		$Success = "n";
		$rResMsg = "���� ���з� ���� ����";
	}
	else
	{
		/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/

		$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";

		/** ���� ������ ��ȣȭProcess�� ���� **/

		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/

		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

		/****************************************************************************
		* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
		* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
		* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/

		/** ���� close **/

		fclose( $fp );
	}

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sRecvMsg."<br>";
	}

	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** ���� ������(����) üũ ���� **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rAdm_no = $RecvValArray[2];
		$rSuccess = $RecvValArray[3];
		$rResMsg = $RecvValArray[4];
		$rAlert_msg1 = $RecvValArray[5];
		$rAlert_msg2 = $RecvValArray[6];

	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/

		$Success = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";

	}

	/****************************************************************************
	*
	* ���� ��� ����
	*
	****************************************************************************/
	if( !strcmp($rSuccess,"y") && strcmp($Success,"n") ) // rSuccess "y" �϶��� ����
	{
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� �߱� ����'."\n";
		$settlelog .= '����ڵ� : '.$rSuccess."\n";
		$settlelog .= '������� : '.$rResMsg."\n";
		$settlelog .= '��üID   : '.$rRetailer_id."\n";
		$settlelog .= '�ֹ���ȣ : '.$rDealno."\n";
		$settlelog .= '���ι�ȣ : '.$rAdm_no."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		if (empty($crno) === true)
		{
			$db->query("update gd_order set cashreceipt='{$rAdm_no}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# ���ݿ�������û���� ����
			$db->query("update gd_cashreceipt set pg='agspay',cashreceipt='{$rAdm_no}',receiptnumber='{$rAdm_no}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='{$rAdm_no}' where ordno='{$ordno}'");
		}

		if (isset($_GET['crno']) === false)
		{
			msg('���ݿ������� ����߱޵Ǿ����ϴ�');
			echo '<script>parent.location.reload();</script>';
		}
		else {
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
	else { // rSuccess �� "y" �ƴҶ��� ����, rResMsg �� ���п� ���� �޼���
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� �߱� ����'."\n";
		$settlelog .= '����ڵ� : '.$rSuccess."\n";
		$settlelog .= '������� : '.$rResMsg."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		if (empty($crno) === true)
		{
			$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# ���ݿ�������û���� ����
			$db->query("update gd_cashreceipt set pg='agspay',errmsg='{$rSuccess}:{$rResMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		}

		if (isset($_GET['crno']) === false)
		{
			msg($rResMsg);
			exit;
		}
		else {
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
}

/*******************************************************************************************
*
* Pay_kind = "cash-cncl" ���ݿ����� ��ҿ�û��
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-cncl" ) == 0 )
{
	/**************************************************************
	* ��ҿ�û��
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* ���� ���� Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Org_adm_no."|".
		$Email."|".
		$prod_nm."|";


	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sSendMsg."<br>";
	}

	/****************************************************************************
	*
	* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
	*
	****************************************************************************/

	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );

	if( !$fp )
	{
		/** ���� ���з� ���� ���ν��� �޼��� ���� **/

		$Success = "n";
		$rResMsg = "���� ���з� ���� ����";
	}
	else
	{
		/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/

		$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";

		/** ���� ������ ��ȣȭProcess�� ���� **/

		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/

		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

		/****************************************************************************
		* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
		* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
		* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/

		/** ���� close **/

		fclose( $fp );
	}

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sRecvMsg."<br>";
	}

	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** ���� ������(����) üũ ���� **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rAdm_no = $RecvValArray[2];
		$rSuccess = $RecvValArray[3];
		$rResMsg = $RecvValArray[4];
		$rAlert_msg1 = $RecvValArray[5];
		$rAlert_msg2 = $RecvValArray[6];

	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/

		$Success = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";

	}

	/****************************************************************************
	*
	* ���� ��� ����
	*
	****************************************************************************/
	if( !strcmp($rSuccess,"y") && strcmp($Success,"n") ) // rSuccess "y" �϶��� ����
	{
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� ��� ����'."\n";
		$settlelog .= '����ڵ� : '.$rSuccess."\n";
		$settlelog .= '������� : '.$rResMsg."\n";
		$settlelog .= '��üID   : '.$rRetailer_id."\n";
		$settlelog .= '�ֹ���ȣ : '.$rDealno."\n";
		$settlelog .= '���ι�ȣ : '.$rAdm_no."(".$Org_adm_no.")\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
	else { // rSuccess �� "y" �ƴҶ��� ����, rResMsg �� ���п� ���� �޼���
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '���ݿ����� ��� ����'."\n";
		$settlelog .= '����ڵ� : '.$rSuccess."\n";
		$settlelog .= '������� : '.$rResMsg."\n";
		$settlelog .= '-----------------------------------'."\n";
		echo nl2br($settlelog);

		$db->query("update gd_cashreceipt set errmsg='{$rSuccess}:{$rResMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}

/*******************************************************************************************
*
* Pay_kind = cash-appr-temp" ���ݿ����� �ӽý��������û��
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-appr-temp" ) == 0 )
{

	/**************************************************************
	* ���ο�û��
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* ���� ���� Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Email."|".
		$prod_nm."|";


	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sSendMsg."<br>";
	}

	/****************************************************************************
	*
	* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
	*
	****************************************************************************/

	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );

	if( !$fp )
	{
		/** ���� ���з� ���� ���ν��� �޼��� ���� **/

		$Success = "n";
		$rResMsg = "���� ���з� ���� ����";
	}
	else
	{
		/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/

		$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";

		/** ���� ������ ��ȣȭProcess�� ���� **/

		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/

		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

		/****************************************************************************
		* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
		* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
		* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/

		/** ���� close **/

		fclose( $fp );
	}

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sRecvMsg."<br>";
	}

	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** ���� ������(����) üũ ���� **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rSuccess = $RecvValArray[2];
		$rResMsg = $RecvValArray[3];

	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/

		$Success = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";

	}
}

/*******************************************************************************************
*
* Pay_kind = "cash-cncl-temp" ���ݿ����� ��ҿ�û��
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-cncl-temp" ) == 0 )
{
	/**************************************************************
	* ��ҿ�û��
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* ���� ���� Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Org_adm_no."|".
		$Email."|".
		$prod_nm."|";


	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sSendMsg."<br>";
	}

	/****************************************************************************
	*
	* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
	*
	****************************************************************************/

	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );

	if( !$fp )
	{
		/** ���� ���з� ���� ���ν��� �޼��� ���� **/

		$Success = "n";
		$rResMsg = "���� ���з� ���� ����";
	}
	else
	{
		/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/

		$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";

		/** ���� ������ ��ȣȭProcess�� ���� **/

		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/

		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

		/****************************************************************************
		* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
		* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
		* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/

		/** ���� close **/

		fclose( $fp );
	}

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sRecvMsg."<br>";
	}

	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** ���� ������(����) üũ ���� **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rSuccess = $RecvValArray[2];
		$rResMsg = $RecvValArray[3];

	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/

		$Success = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";

	}
}
?>