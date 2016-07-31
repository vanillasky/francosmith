<?php

//######################################################################
// ���ϸ� : mc_web_sample.php
// �ۼ��� : ���������
// �ۼ��� : 2012.09
// ��  �� : �޴��� Weblink ��� ���� ���� ������
// ��	�� : 0004

// �������� �ҽ� ���Ǻ��濡 ���� å���� ������𽺿��� å���� ���� �ʽ��ϴ�.
// ��û �Ķ���� �� ���� ��  ��������  Okurl / Notiurl ���� Return �Ǵ� �Ķ���Ϳ� ������ ����ó�� �����
// ���� �Ŵ����� �ݵ�� �����ϼ���.
// �����Ǽ��� ��ȯ�� �� ������� ������������� �����ٶ��ϴ�.

// ��ȣȭ ����  �ʼ� libCipher ���������� �������� ������ ��ġ
// ��ġ����� seed.tar ���ϰ� ��ġ�Ŵ��� ����
//######################################################################

include dirname(__FILE__).'/../../../lib/library.php';

$shopConfig = Core::loader('config')->_load_config();
$mobilians = Core::loader('Mobilians');
$cart = Core::loader('cart', $_COOKIE['gd_isDirect']);
$sendData = array();
$mobiliansCfg = $mobilians->getConfig();
$domain = array_shift(explode(':', $_SERVER['HTTP_HOST']));
$address = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$domain.($_SERVER['SERVER_PORT']!=='80'?':'.$_SERVER['SERVER_PORT']:'');

//######################################################################
// �޴��� ���� / ��������  ������  CASH_GB ������ ���� ������
//	$CASH_GB = 'MC' �޴��� ����â ȣ��
//	$CASH_GB = 'CE' �޴��� ��������  ȣ��
//######################################################################
$sendData['CASH_GB'] = 'MC'; 	// ��ǥ��������


//######################################################################
// �ʼ� �Է� �׸�
//######################################################################
if (isset($_GET['mode']) && $_GET['mode'] === 'resettle') {
	$lookupOrderItem = $db->query('SELECT goodsnm, opt1, opt2 FROM gd_order_item WHERE ordno='.$_POST['ordno']);
	$itemList = array();
	while ($orderItem = $db->fetch($lookupOrderItem, true)) {
		$itemList[] = array(
			'goodsnm' => $orderItem['goodsnm'],
			'opt' => array($orderItem['opt1'], $orderItem['opt2']),
		);
	}
	$sendData['Prdtnm'] = Mobilians::makeGoodsName($itemList);	// ��ǰ�� (50byte �̳�)
}
else {
	$sendData['Prdtnm'] = Mobilians::makeGoodsName($cart->item);	// ��ǰ�� (50byte �̳�)
}
$sendData['Prdtprice'] = $_POST['settleprice'];	// ������û�ݾ�
$sendData['Siteurl'] = preg_replace('/^(^www\.)?(.{0,20}).*$/', '$2', $_SERVER['SERVER_NAME']);	// ������������URL
$sendData['Tradeid'] = $_POST['ordno'];	// �������ŷ���ȣ Unique ������ ���� ����
$sendData['PAY_MODE'] = $mobiliansCfg['serviceType'];	// ������ �׽�Ʈ,�ǰ������� (00 : �׽�Ʈ����, 10 : �ǰŷ�����)
$sendData['MC_SVCID'] = $mobiliansCfg['serviceId'];	// ���񽺾��̵�(��ǥ����ID SKT������ ����)
$sendData['Okurl'] = $address.$shopConfig['rootDir'].'/order/card/mobilians/card_return.php';	// ����URL : �����Ϸ��뺸������ full Url (��:http://www.mcash.co.kr/okurl.php)


//######################################################################
// ���� �Է� �׸�
//######################################################################
$sendData['MC_FIXNO'] = 'N';	// ���������ȣ �����Ұ�����(N : �������� default, Y : �����Ұ�)
$sendData['Failurl'] = $address.$shopConfig['rootDir'].'/order/card/mobilians/card_return.php';	// ����URL : �������н��뺸������ full Url (��:http://www.mcash.co.kr/failurl.asp)
                       // ����ó���� ���� ����ó�� �ȳ��� ���������� �����ؾ� �� ��츸 ���
$sendData['MSTR'] = '';	// �������ݹ麯��
                        // ���������� �߰������� �Ķ���Ͱ� �ʿ��� ��� ����ϸ� &, % �� ���Ұ� (�� : MSTR="a=1|b=2|c=3")
$sendData['Payeremail'] = $_POST['email'];	// ������email
$sendData['EMAIL_HIDDEN'] = "N";	// ������email �Է�â ����(N default, Y �ΰ�� ����â���� �̸����׸� ����)
$sendData['Userid'] = $sess ? $sess['m_id'] : '';	// ������������ID
$sendData['Item'] = '';	// �������ڵ�
$sendData['Prdtcd'] = isset($_GET['isMobile']) ? 'MOB' : 'WEB';	// ��ǰ�ڵ�(����� ����ϰ� PC������ �����ϴ� �뵵�� �����)
$sendData['MC_Cpcode'] = '';	// ��������������key
$sendData['MC_AUTHPAY'] = 'N';	// �޴��� ���������� ����  'Y' �� ���� (�޴��� ���������� �Ϲ� ���ϸ�� ���� ������ ���)
$sendData['MC_AUTOPAY'] = 'N';	// �ڵ������� ���� �����Ϲݰ��� - �ڵ�����key �߱� (Y:���, N:�̻�� default)
$sendData['MC_PARTPAY'] = 'N';	// �κ���Ҹ� ���� �Ϲݰ��� - �ڵ�����key �߱� (Y : ���, N : �̻�� default)
//$sendData['MC_No'] = $_POST['mobileOrder'][0].$_POST['mobileOrder'][1].$_POST['mobileOrder'][2];	// ����� ����ȣ (����â ȣ��� ������ ����ȣ)

