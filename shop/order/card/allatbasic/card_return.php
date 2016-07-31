<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.allatbasic.php";


// �þܰ��� �Լ� Include
//----------------------
include "./allatutil.php";

// PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_POST['allat_order_no'],$_POST['allat_amt']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['allat_order_no'],'parent');
	exit();
}

// Ncash ���� ���� API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($_POST['allat_vbank_yn']=="Y") $ncashResult = $naverNcash->payment_approval($_POST['allat_order_no'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['allat_order_no'], true);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.','../../order_fail.php?ordno='.$_POST['allat_order_no'],'parent');
		exit();
	}
}

function return_allat($str){
	$tmp = explode("\n",trim($str));
	for($i=0;$i<sizeof($tmp);$i++){
		$div = explode("=",trim($tmp[$i]));
		$arr[$div[0]] = $div[1];
	}
	return $arr;
}

function allat_log_write($logMsg)
{
	$logInfo  = 'INFO ['.date('Y-m-d H:i:s').'] START Order log'.chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Connect IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Request URL : '.$_SERVER['REQUEST_URI'].chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] User Agent : '.$_SERVER['HTTP_USER_AGENT'].chr(10);
	$logInfo .= $logMsg;
	$logInfo .= 'INFO ['.date('Y-m-d H:i:s').'] END Order log'.chr(10);
	$logInfo .= '------------------------------------------------------------------------------'.chr(10).chr(10);

	error_log($logInfo, 3, './log/allat_log_'.date('Ymd').'.log');
}

$ordno = $_POST['allat_order_no'];

//Request Value Define
//----------------------
/********************* Service Code *********************/
$at_cross_key = $pg['crosskey'];     //�����ʿ� [����Ʈ ���� - http://www.allatpay.com/servlet/AllatBiz/support/sp_install_guide_scriptapi.jsp#shop]
$at_shop_id   = urlencode($pg[id]);       //�����ʿ�
$at_amt=$_POST['allat_amt'];         //���� �ݾ��� �ٽ� ����ؼ� ������ ��(��ŷ����)
                                         //( session, DB ��� )
/*********************************************************/

// ��û ������ ����
//----------------------

$at_data   = "allat_shop_id=".$at_shop_id.
             "&allat_amt=".$at_amt.
             "&allat_enc_data=".$_POST["allat_enc_data"].
             "&allat_cross_key=".$at_cross_key;


// �þ� ���� ������ ��� : ApprovalReq->����Լ�, $at_txt->�����
//----------------------------------------------------------------
$at_txt = ApprovalReq($at_data,$pg[ssl]); // ���� �ʿ� (SSL:SSL�̿�� / NOSSL:SSL�̻���-�����ڵ� 0212�� ��� ���)
// �� �κп��� �α׸� ����� ���� �����ϴ�.
// (�þ� ���� ������ ��� �Ŀ� �α׸� �����, ��ſ����� ���� �����ľ��� �����մϴ�.)
$at_return	= return_allat($at_txt);

// �þ� �α�
$logMsg = chr(9).str_replace(chr(10),chr(10).chr(9), str_replace('=', chr(9).chr(9).'= ', $at_txt)).chr(10);
allat_log_write($logMsg);

// ���� ��� �� Ȯ��
//------------------
$REPLYCD   =getValue("reply_cd",$at_txt);        //����ڵ�
$REPLYMSG  =getValue("reply_msg",$at_txt);       //��� �޼���


// �����α� ����
$at_return = array_map("trim",$at_return);
extract($at_return);

/*******************************************************************************
reply_cd			= 0000				# ����ڵ�
reply_msg			= ����				# ����޼���
order_no			= 1341801465732		# �ֹ���ȣ
amt					= 1000				# ���αݾ�
pay_type			= ISP				# ���Ҽ��� (3D, ISP, NOR, ABANK)
approval_ymdhms		= 20120709113811	# �����Ͻ�
seq_no				= 164884116			# �ŷ��Ϸù�ȣ
escrow_yn			=					# ����ũ�ο��� - Y(����ũ��), N(������)
******************************�ſ�ī��******************************************
approval_no			= 30012692			# ���ι�ȣ
card_id				= 00				# ī��ID - ī�������ڵ�(��:01,02,�� �� )
card_nm				= �׽�Ʈ			# ī��� - ī��������(��:�Ｚ, ����, �� �� )
sell_mm				= 00				# �Һΰ���
zerofee_yn			= N					# ������(Y),�Ͻú�(N)
cert_yn				= N					# �������� - ����(Y),������(N)
contract_yn			= N					# �����Ϳ��� - 3�ڰ�����(Y),��ǥ������(N)
save_amt			=					# ���̺� ���� �ݾ�
******************************������ü / �������*******************************
bank_id				=					# ����ID
bank_nm				=					# �����
cash_bill_no		=					# ���ݿ������Ϸù�ȣ - ���ݿ����� ��Ͻ�
******************************�������******************************************
account_no			=					# ���¹�ȣ
income_acc_nm		=					# �Աݰ��¸�
account_nm			=					# �Ա��ڸ�
income_limit_ymd	=					# �Աݱ�����
income_expect_ymd	=					# �Աݿ�����
cash_yn				=					# ���ݿ�������û����
******************************�޴�������****************************************
hp_id				=					# �̵���Ż籸��
******************************��ǰ�ǰ���****************************************
ticket_id			=					# ��ǰ�� ID
ticket_name			=					# ��ǰ�� �̸�
ticket_pay_type		=					# ��������
********************************************************************************
sfcard_id		= 00					#
sfcard_nm		= �׽�Ʈ				#
*******************************************************************************/

