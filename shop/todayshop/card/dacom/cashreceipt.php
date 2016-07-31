<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
}
include dirname(__FILE__).'/../../../conf/config.php';
include dirname(__FILE__).'/../../../conf/pg.dacom.php';

/********************************************************************************************************

 * ���ݿ����� �߱�/��� PHP �������� �� ����


 1, ���ݿ����� �߱�/��� ��û �Ķ����
 ============ ====================================================================
  �Ķ���͸�		����
 ============ ====================================================================
 mid			�����޿��� �߱��� �������̵�
 oid			�ֹ���ȣ
 paytype		�������� : SC0030(������ü), SC0040(�������Ա�), SC0100(�ܵ�)
 usertype		�뵵 : 1(�ҵ������), 2(����������)
 ssn			���ݿ����� �߱� ���� , �ֹε�Ϲ�ȣ �Ǵ� ����ڹ�ȣ �Ǵ� ��ȭ��ȣ��
 amount			�߱�(���)�ݾ�
 bussinessno	���ݿ����� �߱� ����ڹ�ȣ
 method			���� : auth(�߱�), cancel(���)
 ret_url	    defaul(����) : NONE
 hashdata		�ؽ�����Ÿ(���Ἲ�����ʵ�)  :   md5($mid.$oid.$mertkey)
 ============ ====================================================================

 2. ��� �Ķ����
 ============ ====================================================================
 �Ķ���͸�			����
 ============ ====================================================================
 mid				�����޿��� �߱��� �������̵�
 oid				�ֹ���ȣ
 paytype			�������� - SC0030(������ü), SC0040(�������Ա�), SC0100(�ܵ�)
 receiptnumber		���ݿ����� ���ι�ȣ
 respcode			����ڵ� ('0000' : ����,  �׿� : ����)
 respmsg			����޽���
 ============ ====================================================================
 ==> ��û ��� ����
   �� �� )  name|value^name|value^name|value^name|value
   ��) mid|tdacomts1^oid|20080306-1^paytype|SC0030^receiptnumber|null^respcode|0000^respmsg|����


 ����) 1. ���ݿ����� �߱� (�ܵ��� ����)
          ���������� �ܵ� �߱޸� ���� �մϴ�. (SC0100)

       2. ���ݿ����� ���
          �ʼ�: �ֹ���ȣ(oid), �ݾ�(amount), �������̵�(mid), paytype, hashdata, ret_url, method

******************************************************************************************************/

// ���� ��û URL
// ���񽺿� : http://pg.dacom.net/common/cashreceipt.jsp
// �׽�Ʈ�� : http://pg.dacom.net:7080/common/cashreceipt.jsp

$service_url = 'http://pg.dacom.net/common/cashreceipt.jsp';
$mid = $pg['id']; //�����޿��� �߱��� �������̵�
$mertkey = $pg['mertkey']; //�����޿��� �߱�(���������� > ������� > �������� ���� ���� mertkey Ȯ��)
$ret_url = 'NONE'; // defaul : NONE

