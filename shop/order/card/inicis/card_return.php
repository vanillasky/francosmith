<?

$banks	= array(
		'02'	=> '�ѱ��������',
		'03'	=> '�������',
		'04'	=> '��������',
		'05'	=> '��ȯ����',
		'06'	=> '��������',
		'07'	=> '�����߾�ȸ',
		'11'	=> '�����߾�ȸ',
		'12'	=> '��������',
		'16'	=> '�����߾�ȸ',
		'20'	=> '�츮����',
		'21'	=> '��������',
		'22'	=> '�������',
		'23'	=> '��������',
		'24'	=> '��������',
		'25'	=> '��������',
		'26'	=> '��������',
		'27'	=> '�ѹ�����',
		'31'	=> '�뱸����',
		'32'	=> '�λ�����',
		'34'	=> '��������',
		'35'	=> '��������',
		'37'	=> '��������',
		'38'	=> '��������',
		'39'	=> '�泲����',
		'41'	=> '��ī��',
		'53'	=> '��Ƽ����',
		'54'	=> 'ȫ�����������',
		'71'	=> '��ü��',
		'81'	=> '�ϳ�����',
		'83'	=> '��ȭ����',
		'87'	=> '�ż���',
		'88'	=> '��������',
);

$cards	= array(
		'01'	=> '��ȯ',
		'03'	=> '�Ե�',
		'04'	=> '����',
		'06'	=> '����',
		'11'	=> 'BC',
		'12'	=> '�Ｚ',
		'13'	=> 'LG',
		'14'	=> '����',
		'21'	=> '�ؿܺ���',
		'22'	=> '�ؿܸ�����',
		'23'	=> 'JCB',
		'24'	=> '�ؿܾƸ߽�',
		'25'	=> '�ؿܴ��̳ʽ�',
);

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.inicis.php";

// PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_POST['ordno'],$_POST['price']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['ordno'],'parent');
	exit();
}

// Ncash ���� ���� API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if ($_POST['paymethod']=="VBank") $ncashResult = $naverNcash->payment_approval($_POST['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['ordno'], true);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', '../../order_fail.php?ordno='.$_POST['ordno'],'parent');
		exit();
	}
}

extract($_POST);

include "sample/INIpay41Lib.php";
$inipay = new INIpay41;

/*********************
 * 3. ���� ���� ���� *
 *********************/