switch ($pay_type){
	case "3D": case "ISP": case "NOR":
		$settlelogAdd = "
����ī�� : [$card_id] $card_nm
�Һΰ��� : $sell_mm
������   : $zerofee_yn
";
		break;
	case "ABANK":
		$settlelogAdd = "
�������� : [$bank_id] $bank_nm
���ݿ������Ϸù�ȣ : $cash_bill_no
";
		break;
	case "VBANK":
		$settlelogAdd = "
������� : $bank_nm $account_no $account_nm
�Աݰ��¸� : $income_account_nm
�Աݱ����� : $income_limit_ymd
�Աݿ����� : $income_expect_ymd
���ݿ�������û���� : $cash_yn
���ݿ������Ϸù�ȣ : $cash_bill_no
";
		break;
	case "HP":
		$settlelogAdd = "
�̵���Ż籸�� : $hp_id
";
		break;
}

$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
����ڵ� : $reply_cd
������� : $reply_msg
���αݾ� : $amt
���Ҽ��� : $pay_type
�����Ͻ� : $approval_ymdhms
�ŷ���ȣ : $seq_no
���ι�ȣ : $approval_no
�������� : $cert_yn
����ũ�� : $escrow_yn
----------------------------------------";

if ($settlelogAdd) $settlelog .= $settlelogAdd."----------------------------------------";

// ���ں������� �߱�
@session_start();
if (session_is_registered('eggData') === true && !strcmp($REPLYCD,"0000")){
	if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION[eggData];
		switch ($pay_type){
			case "3D": case "ISP": case "NOR":
				$eggData[payInfo1] = $card_nm; # (*) ��������(ī���)
				$eggData[payInfo2] = $approval_no; # (*) ��������(���ι�ȣ)
				break;
			case "ABANK":
				$eggData[payInfo1] = $bank_nm; # (*) ��������(�����)
				$eggData[payInfo2] = $seq_no; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
				break;
			case "VBANK":
				$eggData[payInfo1] = $bank_nm; # (*) ��������(�����)
				$eggData[payInfo2] = $account_no; # (*) ��������(���¹�ȣ)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $pay_type == "VBANK" ){
			$REPLYCD = '';
		}
		else if ( $eggCls->isErr == true && in_array($pay_type, array("3D","ISP","NOR","ABANK")) );
	}
	session_unregister('eggData');
}

// �ŷ��Ϸù�ȣ ����
$query = "update ".GD_ORDER." set cardtno='".$seq_no."' where ordno='".$ordno."'";
$db -> query($query);

// ������� ������ ��� üũ �ܰ� ����
$res_cstock = true;
if($cfg['stepStock'] == '1' && $pay_type=="VBANK") $res_cstock = false;

