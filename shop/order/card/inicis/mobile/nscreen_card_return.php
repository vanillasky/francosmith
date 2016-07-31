<?

include dirname(__FILE__)."/../../../../lib/library.php";
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inicis.php";


$page_type = $_GET['page_type'];

if($page_type=='mobile') {
	$order_end_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php';
	$order_fail_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php';
}
else {
	$order_end_page = $cfg['rootDir'].'/order/order_end.php';
	$order_fail_page = $cfg['rootDir'].'/order/order_fail.php';
}


parse_str($_POST['P_NOTI'],$P_NOTI);
// PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_GET['ordno'],$P_NOTI['P_AMT']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.',$order_fail_page.'?ordno='.$_GET['ordno'],'parent');
	exit();
}

// ���̹� ���ϸ��� ���� ���� API
include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
$naverNcash = new naverNcash(true);
if ($naverNcash->useyn == 'Y') {
	if ($_GET['settlekind'] == 'v') $ncashResult = $naverNcash->payment_approval($_GET['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_GET['ordno'], true);
	if ($ncashResult === false) {
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', $order_fail_page.'?ordno='.$_GET['ordno'], 'parent');
		exit();
	}
}

$pg_mobile = $pg;

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

/* ���� ���� */
if($_POST['P_STATUS'] == '00'){
	$reqData = array(
		'P_TID' => $_POST['P_TID'],
		'P_MID' => $pg_mobile['id'],
	);

	/* ���� ���� ��û */
	$res = readpost($_POST['P_REQ_URL'],$reqData);

	/* ������� ���ϰ� �Ľ� */
	$resData = stringUnserialize($res);


	$resData['P_RMESG1'] = strip_tags($resData['P_RMESG1']);

	$ordno = $resData['P_OID'];

	switch ($resData['P_TYPE']){
		case "CARD":
			$card_nm = $cards[$resData['P_FN_CD1']];
			$paymethod = 'Card';

			$settlelogAdd = "
�����Ͻ� : ".$resData['P_AUTH_DT']."
���ι�ȣ : ".$resData['P_AUTH_NO']."
�ҺαⰣ : ".$resData['P_RMESG2']."
�ſ�ī��� : [".$resData['P_FN_CD1']."] $card_nm
ī��߱޻� : ".$resData['P_CARD_ISSUER_CODE']."
";
			break;
		case "VBANK":
			$bank_nm = $banks[$resData['P_VACT_BANK_CODE']];
			$paymethod = 'VBank';
			$settlelogAdd = "
�� �� �� : [".$resData['P_VACT_BANK_CODE']."] $bank_nm
������� : ".$resData['P_VACT_NUM']."
�����ָ� : ".$resData['P_VACT_NAME']."
�Աݸ����Ͻ� : ".$resData['P_VACT_DATE']." ".$resData['P_VACT_TIME']."
";
			break;
		case "MOBILE":
			$paymethod = 'HPP';
			$settlelogAdd = "
�޴�����Ż� : ".$resData['P_HPP_CORP']."
";
			break;
	}

	$settlelog = "INIpay Mobile ������û�� ���� ���
$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
�ŷ���ȣ : ".$resData['P_TID']."
����ڵ� : ".$resData['P_STATUS']."
������� : ".$resData['P_RMESG1']."
���ҹ�� : ".$resData['P_TYPE']."
���αݾ� : ".$resData['P_AMT']."
----------------------------------------";

	$settlelog .= $settlelogAdd."----------------------------------------";

	### ������� ������ ��� üũ �ܰ� ����
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $resData['P_TYPE']=="VBANK") $res_cstock = false;

	### item check stock
	include dirname(__FILE__)."/../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $res_cstock){

		include dirname(__FILE__)."/../sample/INIpay41Lib.php";
		$inipay = new INIpay41;

		/*********************
		 * ���� ���� ���� *
		 *********************/
		$inipay->m_inipayHome = dirname($_SERVER['SCRIPT_FILENAME']).'/../'; // �̴����� Ȩ���͸�
		$inipay->m_pgId = "INIpay".$pg_mobileid; // ����
		$inipay->m_subPgIp = "203.238.3.10"; // ����
		$inipay->m_keyPw = "1111"; // Ű�н�����(�������̵� ���� ����)
		$inipay->m_debug = "true"; // �α׸��("true"�� �����ϸ� �󼼷αװ� ������.)
		$inipay->m_mid = $pg_mobile['id']; // �������̵�
		$inipay->m_tid = $resData['P_TID']; // �ŷ���ȣ
		$inipay->m_uip = '127.0.0.1'; // ����
		$inipay->m_type = "cancel"; // ����
		$inipay->m_msg = "OUT OF STOCK"; // ��һ���
		$inipay->startAction();
		if($inipay->m_resultCode == "00")
		{
			$resData['P_STATUS'] = "01";
			$resData['P_RMESG1'] = "OUT OF STOCK";
		}
	}
	// �ŷ��ݾ� ������ Ȯ��
	else if (forge_order_check($ordno, $resData['P_AMT']) === false) {
		include dirname(__FILE__).'/../sample/INIpay41Lib.php';
		$inipay = new INIpay41;
		$inipay->m_inipayHome = dirname($_SERVER['SCRIPT_FILENAME']).'/../'; // �̴����� Ȩ���͸�
		$inipay->m_pgId = 'INIpay'.$pg_mobileid; // ����
		$inipay->m_subPgIp = '203.238.3.10'; // ����
		$inipay->m_keyPw = '1111'; // Ű�н�����(�������̵� ���� ����)
		$inipay->m_debug = 'true'; // �α׸��("true"�� �����ϸ� �󼼷αװ� ������.)
		$inipay->m_mid = $pg_mobile['id']; // �������̵�
		$inipay->m_tid = $resData['P_TID']; // �ŷ���ȣ
		$inipay->m_uip = '127.0.0.1'; // ����
		$inipay->m_type = 'cancel'; // ����
		$inipay->m_msg = '�ŷ��ݾ� ������ ������'; // ��һ���
		$inipay->startAction();
		if ($inipay->m_resultCode == '00') {
			$resData['P_STATUS'] = '01';
			$resData['P_RMESG1'] = '�ŷ��ݾ� �������� �����Ǿ� �ڵ���� �Ǿ����ϴ�.';
			$settlelog = '----------------------------------------'.
				PHP_EOL.'������� : �ŷ��ݾ� ������ ������ ���� �ڵ����'.
				PHP_EOL.'----------------------------------------'.
				PHP_EOL.$settlelog;
		}
		else {
			$settlelog = '----------------------------------------'.
				PHP_EOL.'������� : �ŷ��ݾ� �������� �����Ǿ����� �ڵ���� ����'.
				PHP_EOL.'PGȮ�� ���'.
				PHP_EOL.'----------------------------------------'.
				PHP_EOL.$settlelog;
		}
	}

	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($resData['P_STATUS'],"1179")){		// �ߺ�����
		### �α� ����
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go($order_end_page."?ordno=$ordno&card_nm=$card_nm","parent");

	} else if( !strcmp($resData['P_STATUS'],"00") ){		// ī����� ����

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
		$escrowno = $resData['P_TID'];

		### ���� ���� ����
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		### ������� ������ �������� ����
		if ($resData['P_TYPE']=="VBANK"){
			$vAccount = $bank_nm." ".$resData['P_VACT_NUM']." ".$resData['P_VACT_NAME'];
			$step = 0; $qrc1 = $qrc2 = "";
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
			cardtno		= '".$resData['P_TID']."'
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		### ��ǰ���Խ� ������ ���
		if ($sess[m_no] && $data[emoney]){
			setEmoney($sess[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
		}

		### �ֹ�Ȯ�θ���
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($ordno));
		}

		### SMS ���� ����
		$dataSms = $data;

		if ($resData['P_TYPE']!="VBANK"){
			sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
			sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
		}

		go($order_end_page."?ordno=$ordno&card_nm=$card_nm","parent");

	} else {		// ī����� ����
		if($resData['P_RMESG1'] == "OUT OF STOCK"){
			$cancel -> cancel_db_proc($ordno,$inipay->m_tid);
		}else{
			$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$resData['P_TID']."' where ordno='$ordno'");
			$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
		}

		// Ncash ���� ���� ��� API ȣ��
		if ($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

		go($order_fail_page."?ordno=$ordno","parent");
	}
}
else{
	$ordno = $_GET['ordno'];

	// Ncash ���� ���� ��� API ȣ��
	if ($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	msg($_POST['P_RMESG1']);
	go($order_fail_page."?ordno=$ordno","parent");
}

/* ������� ���ϰ� �Ľ� �Լ� */
function stringUnserialize($string){
	$string = trim($string);
	$arr = explode("&",$string);
	$result = array();
	foreach($arr as $v){
		$div = explode("=",$v);
		$result[$div[0]] = $div[1];
	}
	return $result;
}
?>