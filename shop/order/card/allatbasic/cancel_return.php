<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.allatbasic.php";
include "../../../lib/cardCancel.class.php";

// �þܰ��� �Լ� Include
//----------------------
include "./allatutil.php";

$ordno = $_POST['allat_order_no'];
$a_sno = $_POST['sno'];
$a_price = $_POST['price'];
$a_repay = $_POST['repay'];

// ��û ������ ����
//----------------------
$at_data	= "allat_shop_id=".urlencode($pg['id'])."&allat_enc_data=".$_POST['allat_enc_data']."&allat_cross_key=".$pg['crosskey'];

// �þ� ���� ������ ��� : CancelReq->����Լ�, $at_txt->�����
//----------------------------------------------------------------
$at_txt = CancelReq($at_data,$pg[ssl]);

// ���� ��� �� Ȯ��
//------------------
$REPLYCD   =getValue("reply_cd",$at_txt);
$REPLYMSG  =getValue("reply_msg",$at_txt);

// ��� ���� '0000'�̸� ������. ��, allat_test_yn=Y �ϰ�� '0001'�� ������.
// ���� ���   : allat_test_yn=N �� ��� reply_cd=0000 �̸� ����
// �׽�Ʈ ��� : allat_test_yn=Y �� ��� reply_cd=0001 �̸� ����
//----------------------------------------------------------------------------------------
if( !strcmp($REPLYCD,"0000") ){
	// reply_cd "0000" �϶��� ����
	$CANCEL_YMDHMS=getValue("cancel_ymdhms",$at_txt);
	$PART_CANCEL_FLAG=getValue("part_cancel_flag",$at_txt);
	$REMAIN_AMT=getValue("remain_amt",$at_txt);
	$PAY_TYPE=getValue("pay_type",$at_txt);

	$log .= "����ڵ�    : ".$REPLYCD."\n";
	$log .= "����޼���  : ".$REPLYMSG."\n";
	$log .= "��ҳ�¥    : ".$CANCEL_YMDHMS."\n";
	$log .= "��ұ���    : ".$PART_CANCEL_FLAG."\n"; //�ſ�ī�� : ���(0),�κ����(1),  ������ü: ���(0), ȯ��(2),�κ�ȯ��(3)
	$log .= "�ܾ�        : ".$REMAIN_AMT."\n";
	$log .= "�ŷ���ı���: ".$PAY_TYPE."\n";

	$cancel = new cardCancel();
	if($_POST['actmode'] == 1){
		if($PART_CANCEL_FLAG == 1){
			$cancel -> partcancel_allatbasic_return($ordno,$a_sno,$log,$a_price, $a_repay);
			msg('�������� �κ���ҿϷ�');
			echo("<script>parent.location.reload();</script>");
		}else{
			$cancel -> cancel_proc($ordno,$log);
			msg('����������ҿϷ�');
			echo("<script>parent.location.reload();</script>");
		}
	}else{
		$cancel -> cancel_db_proc($ordno);
		go("../../order_fail.php?ordno=$ordno","parent");
	}

}else{
	$log .= "����ڵ�    : ".$REPLYCD."\n";
	$log .= "����޼���  : ".$REPLYMSG."\n";
	msg('�ŷ���� ���� �����ڿ��� ���� �Ͻʽÿ�!');
	echo '<div style="font:9pt verdana;">'.nl2br($log).'</div>';
}
?>