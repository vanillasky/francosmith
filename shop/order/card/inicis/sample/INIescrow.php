<?

/**********************************************************************
 *  FILE NAME : escrow.php                                            *
 *  AUTHOR : ts@inicis.com                                            *
 *  DATE : 2003/12 (Payment Team �븮 ������)                         *
 *                                                                    *
 * �ϳ����� ����ũ�ο� ��� �� ��ǰ ��û �� ��� ������               *
 **********************************************************************/


/*########################
            ���̺귯�� ȣ��
########################*/

include "../../../../lib/library.php";
require("EscrowLib.php");
extract($_POST);


/*########################
            �ν��Ͻ� ����
########################*/

$escrow = new Escrow;



/*########################################
                  ���/��ǰ ���� ���� ����
########################################*/

$inipayhome = substr(dirname(__FILE__),0,-7);
$escrow->inipayhome = $inipayhome; 	// �̴����� ���ҽý��� ��ġ ���� ���(�ݵ�� ���� ��η� �Է��Ͻñ� �ٶ��ϴ�.)
$escrow->mid = $mid;					// ���� ���̵�
$escrow->EscrowType=$EscrowType;          		// ����ũ�� Ÿ�� 
$escrow->hanatid = $hanatid;	         		// �ϳ����� �ŷ� ���̵�                                    
$escrow->invno = $invno;				// ����� ��ȣ
$escrow->adminID = $adminID;				// ����� ID
$escrow->adminName = $adminName;			// ����� ����
$escrow->regdate = date("Ymd");				// ��Ͽ�û ����	
$escrow->regtime = date("His");				// ��Ͽ�û �ð�




/*#################################
                 ��۰��� ���� ����
#################################*/


$escrow->compName = $compName;				// ���ȸ���
$escrow->compID = $compID;				// ���ȸ��
$escrow->transtype = $transtype;		        // �������, ������� �ϰ�� - ��� ���� (0 - ���, 1 - �ݼ�)		
$escrow->transport = $transport;			// ��ۼ���	
$escrow->transfee = $transfee;				// ��ۺ�
$escrow->paymeth = $paymeth;				// ��ۺ� ���޹��	
$escrow->notice = $notice;				// ��� ���� ����
$escrow->transdate1 = $transdate1;		        // ��ۿ�û���� (from)
$escrow->transdate2 = $transdate2;                      // ��ۿ�û���� (to)
$escrow->cnt = "1";		                        // ��ۿ�û �޼��� �ټ�(������� ��Ͻÿ��� �ʿ���)



/*#################################
                 ������� ���� ����
#################################*/



$escrow->transid = $transid;				// �ù�� ID
$escrow->customcode = $customcode;			// ���� �ڵ�
$escrow->orderno = $orderno;				// �ֹ���ȣ(����/����)
$escrow->settleno = $settleno;				// ���� ��ȣ
$escrow->pgid = "PINICIS001";				// PGID (����-���� ���� �Ұ�)
$escrow->sendareacode = $sendareacode;			// ����� �ڵ�
$escrow->mername = $mername;				// ���޻�� (���� �̸�)
$escrow->sendtel = $sendtel;				// ������ ��ȭ��ȣ
$escrow->sendzip = $sendzip;				// ������ �����ȣ
$escrow->sendaddr = $sendaddr;				// ������ �ּ�
$escrow->sendhpp = $sendhpp;				// ������������ڵ��� ��ȣ
$escrow->sendaddr1 = $sendaddr1;			// ������ �� �ּ�
$escrow->recvname = $recvname;				// ������ �̸� (�Ǵ� ������ ��)
$escrow->recvtel = $recvtel;				// ������ ��ȭ��ȣ
$escrow->recvzip = $recvzip;				// ������ �����ȣ
$escrow->recvaddr = $recvaddr;				// ������ �ּ�
$escrow->recvaddr1 = $recvaddr1;			// ������ ���ּ�
$escrow->recvhpp = $recvhpp;				// ������ �ڵ��� ��ȣ
$escrow->ordertype = $ordertype;			// ���� ���� (1 - �Ϲ�, 2 - ��ȯ, 3 - A S)
$escrow->feetype = $feetype;				// ���� ���� (1 - ����, 2 - ����, 3 - �ſ�, 4 - �����ſ�)
$escrow->boxtype = $boxtype;				// �ڽ� Ÿ�� (1 - ��, 2 - ��, 3 - ��)
$escrow->goodcode = $goodcode;				// ��ǥ ��ǰ�ڵ�
$escrow->goodname = $goodname;				// ��ǥ ��ǰ��
$escrow->qty = $qty;					// ����
$escrow->origininvoice = $origininvoice;			// �� ��� �����ȣ(�ݼ۽ÿ� ���)
$escrow->goodsort = $goodsort;				// �ѻ�ǰ ���� ��
$escrow->transmsg = $transmsg;				// ���� ����
$escrow->orderremark = $orderremark;			// �ֹ� �޸�
$escrow->orderdate = date("Ymdhms");			// �ֹ���¥�ð�



