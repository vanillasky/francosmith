<?php
 /***************************************************************************************************************
 * �ô�����Ʈ�κ��� ������� ��/��� ����Ÿ�� �޾Ƽ� �������� ó�� �� ��
 * �ô�����Ʈ�� �ٽ� ���䰪�� �����ϴ� �������Դϴ�.
 * ���� DBó�� �κ��� ��ü�� �°� �����Ͽ� �۾��Ͻñ� �ٶ��ϴ�.
***************************************************************************************************************/

include "../../../../lib/library.php";
include "../../../../conf/config.php";

/*********************************** �ô�����Ʈ�� ���� �Ѱ� �޴� ���� ���� *************************************/
$trcode     = trim( $_POST["trcode"] );					    //�ŷ��ڵ�
$service_id = trim( $_POST["service_id"] );					//�������̵�
$orderdt    = trim( $_POST["orderdt"] );				    //��������
$virno      = trim( $_POST["virno"] );				        //������¹�ȣ
$deal_won   = trim( $_POST["deal_won"] );					//�Աݾ�
$ordno		= trim( $_POST["ordno"] );                      //�ֹ���ȣ
$inputnm	= trim( $_POST["inputnm"] );					//�Ա��ڸ�
/*********************************** �ô�����Ʈ�� ���� �Ѱ� �޴� ���� �� *************************************/

/***************************************************************************************************************
 * �������� �ش� �ŷ��� ���� ó�� db ó�� ��....
 *
 * trcode = "1" �� �Ϲݰ������ �Ա��뺸����
 * trcode = "2" �� �Ϲݰ������ ����뺸����
 *
***************************************************************************************************************/

if ($trcode == '1') $trname = '�Ϲݰ������ �Ա��뺸����';
else if ($trcode == '2') $trname = '�Ϲݰ������ ����뺸����';
$tmp_log = array();
$tmp_log[] = '----------------------------------------';
$tmp_log[] = '�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��('.$trname.')';
$tmp_log[] = 'Ȯ�νð� : '.date('Y:m:d H:i:s');
$tmp_log[] = '�ŷ��ڵ� : '.$trcode;
$tmp_log[] = '�������̵� : '.$service_id;
$tmp_log[] = '�ֹ��Ͻ� : '.$orderdt;
$tmp_log[] = '������¹�ȣ : '.$virno;
$tmp_log[] = '�Աݾ� : '.$deal_won;
$tmp_log[] = '�Ա��ڸ� : '.$inputnm;
$tmp_log[] = '----------------------------------------';
$settlelog = implode( "\n", $tmp_log )."\n";

### item check stock
include "../../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1'){
	$cancel -> cancel_db_proc($ordno,$no_tid);
} else if ($trcode == '1') {

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	### �ǵ���Ÿ ����
	$db->query("
	update ".GD_ORDER." set cyn='y', cdt=now(),
		step		= '1',
		step2		= '',
		settlelog	= concat('".$settlelog."',settlelog)
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

	### ���� ���� ����
	$step = 1;

	### �ֹ��α� ����
	orderLog($ordno,$r_step2[$data['step2']]." > ".$r_step[$step]);

	### ��� ó��
	setStock($ordno);

	### �Ա�Ȯ�θ���
	sendMailCase($data['email'],1,$data);

	### �Ա�Ȯ��SMS
	$dataSms = $data;
	sendSmsCase('incash',$data['mobileOrder']);

	// ���̹� ���ϸ��� �ŷ� Ȯ�� API
	include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
	$naverNcash = new naverNcash();
	$naverNcash->deal_done($ordno);
}


/******************************************ó�� ��� ����******************************************************/
$rResMsg  = "";
$rSuccYn  = "y";// ���� : y ���� : n

//����ó�� ��� �ŷ��ڵ�|�������̵�|�ֹ��Ͻ�|������¹�ȣ|ó�����|
$rResMsg .= $trcode."|";
$rResMsg .= $service_id."|";
$rResMsg .= $orderdt."|";
$rResMsg .= $virno."|";
$rResMsg .= $rSuccYn."|";

echo $rResMsg;
/******************************************ó�� ��� ����******************************************************/
?>