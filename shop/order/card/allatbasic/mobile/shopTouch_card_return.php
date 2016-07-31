<?php

include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
include "../../../../conf/pg_mobile.allatbasic.php";
include "./allatutil.php";

$ordno = $_POST['allat_order_no'];

$query = "select settleprice from gd_order where ordno='".$ordno."';";
list($settleprice) = $db->fetch($query);

//Request Value Define
//----------------------
/********************* Service Code *********************/
$at_cross_key =  $pg_mobile['crosskey']; //�����ʿ�
$at_shop_id = $pg_mobile['id']; //�����ʿ�
$at_amt=$settleprice; //���� �ݾ��� �ٽ� ����ؼ� ������ ��(��ŷ����)
/*********************************************************/

// ��û ������ ����
//----------------------

$at_data   = "allat_shop_id=".$at_shop_id.
		   "&allat_amt=".$at_amt.
		   "&allat_enc_data=".$_POST["allat_enc_data"].
		   "&allat_cross_key=".$at_cross_key;


// �þ� ���� ������ ��� : ApprovalReq->����Լ�, $at_txt->�����
//----------------------------------------------------------------
$at_txt = ApprovalReq($at_data,"SSL");
// �� �κп��� �α׸� ����� ���� �����ϴ�.
// (�þ� ���� ������ ��� �Ŀ� �α׸� �����, ��ſ����� ���� �����ľ��� �����մϴ�.)

// ���� ��� �� Ȯ��
//------------------
$REPLYCD			= getValue("reply_cd",$at_txt);        //����ڵ�
$REPLYMSG			= getValue("reply_msg",$at_txt);       //��� �޼���

$ORDER_NO			=getValue("order_no",$at_txt);
$AMT				=getValue("amt",$at_txt);
$PAY_TYPE			=getValue("pay_type",$at_txt);
$APPROVAL_YMDHMS	=getValue("approval_ymdhms",$at_txt);
$SEQ_NO				=getValue("seq_no",$at_txt);
$APPROVAL_NO		=getValue("approval_no",$at_txt);
$CARD_ID			=getValue("card_id",$at_txt);
$CARD_NM			=getValue("card_nm",$at_txt);
$SELL_MM			=getValue("sell_mm",$at_txt);
$ZEROFEE_YN			=getValue("zerofee_yn",$at_txt);
$CERT_YN			=getValue("cert_yn",$at_txt);
$CONTRACT_YN		=getValue("contract_yn",$at_txt);
$SAVE_AMT			=getValue("save_amt",$at_txt);
$BANK_ID			=getValue("bank_id",$at_txt);
$BANK_NM			=getValue("bank_nm",$at_txt);
$CASH_BILL_NO		=getValue("cash_bill_no",$at_txt);
$ESCROW_YN			=getValue("escrow_yn",$at_txt);
$ACCOUNT_NO			=getValue("account_no",$at_txt);
$ACCOUNT_NM			=getValue("account_nm",$at_txt);
$INCOME_ACC_NM		=getValue("income_account_nm",$at_txt);
$INCOME_LIMIT_YMD	=getValue("income_limit_ymd",$at_txt);
$INCOME_EXPECT_YMD	=getValue("income_expect_ymd",$at_txt);
$CASH_YN			=getValue("cash_yn",$at_txt);
$HP_ID				=getValue("hp_id",$at_txt);
$TICKET_ID			=getValue("ticket_id",$at_txt);
$TICKET_PAY_TYPE	=getValue("ticket_pay_type",$at_txt);
$TICKET_NAME		=getValue("ticket_nm",$at_txt);

switch ($PAY_TYPE){
	case "3D": case "ISP": case "NOR":
		$settlelogAdd = "����ī�� : [$CARD_ID] $CARD_NM
�Һΰ��� : $SELL_MM
������   : $ZEROFEE_YN
";
		break;
	case "ABANK":
		$settlelogAdd = "�������� : [$BANK_ID] $BANK_NM
���ݿ������Ϸù�ȣ : $CASH_BILL_NO
";
		break;
	case "VBANK":
		$settlelogAdd = "������� : $BANK_NM $ACCOUNT_NO $ACCOUNT_NM
�Աݰ��¸� : $INCOME_ACC_NM
�Աݱ����� : $INCOME_LIMIT_YMD
�Աݿ����� : $INCOME_EXPECT_YMD
���ݿ�������û���� : $CASH_YN
���ݿ������Ϸù�ȣ : $CASH_BILL_NO
";
		break;
	case "HP":
		$settlelogAdd = "�̵���Ż籸�� : $HP_ID
";
		break;
}

