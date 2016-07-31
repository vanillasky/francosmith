<?php
/**
 * @Path		: /shop/order/ipay_order_indb.php
 * @Description	: ���� �������� ������� �̿� �ֹ� DB ó�� ������
 * @Author		: ������@������
 * @Since		: 2012.05.19
 */

include "../lib/library.php";
include "../conf/config.php";
require_once dirname(__FILE__)."/../lib/auctionIpay.service.class.php";
require_once dirname(__FILE__)."/../lib/integrate_order_processor.class.php";
require_once dirname(__FILE__)."/../lib/integrate_order_processor.model.ipay.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// Ncash �ŷ� Ȯ�� API
include "../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($result['PaymentType']=='A') $ncashResult = $naverNcash->payment_approval($_GET['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_GET['ordno'], true);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.\r\n���� �������� ������ �ڵ���ҵ��� �����Ƿ� �Ա��� �Ϸ�Ȱ��\r\n���θ� �����ͷ� ȯ�ҿ�û �ֽñ� �ٶ��ϴ�.', 'order_fail.php?ordno='.$_GET['ordno'],'parent');
		exit();
	}
}

$auctionIpay = new integrate_order_processor_ipay();

// �����Ϸ� ���� ����
$result = $auctionIpay->GetIpayAccountNumb($_GET['ipayno']);

$ordno = $_GET['ordno'];

$settlelogAdd = "";
$TypeName = "";

$PayPrice = (int)$result['PayPrice'];

switch ($result['PaymentType']){
	case "A" :	// �������Ա�(�������)
		if($PayPrice==0)
		{
			$settlelogAdd = PHP_EOL."���� ���������� ����".PHP_EOL;
		}
		else
		{
			$settlelogAdd = "
�� �� �� : ".$result['BankName']."
������� : ".$result['AcctNumb']."
���������� : ".$result['ExpireDate']."
";
			$TypeName = "�������Ա�(�������)";
		}
		break;
	case "C" :	// ī�����
		$settlelogAdd = "
�����Ͻ� : ".$result['PayDate']."
�ҺαⰣ : ".$result['CardMonth']."
�������Һ� : ".$result['NoInterestYN']."
�ſ�ī��� : ".$result['CardName']."
ī���ȣ : ".$result['CardNumb']."-****-****
";
		$TypeName = "�ſ�ī�����";
		break;
	case "D" :	// �ǽð�������ü
		$settlelogAdd = "
�����Ͻ� : ".$result['PayDate']."
";
		$TypeName = "�ǽð�������ü";
		break;
	case "M" :	// �޴�������
		$TypeName = "�޴�������";
		break;
}


$settlelog = $ordno." (".date('Y:m:d H:i:s').")
----------------------------------------
�ŷ���ȣ : ".$result['OrderNo']."
������� : ����ó��
���ҹ�� : ".$TypeName."
���αݾ� : ".$result['PayPrice']."
----------------------------------------";

$settlelog .= $settlelogAdd."----------------------------------------";


### ������� ������ ��� üũ �ܰ� ����
$res_cstock = true;
if($cfg['stepStock'] == '1' && $result['PaymentType']=="A") $res_cstock = false;

### item check stock
include "../lib/cardCancel.class.php";
$pre_pg = $pg;	// pg �ӽ� ����
$pg = "ipay";
$pg['id'] = "ipay";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$step = "51";
}
$pg = $pre_pg;	 // pg ����

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != ''){		// �ߺ�����
	### �α� ����
	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$result[CardName]");

}elseif($step != 51){	// ��������
	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	include "../lib/cart.class.php";

	$cart = new Cart($_COOKIE[gd_isDirect]);
	$cart->chkCoupon();
	$cart->delivery = $data[delivery];
	$cart->dc = $member[dc]."%";
	$cart->calcu();
	$cart -> totalprice += $data[price];

	### �ֹ�Ȯ�θ���
	$data[cart] = $cart;
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	sendMailCase($data[email],0,$data);

	### ���� ���� ����
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### ������� ������ �������� ����
	if ($result['PaymentType']=="A"){
		$vAccount = $result['BankName']." ".$result['AcctNumb'];
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### ���ݿ����� ����

	### gd_order_item �� ���������ֹ���ȣ(AuctionOrderNos) �� ó��
	$ItemNos = array();
	$AuctionOrderNos = array();

	$ItemNos = explode("@",$result['ItemNos']);
	$AuctionOrderNos = explode("@",$result['AuctionOrderNos']);

	for($i=0;$i<count($ItemNos)-1;$i++){
		$order_ItemSno = "";
		$order_ItemSno = split_betweenStr($ItemNos[$i],'_','=');
		$db->query("update ".GD_ORDER_ITEM." set ipay_ordno='$AuctionOrderNos[$i]' where sno='$order_ItemSno[0]'");
	}

	// �������
	switch($result['PaymentType'])
	{
		// �������
		case 'A':
			$ipay_settlekind = 'v';
			break;
		// �ſ�ī��
		case 'C':
			$ipay_settlekind = 'c';
			break;
		// �����
		case 'M':
			$ipay_settlekind = 'h';
			break;
		// �ǽð� ������ü
		case 'D':
			$ipay_settlekind = 'o';
			break;
	}

	// gd_order ���̺� ���� �̸Ӵ�, ����Ʈ �����ǰ� �������ݾ� ������Ʈ
	$qrc1 .= "`settleprice`=".$result['PayPrice'].", `prn_settleprice`=".$result['PayPrice'].",";
	
	// ���������ݾ��� 0���̸� �������� �� �Ա�Ȯ�� ó��
	if($PayPrice==0)
	{
		$step = 1;
		$ipay_settlekind = 'd';
		$qrc1 .= "`cyn`='y', `cdt`=NOW(),";
		$qrc2 .= "`cyn`='y',";
	}

	### �ǵ���Ÿ ����
	$db->query("
	update ".GD_ORDER." set $qrc1
		step			= '$step',
		step2			= '',
		vAccount		= '$vAccount',
		`settlekind`	= '".$ipay_settlekind."',
		settlelog		= concat(ifnull(settlelog,''),'$settlelog'),
		cardtno			= '$result[OrderNo]',
		ipay_payno		= '$result[PayNo]',
		ipay_cartno		= '$result[IpayCartNo]'
	where ordno='$ordno'"
	);

	$ItemNos = explode('@', $result['ItemNos']);
	array_pop($ItemNos);
	$AuctionOrderNos = explode('@', $result['AuctionOrderNos']);
	array_pop($AuctionOrderNos);
	foreach($AuctionOrderNos as $key => $AuctionOrderNo)
	{
		$ItemNo = explode('=', $ItemNos[$key]);
		$option = explode('_', $ItemNo[0]);
		$db->query("
		UPDATE `".GD_ORDER_ITEM."` SET ".$qrc2."
			`istep`='".$step."',
			`ipay_ordno`='".$AuctionOrderNo."',
			`ipay_itemno`='".$ItemNo[1]."'
		WHERE `ordno`='".$ordno."' AND `sno`=".$option[1]
		);
	}

	### �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### ��� ó��
	setStock($ordno);

	### ��ǰ���Խ� ������ ���
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	### SMS ���� ����
	$dataSms = $data;

	if ($result['PaymentType']!="A"){
		sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
		sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
	}

	go("./order_end.php?ordno=$ordno&card_nm=$result[CardName]");

}else{		// ī����� ����
	if($step == '51'){
		$cancel -> cancel_db_proc($ordno);
	}else{
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
	}

	// Ncash ���� ���� ��� API ȣ��
	if($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);

	go("./order_fail.php?ordno=$ordno");

}

?>