$inipay->m_inipayHome = dirname($_SERVER['SCRIPT_FILENAME']); // �̴����� Ȩ���͸�
$inipay->m_type = "securepay"; // ����
$inipay->m_pgId = "INIpay".$pgid; // ����
$inipay->m_subPgIp = "203.238.3.10"; // ����
$inipay->m_keyPw = "1111"; // Ű�н�����(�������̵� ���� ����)
$inipay->m_debug = "true"; // �α׸��("true"�� �����ϸ� �󼼷αװ� ������.)
$inipay->m_mid = $mid; // �������̵�
$inipay->m_uid = $uid; // INIpay User ID
$inipay->m_uip = '127.0.0.1'; // ����
$inipay->m_goodName = $goodname;
$inipay->m_currency = $currency;
$inipay->m_price = $price;
$inipay->m_merchantReserved1 = "Tax=".$tax."&TaxFree=".$taxfree; //�ΰ��� & �鼼 ����
$inipay->m_buyerName = $buyername;
$inipay->m_buyerTel = $buyertel;
$inipay->m_buyerEmail = $buyeremail;
$inipay->m_payMethod = $paymethod;
$inipay->m_encrypted = $encrypted;
$inipay->m_sessionKey = $sessionkey;
$inipay->m_url = "http://".$_SERVER[SERVER_NAME]; // ���� ���񽺵Ǵ� ���� SITE URL�� �����Ұ�
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
 * 5. ����  ���																								*
 *																												*
 *  ��. ��� ���� ���ܿ� ����Ǵ� ���� ��� ����																*
 * 	�ŷ���ȣ : $inipay->m_tid																					*
 * 	����ڵ� : $inipay->m_resultCode ("00"�̸� ���� ����)														*
 * 	������� : $inipay->m_resultMsg (���Ұ���� ���� ����)														*
 * 	���ҹ�� : $inipay->m_payMethod (�Ŵ��� ����)																*
 * 	�����ֹ���ȣ : $inipay->m_moid																				*
 *																												*
 *  ��. �ſ�ī��,ISP,�ڵ���, ��ȭ ����, ���������ü, OK CASH BAG Point �����ÿ��� ���� ��� ����				*
 *              (�������Ա� , ��ȭ ��ǰ��)																		*
 * 	�̴Ͻý� ���γ�¥ : $inipay->m_pgAuthDate (YYYYMMDD)														*
 * 	�̴Ͻý� ���νð� : $inipay->m_pgAuthTime (HHMMSS)															*
 *																												*
 *  ��. �ſ�ī��  ���������� �̿�ÿ���  ������� ����															*
 *																												*
 * 	�ſ�ī�� ���ι�ȣ : $inipay->m_authCode																		*
 * 	�ҺαⰣ : $inipay->m_cardQuota																				*
 * 	�������Һ� ���� : $inipay->m_quotaInterest ("1"�̸� �������Һ�)												*
 * 	�ſ�ī��� �ڵ� : $inipay->m_cardCode (�Ŵ��� ����)															*
 * 	ī��߱޻� �ڵ� : $inipay->m_cardIssuerCode (�Ŵ��� ����)													*
 * 	�������� ���࿩�� : $inipay->m_authCertain ("00"�̸� ����)													*
 *      ���� �̺�Ʈ ���� ���� : $inipay->m_eventFlag															*
 *																												*
 *      �Ʒ� ������ "�ſ�ī�� �� OK CASH BAG ���հ���" �Ǵ�"�ſ�ī�� ���ҽÿ� OK CASH BAG����"�ÿ� �߰��Ǵ� ����*
 * 	OK Cashbag ���� ���ι�ȣ : $inipay->m_ocbSaveAuthCode														*
 * 	OK Cashbag ��� ���ι�ȣ : $inipay->m_ocbUseAuthCode														*
 * 	OK Cashbag �����Ͻ� : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)												*
 * 	OCB ī���ȣ : $inipay->m_ocbcardnumber																		*
 * 	OK Cashbag ���հ���� �ſ�ī�� ���ұݾ� : $inipay->m_price1													*
 * 	OK Cashbag ���հ���� ����Ʈ ���ұݾ� : $inipay->m_price2													*
 *																												*
 * ��. OK CASH BAG ���������� �̿�ÿ���  ������� ����	 ���													*
 * 	OK Cashbag ���� ���ι�ȣ : $inipay->m_ocbSaveAuthCode														*
 * 	OK Cashbag ��� ���ι�ȣ : $inipay->m_ocbUseAuthCode														*
 * 	OK Cashbag �����Ͻ� : $inipay->m_ocbAuthDate (YYYYMMDDHHMMSS)												*
 * 	OCB ī���ȣ : $inipay->m_ocbcardnumber																		*
 *																												*
 * ��. ������ �Ա� ���������� �̿�ÿ���  ���� ��� ����														*
 * 	������� ��ȣ : $inipay->m_vacct																			*
 * 	�Ա��� ���� �ڵ� : $inipay->m_vcdbank																		*
 * 	�Աݿ����� : $inipay->m_dtinput (YYYYMMDD)																	*
 * 	�۱��� �� : $inipay->m_nminput																				*
 * 	������ �� : $inipay->m_nmvacct																				*
 *																												*
 * ��. �ڵ���, ��ȭ�����ÿ���  ���� ��� ���� ( "���� ���� �ڼ��� ����"���� �ʿ� , ���������� �ʿ���� ������)  *
 * 	��ȭ���� ����� �ڵ� : $inipay->m_codegw                        											*
 *																												*
 * ��. �ڵ��� ���������� �̿�ÿ���  ���� ��� ����																*
 * 	�޴��� ��ȣ : $inipay->m_nohpp (�ڵ��� ������ ���� �޴�����ȣ)       										*
 *																												*
 * ��. ��ȭ ���������� �̿�ÿ���  ���� ��� ����																*
 * 	��ȭ��ȣ : $inipay->m_noars (��ȭ������  ���� ��ȭ��ȣ)      												*
 * 																												*
 * ��. ��ȭ ��ǰ�� ���������� �̿�ÿ���  ���� ��� ����														*
 * 	���� ���� ID : $inipay->m_cultureid	                           												*
 *																												*
 * ��. ��� ���� ���ܿ� ���� ���� ���нÿ��� ���� ��� ���� 													*
 * 	�����ڵ� : $inipay->m_resulterrcode                             											*
 *																												*
 ****************************************************************************************************************/

$inipay->m_resultMsg = strip_tags($inipay->m_resultMsg);

