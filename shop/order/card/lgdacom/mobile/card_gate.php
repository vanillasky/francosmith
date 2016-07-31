<?php
/**
 * LG ���÷��� PG ���
 * ���� ���ϸ� payreq_crossplatform.php
 * LG ���÷��� PG ���� : LG U+ ǥ�ذ���â 2.5 - SmartXPay(V1.2 - 20141212)
 * @author artherot @ godosoft development team.
 */

// �⺻ ���� ����
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg_mobile.lgdacom.php";

// LG���÷��� ���̵� ó��
if (empty($pg_mobile['serviceType'])) {
	$pg_mobile['serviceType']	= 'service';
}
if ($pg_mobile['serviceType'] == 'test') {
	$LGD_MID	= 't'.$pg_mobile['id'];
} else {
	$LGD_MID	= $pg_mobile['id'];
}

// ��ǰ�� ó��
if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
	$item	= $cart -> item;
}
foreach($item as $v){
	$i++;
	if($i == 1) $ordnm	= $v['goodsnm'];
}

//��ǰ�� Ư������ �� �±� ����
$ordnm		= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " ��".($i-1)."��";

// ������ ���� (Y:1 / N:0)
if ($pg_mobile['zerofee'] == 'yes') {
	$pg_mobile['zerofee']	= '1';
} else {
	$pg_mobile['zerofee']	= '0';
}

// ������ �Һ� ����
if ($pg_mobile['zerofee'] == '0') {
	$pg_mobile['zerofee_period']	= '';
}

// �������� ����
$arrSettlekind	=array(
	'c'	=> 'SC0010',
	'o'	=> 'SC0030',
	'v'	=> 'SC0040',
	'h'	=> 'SC0060',
);

/*
 *************************************************
 * 1. �⺻���� ������û ���� ����
 *************************************************
 */

$configPath								= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/conf/lgdacom_mobile';		// LG���÷������� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.
$lguplusReturnUrl						= ProtocolPortDomain().$cfg['rootDir'].'/order/card/lgdacom';			// LG���÷��� ���� URL ����
$payReqMap								= array();
$payReqMap['CST_PLATFORM']				= $pg_mobile['serviceType'];					// LG���÷��� ���� ���� ����(test:�׽�Ʈ, service:����)
$payReqMap['CST_WINDOW_TYPE']			= 'submit';										// �����Ұ� (�������, ������ ��ȯ ���)
$payReqMap['CST_MID']					= $pg_mobile['id'];								// �������̵�(LG���÷������� ���� �߱޹����� �������̵� �Է��ϼ��� - �׽�Ʈ ���̵�� 't'�� �ݵ�� �����ϰ� �Է��ϼ���.)
$payReqMap['LGD_MID']					= $LGD_MID;										// �������̵�(�ڵ����� - �׽�Ʈ �ΰ�� �ڵ����� �տ� t�� ����)
$payReqMap['LGD_OID']					= $_POST['ordno'];								// �ֹ���ȣ
$payReqMap['LGD_AMOUNT']				= $_POST['settleprice'];						// �����ݾ�("," �� ������ �����ݾ��� �Է��ϼ���)
$payReqMap['LGD_BUYER']					= $_POST['nameOrder'];							// �����ڸ�
$payReqMap['LGD_PRODUCTINFO']			= $ordnm;										// ��ǰ��
$payReqMap['LGD_BUYEREMAIL']			= $_POST['email'];								// ������ �̸���
$payReqMap['LGD_CUSTOM_SKIN']			= 'SMART_XPAY2';								// �������� ����â ��Ų
$payReqMap['LGD_CUSTOM_PROCESSTYPE']	= 'TWOTR';										// Ʈ����� ó����� (TWOTR : ���� ��� ���� �帧, ONETR : �񵿱� ��� ���� �帧)
$payReqMap['LGD_TIMESTAMP']				= date(YmdHms);									// Ÿ�ӽ�����
$payReqMap['LGD_VERSION']				= 'PHP_SmartXPay_1.0';							// �������� (�������� ������)
$payReqMap['LGD_CUSTOM_FIRSTPAY']		= $arrSettlekind[$_POST['settlekind']];			// �������� �ʱ��������
$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']	= strtoupper($payReqMap['CST_WINDOW_TYPE']);	// �ſ�ī�� ī��� ���� ������ ���� ���

if( $_POST['settlekind'] == 'c') {
	$payReqMap['LGD_INSTALLRANGE']		= $pg_mobile['quota'];							// �Һΰ��� ����
	$payReqMap['LGD_NOINTINF']			= $pg_mobile['zerofee_period'];					// ������ �Һ�(������ �����δ�) ���� : Ư��ī��/Ư�����������ڼ���
}