// item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$cancel -> cancel_allat_request($ordno);
	exit;
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");

  // ����� ó��
  //--------------------------------------------------------------------------
  // ��� ���� '0000'�̸� ������. ��, allat_test_yn=Y �ϰ�� '0001'�� ������.
  // ���� ����   : allat_test_yn=N �� ��� reply_cd=0000 �̸� ����
  // �׽�Ʈ ���� : allat_test_yn=Y �� ��� reply_cd=0001 �̸� ����
  //--------------------------------------------------------------------------
 if($oData['step'] > 0 || $oData['vAccount'] != ''){		// �ߺ�����

	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else  if( !strcmp($REPLYCD,"0000") ){
    // reply_cd "0000" �϶��� ����
    $ORDER_NO         =getValue("order_no",$at_txt);
    $AMT              =getValue("amt",$at_txt);
    $PAY_TYPE         =getValue("pay_type",$at_txt);
    $APPROVAL_YMDHMS  =getValue("approval_ymdhms",$at_txt);
    $SEQ_NO           =getValue("seq_no",$at_txt);
    $APPROVAL_NO      =getValue("approval_no",$at_txt);
    $CARD_ID          =getValue("card_id",$at_txt);
    $CARD_NM          =getValue("card_nm",$at_txt);
    $SELL_MM          =getValue("sell_mm",$at_txt);
    $ZEROFEE_YN       =getValue("zerofee_yn",$at_txt);
    $CERT_YN          =getValue("cert_yn",$at_txt);
    $CONTRACT_YN      =getValue("contract_yn",$at_txt);
    $SAVE_AMT         =getValue("save_amt",$at_txt);
    $BANK_ID          =getValue("bank_id",$at_txt);
    $BANK_NM          =getValue("bank_nm",$at_txt);
    $CASH_BILL_NO     =getValue("cash_bill_no",$at_txt);
    $ESCROW_YN        =getValue("escrow_yn",$at_txt);
    $ACCOUNT_NO       =getValue("account_no",$at_txt);
    $ACCOUNT_NM       =getValue("account_nm",$at_txt);
    $INCOME_ACC_NM    =getValue("income_account_nm",$at_txt);
    $INCOME_LIMIT_YMD =getValue("income_limit_ymd",$at_txt);
    $INCOME_EXPECT_YMD=getValue("income_expect_ymd",$at_txt);
    $CASH_YN          =getValue("cash_yn",$at_txt);
    $HP_ID            =getValue("hp_id",$at_txt);
    $TICKET_ID        =getValue("ticket_id",$at_txt);
    $TICKET_PAY_TYPE  =getValue("ticket_pay_type",$at_txt);
    $TICKET_NAME      =getValue("ticket_nm",$at_txt);

   /* echo "����ڵ�              : ".$REPLYCD."<br>";
    echo "����޼���            : ".$REPLYMSG."<br>";
    echo "�ֹ���ȣ              : ".$ORDER_NO."<br>";
    echo "���αݾ�              : ".$AMT."<br>";
    echo "���Ҽ���              : ".$PAY_TYPE."<br>";
    echo "�����Ͻ�              : ".$APPROVAL_YMDHMS."<br>";
    echo "�ŷ��Ϸù�ȣ          : ".$SEQ_NO."<br>";
    echo "����ũ�� ���� ����    : ".$ESCROW_YN."<br>";
    echo "=============== �ſ� ī�� ===============================<br>";
    echo "���ι�ȣ              : ".$APPROVAL_NO."<br>";
    echo "ī��ID                : ".$CARD_ID."<br>";
    echo "ī���                : ".$CARD_NM."<br>";
    echo "�Һΰ���              : ".$SELL_MM."<br>";
    echo "�����ڿ���            : ".$ZEROFEE_YN."<br>";   //������(Y),�Ͻú�(N)
    echo "��������              : ".$CERT_YN."<br>";      //����(Y),������(N)
    echo "�����Ϳ���            : ".$CONTRACT_YN."<br>";  //3�ڰ�����(Y),��ǥ������(N)
    echo "���̺� ���� �ݾ�      : ".$SAVE_AMT."<br>";
    echo "=============== ���� ��ü / ������� ====================<br>";
    echo "����ID                : ".$BANK_ID."<br>";
    echo "�����                : ".$BANK_NM."<br>";
    echo "���ݿ����� �Ϸ� ��ȣ  : ".$CASH_BILL_NO."<br>";
    echo "=============== ������� ================================<br>";
    echo "���¹�ȣ              : ".$ACCOUNT_NO."<br>";
    echo "�Աݰ��¸�            : ".$INCOME_ACC_NM."<br>";
    echo "�Ա��ڸ�              : ".$ACCOUNT_NM."<br>";
    echo "�Աݱ�����            : ".$INCOME_LIMIT_YMD."<br>";
    echo "�Աݿ�����            : ".$INCOME_EXPECT_YMD."<br>";
    echo "���ݿ�������û ����   : ".$CASH_YN."<br>";
    echo "=============== �޴��� ���� =============================<br>";
    echo "�̵���Ż籸��        : ".$HP_ID."<br>";
    echo "=============== ��ǰ�� ���� =============================<br>";
    echo "��ǰ�� ID             : ".$TICKET_ID."<br>";
    echo "��ǰ�� �̸�           : ".$TICKET_NAME."<br>";
    echo "��������              : ".$TICKET_PAY_TYPE."<br>"; */

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);	

	// ����ũ�� ���� Ȯ��
	$escrowyn = ($escrow_yn=="Y") ? "y" : "n";

	// ���� ���� ����
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	// ������� ������ �������� ����
	if ($pay_type=="VBANK"){
		$vAccount = $bank_nm." ".$account_no." ".$account_nm;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	// ���ݿ����� ����
	if ($cash_bill_no != ''){
		$qrc1 .= "cashreceipt='{$cash_bill_no}',";
	}

	// �ǵ���Ÿ ����
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

	// �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	// ��� ó��
	setStock($ordno);

	// ��ǰ���Խ� ������ ��� _ 2007-06-04
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	### �ֹ�Ȯ�θ���
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	// SMS ���� ����
	$dataSms = $data;

	if ($pay_type!="VBANK"){
		sendMailCase($data[email],1,$data);			// �Ա�Ȯ�θ���
		sendSmsCase('incash',$data[mobileOrder]);	// �Ա�Ȯ��SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	// �ֹ�Ȯ��SMS
	}
	
	

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");



  }else{
    // reply_cd �� "0000" �ƴҶ��� ���� (�ڼ��� ������ �Ŵ�������)
    // reply_msg �� ���п� ���� �޼���
    echo "����ڵ�  : ".$REPLYCD."<br>";
    echo "����޼���: ".$REPLYMSG."<br>";

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");

	// Ncash ���� ���� ��� API ȣ��
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");
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