$settlelog = "All@Pay Mobile ������û�� ���� ���
$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
����ڵ� : $REPLYCD
������� : $REPLYMSG
���αݾ� : $AMT
���Ҽ��� : $PAY_TYPE
�����Ͻ� : $APPROVAL_YMDHMS
�ŷ���ȣ : $SEQ_NO
���ι�ȣ : $APPROVAL_NO
�������� : $CERT_YN
----------------------------------------
";

if ($settlelogAdd) $settlelog .= $settlelogAdd."----------------------------------------\n";

### �ŷ��Ϸù�ȣ ����
$query = "update ".GD_ORDER." set cardtno='".$SEQ_NO."' where ordno='".$ordno."'";
$db -> query($query);

### ������� ������ ��� üũ �ܰ� ����
$res_cstock = true;
if($cfg['stepStock'] == '1' && $PAY_TYPE=="VBANK") $res_cstock = false;

### item check stock
include "../../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock && (!strcmp($REPLYCD,"0000") || !strcmp($REPLYCD,"0001"))){
	if($cancel->cancel_allat_mobile_request($ordno))
	{
		$REPLYCD = "OUT OF STOCK";
	}
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != ''){		// �ߺ�����

	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$CARD_NM","parent");

} else if( !strcmp($REPLYCD,"0000") || !strcmp($REPLYCD,"0001") ){		// ���� ����
	// ����� ó��
	//--------------------------------------------------------------------------
	// ��� ���� '0000'�̸� ������. ��, allat_test_yn=Y �ϰ�� '0001'�� ������.
	// ���� ����   : allat_test_yn=N �� ��� reply_cd=0000 �̸� ����
	// �׽�Ʈ ���� : allat_test_yn=Y �� ��� reply_cd=0001 �̸� ����
	//--------------------------------------------------------------------------
	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	include "../../../../lib/cart.class.php";

	$cart = new Cart($_COOKIE[gd_isDirect]);
	$cart->chkCoupon();

	$cart->delivery = $data[delivery];
	$cart->dc = $member[dc]."%";
	$cart->calcu();
	$cart -> totalprice += $delivery[price];

	### �ֹ�Ȯ�θ���
	$data[cart] = $cart;
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	sendMailCase($data[email],0,$data);

	### ����ũ�� ���� Ȯ��
	$escrowyn = ($ESCROW_YN=="Y") ? "y" : "n";

	### ���� ���� ����
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### ������� ������ �������� ����
	if ($PAY_TYPE=="VBANK"){
		$vAccount = $BANK_NM." ".$ACCOUNT_NO." ".$ACCOUNT_NM;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### ���ݿ����� ����
	if ($CASH_BILL_NO != ''){
		$qrc1 .= "cashreceipt='{$CASH_BILL_NO}',";
	}

	### �ǵ���Ÿ ����
	$db->query("
	update ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog')
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	### �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### ��� ó��
	setStock($ordno);

	### ��ǰ���Խ� ������ ��� _ 2007-06-04
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	### SMS ���� ����
	$dataSms = $data;

	if ($PAY_TYPE!="VBANK"){
		sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
		sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
	}

	go("/shopTouch/shopTouch_ord/order_end.php?ordno=$ordno&card_nm=$CARD_NM","parent");

} else {	// ���� ����

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
	go("/shopTouch/shopTouch_ord/order_fail.php?ordno=$ordno","parent");

}

/*
    [�ſ�ī�� ��ǥ��� ����]

    ������ ���������� �Ϸ�Ǹ� �Ʒ��� �ҽ��� �̿��Ͽ�, ������ �ſ�ī�� ��ǥ�� ������ �� �ֽ��ϴ�.
    ��ǥ ��½� �������̵�� �ֹ���ȣ�� �����Ͻñ� �ٶ��ϴ�.

    var urls ="http://www.allatpay.com/servlet/AllatBizPop/member/pop_card_receipt.jsp?shop_id=�������̵�&order_no=�ֹ���ȣ";
    window.open(urls,"app","width=410,height=650,scrollbars=0");

    ���ݿ����� ��ǥ �Ǵ� �ŷ�Ȯ�μ� ��¿� ���� ���Ǵ� �þ����� ����Ʈ�� 1:1����� �̿��Ͻðų�
    02) 3788-9990 ���� ��ȭ �ֽñ� �ٶ��ϴ�.

    ��ǥ��� �������� ���� �þ� Ȩ�������� �Ϻην�, Ȩ������ ���� ���� ������ ���Ͽ� ������ ���� �Ǵ� URL ������ ���� ��
    �ֽ��ϴ�. Ȩ������ ���� ���� ������ ���� ���, ��ǥ��� URL�� Ȯ���Ͻñ� �ٶ��ϴ�.
*/
?>