if ($_POST['method'] == 'auth' && isset($_GET['crno']) === false)
{
	$method = 'auth';
	$paytype = 'SC0100';
	$ordno = $_POST['ordno'];
	$data = $db->fetch("select * from gd_order where ordno='".$ordno."' limit 1");
	$amount = $data['prn_settleprice'];
	$usertype = $_POST['usertype'];
	$ssn = $_POST['ssn'];
	$bussinessno = $cfg['compSerial'];

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

		## ��ǰ��
		list($icnt) = $db->fetch("select count(*) from gd_order_item where istep < 40 and ordno='{$ordno}'");
		list($goodsnm) = $db->fetch("select goodsnm from gd_order_item where istep < 40 and ordno='{$ordno}' order by sno");

		$cutLen = 30;
		if ($icnt > 1){
			$cntStr = ' �� '.($icnt-1).'��';
			$cutLen -= strlen($cntStr) + 2;
		}
		$goodsnm = strcut($goodsnm,$cutLen) . $cntStr;

		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $goodsnm;
		$indata['buyername'] = $data['nameOrder'];
		$indata['useopt'] = ($_POST['usertype'] == '1' ? '0' : '1');
		$indata['certno'] = $_POST['ssn'];
		$indata['amount'] = $amount;

		if ($set['receipt']['compType'] == '1'){ // �鼼/���̻����
			$indata['supply'] = $amount;
			$indata['surtax'] = 0;
		}
		else { // ���������
			$indata['supply'] = round($amount / 1.1);
			$indata['surtax'] = $amount - $indata['supply'];
		}

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else if ($crdata['method'] == 'auth')
{
	$method = 'auth';
	$paytype = 'SC0100';
	$ordno = $crdata['ordno'];
	$amount = $crdata['amount'];
	$usertype = ($crdata['useopt'] == '0' ? '1' : '2');
	$ssn = $crdata['certno'];
	$bussinessno = $cfg['compSerial'];
	$crno = $_GET['crno'];
}
else if ($crdata['method'] == 'cancel')
{
	$method = 'cancel';
	$paytype = 'SC0100';
	$ordno = $crdata['ordno'];
	$amount = $crdata['amount'];
	$bussinessno = $cfg['compSerial'];
}

$oid = $ordno; //�ֹ���ȣ (��ҽ� ���ŷ� �ֹ���ȣ)
$hashdata = md5($mid.$oid.$mertkey); // ����Ű

// �������� ��۰������������� ȣ���Ͽ� ������������
$str_url = $service_url.'?mid='.$mid.'&oid='.$oid.'&paytype='.$paytype.'&usertype='.$usertype.'&ssn='.$ssn.'&amount='.$amount.'&bussinessno='.$bussinessno.'&method='.$method.'&ret_url='.$ret_url.'&hashdata='.$hashdata;

/*
*	fsockopen ���
*	php 4.3 ���� �������� ��밡��
*/
$res = readurl($str_url);
if(!$res)
{
	msg('���ݿ����� �������!!');
}
else
{
	/*<!--***************************************************
	  #��û ��� ���� ����
	   �� �� )  name|value^name|value^name|value^name|value
	   ��) mid|tdacomts1^oid|20080306-1^paytype|SC0030^receiptnumber|null^respcode|0000^respmsg|����
	****************]*************************************-->*/
	$tmp = explode('^',trim($res));
	foreach($tmp as $v){
		unset($rtmp);
		$rtmp = explode('|',$v);
		if( $rtmp[0] )$res_arr[$rtmp[0]] = $rtmp[1];
	}

	if($method == 'auth')
	{
		if($res_arr['respcode'] == '0000')
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '���ݿ����� �߱� ����'."\n";
			$settlelog .= '����ڵ� : '.$res_arr['respcode']."\n";
			$settlelog .= '������� : '.$res_arr['respmsg']."\n";
			$settlelog .= '���ι�ȣ : '.$res_arr['receiptnumber']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			if (empty($crno) === true)
			{
				$db->query("update gd_order set cashreceipt='{$res_arr['receiptnumber']}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# ���ݿ�������û���� ����
				$db->query("update gd_cashreceipt set pg='dacom',cashreceipt='{$res_arr['receiptnumber']}',receiptnumber='{$res_arr['receiptnumber']}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
				$db->query("update gd_order set cashreceipt='{$res_arr['receiptnumber']}' where ordno='{$ordno}'");
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
		else {
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '���ݿ����� �߱� ����'."\n";
			$settlelog .= '����ڵ� : '.$res_arr['respcode']."\n";
			$settlelog .= '������� : '.$res_arr['respmsg']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			if (empty($crno) === true)
			{
				$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# ���ݿ�������û���� ����
				$db->query("update gd_cashreceipt set pg='dacom',errmsg='{$res_arr['respcode']}:{$res_arr['respmsg']}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg($res_arr['respmsg']);
				exit;
			}
			else {
				echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
			}
		}
	}
	else if ($method == 'cancel')
	{
		if($res_arr['respcode'] == '0000')
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '���ݿ����� ��� ����'."\n";
			$settlelog .= '����ڵ� : '.$res_arr['respcode']."\n";
			$settlelog .= '������� : '.$res_arr['respmsg']."\n";
			$settlelog .= '���ι�ȣ : '.$res_arr['receiptnumber']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
		else {
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '���ݿ����� ��� ����'."\n";
			$settlelog .= '����ڵ� : '.$res_arr['respcode']."\n";
			$settlelog .= '������� : '.$res_arr['respmsg']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			$db->query("update gd_cashreceipt set errmsg='{$res_arr['respcode']}:{$res_arr['respmsg']}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
}

?>