/*#################################
                 ��ǰ���� ���� ����
#################################*/

$escrow->returntype = $returntype;                      // ��ǰ ���� (��ǰ���� : 0, ��ǰ���� : 1)
$escrow->returncode = $returncode;                      // ��ǰ���ο���(���ۿϷ� : R0, �⼭�� ���� : R1, ��ǰ��ǰ �̼��� : R2, ��ǰ��ǰ�� ����ǰ�� �ٸ� : R3, �δ籸�� öȸ : R4, ��Ÿ : R5)
$escrow->reMsg = $reMsg;                                // ��ǰ�źλ����޼���


/*###################################
        �������/��ǰ���� �޼��� ����
#####################################

1.������� ��� �޼��� �����

����ID[$escrow->mid] + ������["\x0B"] +
��۵�� �Ǽ�[$escrow->cnt] + ������["\x0B"] +
��۵���� �޼���[$escrow->hanatid ~ $escrow->transdate2] �� �������� �̷������,
�ΰ� �̻��� ��������� ����� ��� �Ʒ��� ���� ������ �Ǹ�, ������ �޼����� "\x0B" �� ���е˴ϴ�.

��) ���� ���̵� hanatest00�̰� ���� ��� �޼����� 2���� ���
$escrow->sendMsg = hanatest00 "\x0B" 2 "\x0B" [$escrow->hanatid ~ $escrow->transdate2] "\x0B" [$escrow->hanatid ~ $escrow->transdate2]

��) ���� ���̵� hanatest00�̰� ���� ��� �޼����� 3���� ���
$escrow->sendMsg = hanatest00 "\x0B" 3 "\x0B" [$escrow->hanatid ~ $escrow->transdate2] "\x0B" [$escrow->hanatid ~ $escrow->transdate2] "\x0B" [$escrow->hanatid ~ $escrow->transdate2]

�� ���� �Ǹ� �ѹ��� ����Ҽ� �ִ� �޼����� ������ �ִ� 10���� ������ ������ �����Ͻñ� �ٶ��ϴ�. (������� ��Ͽ��� �ش�˴ϴ�.)




2. ������� ����� ��ǰ���� ���/������ ������� ��ϰ��� �ٸ��� �ϳ��� �������� �Ͻñ� �ٶ��ϴ�.

*/


if($escrow->EscrowType == "dr") // ������� ��� 
{

	$escrow->sendMsg = $escrow->mid . "\x0B" . 
				       $escrow->cnt . "\x0B" .    // (����) �޼����� �ټ��� �ִ� 10���� ���� �ʵ��� �մϴ�.
			               $escrow->hanatid . "|" . 
                      	               $escrow->invno . "|" . 
                             	       $escrow->adminID . "|" . 
		       	               $escrow->adminName . "|" . 
              	     	               $escrow->regdate . "|" .  
             	                       $escrow->regtime . "|" . 
             	                       $escrow->compName . "|" . 
             	                       $escrow->compID . "|" . 
             	                       $escrow->transtype . "|" . 
             	                       $escrow->transport . "|" . 
             	                       $escrow->transfee . "|" . 
             	                       $escrow->paymeth . "|" . 
             	                       $escrow->notice . "|" . 
             	                       $escrow->transdate1 . "|" . 
             	                       $escrow->transdate2;         // (����) 1���̻��� �޼����� �����Ҷ��� �ݵ�� "\x0B"(ctrl+k)�� �����ϵ��� �Ͻñ� �ٶ��ϴ�.

}
else if($escrow->EscrowType =="du") // ������� ����
{

                     $escrow->sendMsg = $escrow->mid . "\x0B" . 
				        $escrow->hanatid . "\x0B" . 
                               	   	$escrow->invno . "\x0B" . 
                                        $escrow->adminID . "\x0B" . 
		       	                $escrow->adminName . "\x0B" . 
              	              	        $escrow->regdate . "\x0B" .  
             	                        $escrow->regtime . "\x0B" . 
             	                        $escrow->compName . "\x0B" . 
             	                        $escrow->compID . "\x0B" . 
             	                        $escrow->transtype . "\x0B" . 
             	                        $escrow->transport . "\x0B" . 
             	                        $escrow->transfee . "\x0B" . 
             	                        $escrow->paymeth . "\x0B" . 
             	                        $escrow->notice . "\x0B" . 
             	                        $escrow->transdate1 . "\x0B" . 
             	                        $escrow->transdate2;
             	                     
}
else if($escrow->EscrowType == "dd") // �������
{
	$escrow->sendMsg = $escrow->mid . "\x0B" .
			   $escrow->hanatid . "\x0B" .
	                   $escrow->transtype . "\x0B" .
	                   $escrow->transid . "\x0B" .
			   $escrow->customcode . "\x0B" .
			   $escrow->orderno . "\x0B" .
			   $escrow->settleno . "\x0B" .
			   $escrow->pgid . "\x0B" .
			   $escrow->sendareacode . "\x0B" .
			   $escrow->mername . "\x0B" .
			   $escrow->sendtel . "\x0B" .
			   $escrow->sendzip . "\x0B" .
			   $escrow->sendaddr . "\x0B" .
			   $escrow->sendhpp . "\x0B" .
			   $escrow->sendaddr1 . "\x0B" .
			   $escrow->recvname . "\x0B" .
			   $escrow->recvtel . "\x0B" .
			   $escrow->recvzip . "\x0B" .
			   $escrow->recvaddr . "\x0B" .
			   $escrow->recvaddr1 . "\x0B" .
			   $escrow->recvhpp . "\x0B" .
			   $escrow->ordertype . "\x0B" .
			   $escrow->feetype . "\x0B" .
			   $escrow->boxtype . "\x0B" .
			   $escrow->goodcode . "\x0B" .
			   $escrow->goodname . "\x0B" .
			   $escrow->qty . "\x0B" .
			   $escrow->origininvoice . "\x0B" .
			   $escrow->goodsort . "\x0B" .
			   $escrow->transmsg . "\x0B" .
			   $escrow->orderremark . "\x0B" .
			   $escrow->orderdate;


 	
}
else if($escrow->EscrowType == "rr") // ��ǰ���� ���
{

	$escrow->sendMsg = $escrow->mid . "\x0B" . 
					$escrow->hanatid . "\x0B" .
					$escrow->adminID . "\x0B" . 
		       	                $escrow->adminName . "\x0B" . 
		     	                $escrow->regdate . "\x0B" .  
             	                        $escrow->regtime . "\x0B" . 
             	                        $escrow->returntype . "\x0B" . 
             	                        $escrow->returncode . "\x0B" . 
             	                        $escrow->reMsg;
             	                       
}
else if($escrow->EscrowType =="ru") // ��ǰ���� ����
{

	$escrow->sendMsg = $escrow->mid . "\x0B" . 
					$escrow->hanatid . "\x0B" .
					$escrow->adminID . "\x0B" . 
		       	                $escrow->adminName . "\x0B" . 
		     	                $escrow->regdate . "\x0B" .  
             	                        $escrow->regtime . "\x0B" . 
             	                        $escrow->returntype . "\x0B" . 
             	                        $escrow->returncode . "\x0B" . 
             	                        $escrow->reMsg;                 

}