if( $_POST['settlekind'] == 'o' || $_POST['settlekind'] == 'v' ) {
	$payReqMap['LGD_CASHRECEIPTYN']		= $pg_mobile['receipt'];						// ���ݿ����� �̻�뿩��(Y:�̻��,N:���)
}

$payReqMap['LGD_ESCROW_USEYN']			= $_POST['escrow'];								// ����ũ�� ���� : ����(Y),������(N)
if ($payReqMap['LGD_ESCROW_USEYN'] == 'Y') {
	foreach($cart->item as $row) {
		$payReqMap['LGD_ESCROW_GOODID']		= $row['goodsno'];							// ����ũ�λ�ǰ��ȣ
		$payReqMap['LGD_ESCROW_GOODNAME']	= $row['goodsnm'];							// ����ũ�λ�ǰ��
		$payReqMap['LGD_ESCROW_GOODCODE']	= $_POST['escrow'];							// ����ũ�λ�ǰ�ڵ�
		$payReqMap['LGD_ESCROW_UNITPRICE']	= ($row['price']+$row['addprice']);			// ����ũ�λ�ǰ����
		$payReqMap['LGD_ESCROW_QUANTITY']	= $row['ea'];								// ����ũ�λ�ǰ����
	}
	if($_POST['zonecode']){
		$payReqMap['LGD_ESCROW_ZIPCODE']		= $_POST['zonecode'];					// ����ũ�ι����������ȣ (�������ȣ)
		$payReqMap['LGD_ESCROW_ADDRESS1']		= $_POST['road_address'];				// ����ũ�ι�����ּҵ����� (���θ��ּ�)
	}
	else {
		$payReqMap['LGD_ESCROW_ZIPCODE']		= implode('-',$_POST['zipcode']);			// ����ũ�ι���������ȣ
		$payReqMap['LGD_ESCROW_ADDRESS1']		= $_POST['address'];						// ����ũ�ι�����ּҵ�����
	}
	$payReqMap['LGD_ESCROW_ADDRESS2']		= $_POST['address_sub'];					// ����ũ�ι�����ּһ�
	$payReqMap['LGD_ESCROW_BUYERPHONE']		= implode('-',$_POST['mobileOrder']);		// ����ũ�α������޴�����ȣ
}

/*
 *************************************************
 * 2. MD5 �ؽ���ȣȭ (�������� ������)
 *
 * MD5 �ؽ���ȣȭ�� �ŷ� �������� �������� ����Դϴ�.
 *************************************************
 *
 * �ؽ� ��ȣȭ ����( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
 * LGD_MID          : �������̵�
 * LGD_OID          : �ֹ���ȣ
 * LGD_AMOUNT       : �ݾ�
 * LGD_TIMESTAMP    : Ÿ�ӽ�����
 * LGD_MERTKEY      : ����MertKey (mertkey�� ���������� -> ������� -> ���������������� Ȯ���ϽǼ� �ֽ��ϴ�)
 *
 * MD5 �ؽ������� ��ȣȭ ������ ����
 * LG���÷������� �߱��� ����Ű(MertKey)�� ȯ�漳�� ����(lgdacom/conf/mall.conf)�� �ݵ�� �Է��Ͽ� �ֽñ� �ٶ��ϴ�.
 */
require_once(dirname(__FILE__)."/XPayClient.php");
$xpay	= &new XPayClient($configPath, $payReqMap['CST_PLATFORM']);
$xpay->Init_TX($payReqMap['LGD_MID']);
$payReqMap['LGD_HASHDATA']				= md5($payReqMap['LGD_MID'].$payReqMap['LGD_OID'].$payReqMap['LGD_AMOUNT'].$payReqMap['LGD_TIMESTAMP'].$xpay->config[$payReqMap['LGD_MID']]);	// MD5 �ؽ���ȣ��

/*
 *************************************************
 * 3. ��� ����
 *************************************************
 */
if( $_POST['settlekind'] == 'v'){
	// �������(������) ���������� �Ͻô� ���  �Ҵ�/�Ա� ����� �뺸�ޱ� ���� �ݵ�� LGD_CASNOTEURL ������ LG ���÷����� �����ؾ� �մϴ� .
	$payReqMap['LGD_CASNOTEURL']		= $lguplusReturnUrl.'/cas_noteurl.php?isMobile=Y';		// ������� NOTEURL
}

// LGD_RETURNURL �� �����Ͽ� �ֽñ� �ٶ��ϴ�. �ݵ�� ���� �������� ������ ����Ʈ�� ��  ȣ��Ʈ�̾�� �մϴ�. �Ʒ� �κ��� �ݵ�� �����Ͻʽÿ�.
$payReqMap['LGD_RETURNURL']				= $lguplusReturnUrl.'/mobile/card_return.php';			// �������������

/*
 ****************************************************
 * 4. �ȵ���̵��� �ſ�ī�� ISP(����/BC)�������� ����
 ****************************************************

(����)LGD_CUSTOM_ROLLBACK �� ����  "Y"�� �ѱ� ���, LG U+ ���ڰ������� ���� ISP(����/��) ���������� �������� note_url���� ���Ž�  "OK" ������ �ȵǸ�  �ش� Ʈ�������  ������ �ѹ�(�ڵ����)ó���ǰ�,
LGD_CUSTOM_ROLLBACK �� �� �� "C"�� �ѱ� ���, �������� note_url���� "ROLLBACK" ������ �� ���� �ش� Ʈ�������  �ѹ�ó���Ǹ�  �׿��� ���� ���ϵǸ� ���� ���οϷ� ó���˴ϴ�.
����, LGD_CUSTOM_ROLLBACK �� ���� "N" �̰ų� null �� ���, �������� note_url����  "OK" ������  �ȵɽ�, "OK" ������ �� ������ 3�а������� 2�ð�����  ���ΰ���� �������մϴ�.
*/
$payReqMap['LGD_CUSTOM_ROLLBACK']		= 'C';						// �񵿱� ISP���� Ʈ����� ó������

// ISP ī����� ������ �����ISP���(�������� ���������ʴ� �񵿱���)�� ���, LGD_KVPMISPNOTEURL/LGD_KVPMISPWAPURL/LGD_KVPMISPCANCELURL�� �����Ͽ� �ֽñ� �ٶ��ϴ�.
$payReqMap['LGD_KVPMISPNOTEURL']       	= $lguplusReturnUrl.'/mobile/card_return.php?isAsync=Y';								// �񵿱� ISP(ex. �ȵ���̵�) ���ΰ���� �޴� URL
$payReqMap['LGD_KVPMISPWAPURL']			= $lguplusReturnUrl.'/mobile/mispwapurl.php?LGD_OID='.$payReqMap['LGD_OID'];			// �񵿱� ISP(ex. �ȵ���̵�) ���οϷ��� ����ڿ��� �������� ���οϷ� URL - ISP ī�� ������, URL ��� �۸� �Է½�, ��ȣ����
$payReqMap['LGD_KVPMISPCANCELURL']     	= $lguplusReturnUrl.'/mobile/Cancel.php?isAsync=Y';										// ISP �ۿ��� ��ҽ� ����ڿ��� �������� ��� URL

// �ȵ���̵� ���� �ſ�ī�� ����  ISP(����/BC)�������� ���� (����)
$payReqMap['LGD_KVPMISPAUTOAPPYN']		= 'N';						// Y: �ȵ���̵忡�� ISP�ſ�ī�� ������, ���翡�� 'App To App' ������� ����, BCī��翡�� ���� ���� ������ �ް� ������ ���� �����ϰ��� �Ҷ� ���

// Return URL���� ���� ��� ���� �� ���õ� �Ķ���� �Դϴ�.*/
$payReqMap['LGD_RESPCODE']				= '';
$payReqMap['LGD_RESPMSG']				= '';
$payReqMap['LGD_PAYKEY']				= '';

// ó�� ���������� ��ȿ�� üũ�� ���� ��� ������ ���ǿ� ����
$_SESSION['PAYREQ_MAP']					= $payReqMap;

// ���ȼ��� ��뿩�ο� ���� LG U+ ���� ��ũ��Ʈ �ּ� ����
$xpay_uplus_script_url	= 'xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js';
if ($_SERVER['HTTPS'] == 'on') {
	$xpay_uplus_script_url	= 'https://' . $xpay_uplus_script_url;
} else {
	$xpay_uplus_script_url	= 'http://' . $xpay_uplus_script_url;
}
?>
<script language="javascript" src="<?php echo $xpay_uplus_script_url;?>" type="text/javascript"></script>
<script type="text/javascript">
/*
* �����Ұ�
*/
var LGD_window_type	= '<?php echo $payReqMap['CST_WINDOW_TYPE'];?>';

/*
* �����Ұ�
*/
function launchCrossPlatform(){
      lgdwin = open_paymentwindow(document.getElementById('LGD_PAYINFO'), '<?php echo $payReqMap['CST_PLATFORM'];?>', LGD_window_type);
}
/*
* FORM ��  ���� ����
*/
function getFormObject() {
        return document.getElementById('LGD_PAYINFO');
}
</script>
<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" action="">
<?php
foreach ($payReqMap as $key => $value) {
	echo '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />'.chr(10);
}
?>
<input type="hidden" name="LGD_TAXFREEAMOUNT" id="LGD_TAXFREEAMOUNT" />
</form>