if (isset($shopConfig['adminEmail']) && strlen(trim($shopConfig['adminEmail'])) > 0) {
	$sendData['Notiemail'] = $shopConfig['adminEmail'];	// �˸�email : �ԱݿϷ� �� ���� ���������� ������ ������ ��� �˶� ������ ���� ������ ����� �̸����ּ�
}
$sendData['Notiurl'] = ProtocolPortDomain().$shopConfig['rootDir'].'/order/card/mobilians/card_notice.php';	// ����ó��URL : ���� �Ϸ� ��, �������� ���� �� ó���� �������� URL


//######################################################################
//- ������ ���� �����׸� ( ����  ����� �� �ֽ��ϴ�  )
//######################################################################
$sendData['LOGO_YN'] = 'N'; // ������ �ΰ� ��뿩�� (������ �ΰ� ���� 'Y'�� ����, ������ ������𽺿� ������ �ΰ� �̹����� �־����)
if (isset($_GET['isMobile'])) {
	include dirname(__FILE__).'/../../../conf/config.mobileShop.php';
	$sendData['CALL_TYPE'] = 'I';
	$sendData['IFRAME_NAME'] = '_parent';
	$sendData['Closeurl'] = ProtocolPortDomain().$cfgMobileShop['mobileShopRootDir'].'/ord/order.php';
}
else {
	$sendData['CALL_TYPE'] = 'P';
}
$sendData['CONTRACT_HIDDEN'] = 'Y'; // �̿��� ǥ�ÿ���(Y/N)
$sendData['MC_DEFAULTCOMMID'] = 'SKT'; // �⺻�����(SKT/KTF/LGT)
$sendData['MC_FIXCOMMID'] = ''; // ��������(SKT/KTF/LGT) ���� ������

// ����� ���� ����������� �÷��װ� �߰�
if (isset($_GET['isMobile']) && isset($_GET['pc'])) {
	$sendData['Okurl'] .= '?pc=true&isMobile=true';
	$sendData['Failurl'] .= '?pc=true&isMobile=true';
}

//######################################################################
//- ��ȣȭ ( ��ȣȭ ���� )
// Cryptstring �׸��� �ݾ׺����� ���� Ȯ�ο�����  �ݵ�� �Ʒ��� ���� ���ڿ��� �����Ͽ��� �մϴ�.
//
// ��) ��ȣȭ �ؽ�Ű�� ���������� �����ϴ� �ŷ���ȣ�� ���� ����Ǿ� ���ǹǷ�
//           ��ȣȭ�� �̿��� �ŷ���ȣ��  �����Ǿ� ���޵� ��� ��ȣȭ ���з� ���� ���� �Ұ�
//######################################################################
$sendData['Cryptyn'] = 'N';					// "Y" ��ȣȭ���, "N" ��ȣȭ�̻��

$mobilians->writeLog(
	'Paygate open start'.PHP_EOL.
	'File : '.__FILE__.PHP_EOL.
	'Transaction ID : '.$sendData['Tradeid'].PHP_EOL.
	'Send data : '.http_build_query($sendData)
);

if($sendData['Cryptyn'] == 'Y'){
	$sendData['Okurl'] = Mobilians::encrypt($sendData['Okurl'], $sendData['Tradeid']);
	$sendData['Failurl'] = Mobilians::encrypt($sendData['Failurl'], $sendData['Tradeid']);
	$sendData['Notiurl'] = Mobilians::encrypt($sendData['Notiurl'], $sendData['Tradeid']);
	$sendData['Prdtprice'] = Mobilians::encrypt($sendData['Prdtprice'], $sendData['Tradeid']);
	$sendData['Cryptstring'] = Mobilians::encrypt($sendData['Prdtprice'].$sendData['Okurl'], $sendData['Tradeid']);
}

?>
<!--  �������� ������û ������ -->
<html>
	<head>
		<!--
			/*****************************************************************************************
			 ������������ �Ʒ� js ������ �ݵ�� include
			 �� ����ȯ�� ������ ������� ����ڿ� ���� ���
			*****************************************************************************************/
		-->
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_CFG['global']['charset']; ?>" />
		<script src="https://mcash.mobilians.co.kr/js/ext/ext_inc_comm.js"></script>
		<script type="text/javascript" charset="<?php echo $_CFG['global']['charset']; ?>">
			window.onload = function()
			{
				if (MobiliansPaymentForm.Prdtprice.value === "<?php echo $sendData['Prdtprice']; ?>") {
					MCASH_PAYMENT(MobiliansPaymentForm);
				}
				else {
					alert("�����ݾ��� �ùٸ��� �ʽ��ϴ�.");
				}
			};
		</script>
	</head>

	<body>
		<form name="MobiliansPaymentForm" accept-charset="<?php echo $_CFG['global']['charset']; ?>">
			<?php foreach ($sendData as $name => $value) { ?>
			<input type="hidden" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
			<?php } ?>
		</form>
	</body>
</html>