switch ($inipay->m_payMethod){
	case "Card": case "VCard":
		$card_nm = $cards[$inipay->m_cardCode];
		$settlelogAdd = "
�����Ͻ� : $inipay->m_pgAuthDate $inipay->m_pgAuthTime
���ι�ȣ : $inipay->m_authCode
�ҺαⰣ : $inipay->m_cardQuota
�� �� �� : $inipay->m_quotaInterest
�ſ�ī��� : [$inipay->m_cardCode] $card_nm
ī��߱޻� : $inipay->m_cardIssuerCode
�������� : $inipay->m_authCertain
�� �� Ʈ : $inipay->m_eventFlag
";
		break;
	case "DirectBank":
		$settlelogAdd = "
�����Ͻ� : $inipay->m_pgAuthDate $inipay->m_pgAuthTime
";
		break;
	case "VBank":
		$bank_nm = $banks[$inipay->m_vcdbank];
		$settlelogAdd = "
�� �� �� : [$inipay->m_vcdbank] $bank_nm
������� : $inipay->m_vacct
�����ָ� : $inipay->m_nmvacct
�Աݿ����� : $inipay->m_dtinput
�۱��ڸ� : $inipay->m_nminput
";
		break;
	case "HPP":
		$settlelogAdd = "
�ڵ�����ȣ : $inipay->m_nohpp
";
		break;
}

$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
�ŷ���ȣ : $inipay->m_tid
����ڵ� : $inipay->m_resultCode
������� : $inipay->m_resultMsg
���ҹ�� : $inipay->m_payMethod
���αݾ� : $inipay->m_price
----------------------------------------";

$settlelog .= $settlelogAdd."----------------------------------------";

### ���ں������� �߱�
@session_start();
if (session_is_registered('eggData') === true && !strcmp($inipay->m_resultCode,"00")){
	if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION[eggData];
		switch ($inipay->m_payMethod){
			case "Card": case "VCard":
				$eggData[payInfo1] = $cards[$inipay->m_cardCode]; # (*) ��������(ī���)
				$eggData[payInfo2] = $inipay->m_authCode; # (*) ��������(���ι�ȣ)
				break;
			case "DirectBank":
				$eggData[payInfo1] = $banks[$inipay->m_directbankcode]; # (*) ��������(�����)
				$eggData[payInfo2] = $inipay->m_tid; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
				break;
			case "VBank":
				$eggData[payInfo1] = $banks[$inipay->m_vcdbank]; # (*) ��������(�����)
				$eggData[payInfo2] = $inipay->m_vacct; # (*) ��������(���¹�ȣ)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $inipay->m_payMethod == "VBank" ){
			$inipay->m_resultCode = '';
		}
		else if ( $eggCls->isErr == true && in_array($inipay->m_payMethod, array("Card","VCard","DirectBank")) );
	}
	session_unregister('eggData');
}

### ������� ������ ��� üũ �ܰ� ����
$res_cstock = true;
if($cfg['stepStock'] == '1' && $inipay->m_payMethod=="VBank") $res_cstock = false;

### item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$inipay->m_type = "cancel"; // ����
	$inipay->m_msg = "OUT OF STOCK"; // ��һ���
	$inipay->startAction();
	if($inipay->m_resultCode == "00")
	{
		$inipay->m_resultCode = "01";
		$inipay->m_resultMsg = "OUT OF STOCK";
	}
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($inipay->m_resultCode,"1179")){		// �ߺ�����

	### �α� ����
	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else if( !strcmp($inipay->m_resultCode,"00") ){		// ī����� ����

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	### ����ũ�� ���� Ȯ��
	$escrowyn = ($_POST[escrow]=="Y") ? "y" : "n";
	$escrowno = $inipay->m_tid;

	### ���� ���� ����
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### ������� ������ �������� ����
	if ($inipay->m_payMethod=="VBank"){
		$vAccount = $bank_nm." ".$inipay->m_vacct." ".$inipay->m_nmvacct;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### ���ݿ����� ����
	if ($inipay->rcash_rslt == '00' || $inipay->rcash_rslt == '0000' || $inipay->m_rcash_rslt == '00' || $inipay->m_rcash_rslt == '0000'){
		$qrc1 .= "cashreceipt='{$inipay->m_tid}',";
	}

	### �ǵ���Ÿ ����
	$db->query("
	update ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
		cardtno		= '".$inipay->m_tid."'
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	### �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### ��� ó��
	setStock($ordno);

	### ��ǰ���Խ� ������ ���
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	### �ֹ�Ȯ�θ���
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	### SMS ���� ����
	$dataSms = $data;

	if ($inipay->m_payMethod!="VBank"){
		sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
		sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
	}

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else {		// ī����� ����
	if($inipay->m_msg == "OUT OF STOCK"){
		$cancel -> cancel_db_proc($ordno,$inipay->m_tid);
	}else{
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$inipay->m_tid."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
	}

	// Ncash ���� ���� ��� API ȣ��
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>