/*##############################
        �������/��ǰ���� ��û
##############################*/
                 
$escrow->startAction();



/*############################################
                    �������/��ǰ���� ���
##############################################
# * ������� : $escrow->resultMsg            #
# * ����ڵ� : $escrow->resultCode           #
#              (0000 �̸� ����)              # 
#                                            #
############################################*/               

switch ($escrow->resultCode)
{
	case "0000":
		$db->query("update ".GD_ORDER." set escrowconfirm=1 where ordno='$_POST[ordno]'");
		break;
	default: break;
}

?>

<script>alert("<?=$escrow->resultMsg?>");</script>

<html>
<head>

<title>�ϳ����� �Ÿź�ȣ ���� ��� ����</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<style type="text/css">
	BODY{font-size:9pt; line-height:160%}
	TD{font-size:9pt; line-height:160%}
	INPUT{font-size:9pt;}
	.emp{background-color:#E0EFFE;}
</style>

</head>

<body>
<table border=0 width=500>
<tr>
<td>
<hr noshade size=1>
<b>��û ���</b>
<hr noshade size=1>
</td>
</tr>
</table>
<br>

<table border=0 width=500>
	<tr>
		<td align=right nowrap>����ũ�� ���� : </td>
		<td>
			<?php 
				if($escrow->EscrowType == "dr"){
					echo "��۵��";
				}else if($escrow->EscrowType == "du"){
					echo "��ۼ���";
				}else if($escrow->EscrowType == "dd"){
					echo "�������";
				}else if($escrow->EscrowType == "rr"){
					echo "��ǰ���";
				}else if($escrow->EscrowType == "ru"){
					echo "��ǰ����";
				}
		       ?>
			
		</td>
	</tr>
	<tr>
		<td align=right nowrap>������� : </td>
		<td><font class=emp><?php echo($escrow->resultMsg); ?></font></td>
	</tr>
	<tr>
		<td align=right nowrap>����ڵ� : </td>
		<td><font class=emp><?php echo($escrow->resultCode); ?></font></td>
	</tr>
	<tr>
		<td align=right nowrap>�ŷ���ȣ : </td>
		<td><?php echo($escrow->hanatid); ?></td>
	</tr>
	
	</tr>
	<tr>
		<td colspan=2><hr noshade size=1></td>
	</tr>
	<tr>
		<td align=right colspan=2>Copyright Inicis Co., Ltd.<br>www.inicis.com</td>
	</tr>
</table>
</body>
</html>
