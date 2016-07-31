<?
/*
������ �����Ϸ� �뺸
*/
include "../../../lib/library.php";

function response_log($msg)
{
	global $paycoApi;

	$paycoApi->receive_log($msg, 'card_return');
	exit($msg);
}

function arr_data_iconv($b)
{
	$iconv_data = array();

	foreach($b as $k => $v) {
		if(is_array($v)) $iconv_data[$k] = arr_data_iconv($v);
		else $iconv_data[$k] = iconv('utf-8', 'euc-kr', $v);
	}
	return $iconv_data;
}

/*
	gd_order, gd_order_item ������ ����
*/
function order_data_restoration()
{
	global $bankup_gd_order_data, $backup_gd_order_item_data, $gd_order_ins, $arr_gd_order_item, $db;

	foreach($gd_order_ins as $key => $val) {
		$upd_data[$key] = $bankup_gd_order_data[$key];
	}

	$query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i]', $upd_data, $backup_gd_order_data['ordno']);
	$rtns[] = $db->_query($query);

	foreach($backup_gd_order_item_data as $b_item) {
		$item_upd_data['istep'] = $b_item['istep'];
		$item_upd_data['cyn'] = $b_item['cyn'];
		$item_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE sno=[i]', $item_upd_data, $b_item['sno']);
		$rtns[] = $db->_query($item_query);
	}

	foreach($rtns as $rtn) if(!$rtn) return false;
	return true;
}


$arr_data = $_POST;

//response data ��ȿ�� üũ
if(empty($arr_data)) response_log('���۵� �����Ͱ� �����ϴ�.');

//������ Ŭ����
$paycoApi = &load_class('paycoApi','paycoApi');
$payco = &load_class('payco','payco');

// ���ŵ����� �α� ����
$paycoApi->receive_log($arr_data, 'card_return');

// ���� ������ ���θ� üũ �� ������ ��ȣȭ
$settle_data = $paycoApi->shop_check($arr_data);
if($settle_data === false) {
	response_log('���θ� �������� �ٸ��ϴ�.');
}

// ���ŵ����� iconv
$settle_data = arr_data_iconv($settle_data);

// ������ ����
$item_data = $settle_data['orderProducts'];
$delivery_data = $settle_data['deliveryPlace'];
$payment_data = $settle_data['paymentDetails'];

unset($settle_data['orderProducts'], $settle_data['deliveryPlace'], $settle_data['paymentDetails'], $settle_data['paymentAdmission']);
/*
	2015-01-14 $settle_data['paymentAdmission'] �� �����ڿ��� ������ ���޽� ��������
*/

$order_data = $settle_data;

/*	$order_data Array
	sellerOrderReferenceKey			���θ� �ֹ���ȣ
	reserveOrderNo					������ �ֹ����� ��ȣ
	orderNo							������ �ֹ���ȣ
	memberName						�����ڸ�
	memberEmail						������ �̸���
	orderChannel					�ֹ�ä�� (PC or MOBILE)
	totalOrderAmt					�� �ֹ��ݾ�
	totalDeliveryFeeAmt				�� ��ۺ�
	totalRemoteAreaDeliveryFeeAmt	�� �����갣��
	totalPaymentAmt					�� �����ݾ�
	serviceUrlParam					�̻��
	paymentCompletionYn				�����ϷῩ�� (Y or N)
	orderMethod						�ֹ�����(CHECKOUT or EASYPAY_F or EASYPAY)
*/

/*	$item_data Array	2���迭
	orderProductNo					������ �ֹ���ǰ��ȣ
	sellerOrderProductReferenceKey	���θ� item sno
	orderProductStatusCode			�ֹ���ǰ�����ڵ� (OPSPAED
	orderProductStatusName			�ֹ���ǰ���¸� (�����Ϸ�
	cpId							����ID
	productId						��ǰID
*/

/*	$delivery_data Array
	recipient				�����θ�
	englishReceipent		
	address1				������ �ּ�1
	address2				������ �ּ�2
	zipcode					�����ȣ
	deliveryMemo			��ۿ�û����
	telephone				������ �ڵ�����ȣ
	individualCustomUniqNo	���������ȣ
*/

/*	$payment_data
	paymentTradeNo		������ȣ
	paymentMethodCode	���������ڵ� (01)
	paymentMethodName	�������ܸ� (�ſ�ī��)
	paymentAmt			�����ݾ�
	tradeYmdt			�����Ͻ� (20150113201530)
	pgAdmissionNo		PG���ι�ȣ (20150113950567)
	pgAdmissionYmdt		PG�����Ͻ� (20150113201530)
	easyPaymentYn		����������� (Y or N)

	�������ܿ� ���� �Ʒ����� �߰���

	�ſ�ī�� (��Ž� ���� ���� ������)
	cardSettleInfo	Array
		cardCompanyName				ī���� (����ī��)
		cardNo						ī���ȣ (************6336)
		cardInstallmentMonthNumber	00

	�޴��� (���幮���� ǥ��Ǿ� �ִ� ��)
	cellphoneSettleInfo	Array
		companyName				��Ż��
		celphoneNo				�޴�����ȣ

	�ǽð� ������ü (���幮���� ǥ��Ǿ� �ִ� ��)
	realtimeAccountTransferSettleInfo	Array
		bankName				�����
		bankCode				�����ڵ�

	������� (���幮���� ǥ��Ǿ� �ִ� ��)
	nonBankbookSettleInfo	Array
		bankName				�����
		bankCode				�����ڵ�
		accountNo				���¹�ȣ
		paymentExpirationYmd	�Աݸ�����

*/



$query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $order_data['sellerOrderReferenceKey']);
$gd_order_data = $db->_select($query);

// gd_order ������ ���
$bankup_gd_order_data = $gd_order_data[0];

// �ֹ���ȿ�� üũ
if(count($gd_order_data) < 1) response_log('��ϵ� �ֹ��� �����ϴ�.');

// ������ ��� ���� �� ��ȿ�� üũ (true - ��밡��, false - ���Ұ�)
$useEmoney = false;
if ($order_data['orderMethod'] != 'CHECKOUT' && $bankup_gd_order_data['emoney'] > 0 && $bankup_gd_order_data['m_no']){
	$useEmoney = true;

	//������� �Ա�Ȯ���� �̻�� �� üũX
	foreach($payment_data as $payment) {
		if($payment['paymentMethodCode'] == '02' && $order_data['paymentCompletionYn'] == 'Y') {
			$useEmoney = false;
			break;
		}
	}
}

if ($useEmoney === true){
	//������ ��ȿ�� üũ
	if($payco->checkEmoney($bankup_gd_order_data) == false){
		response_log('����� �������� ������ �����ݺ��� �����ϴ�.');
	}
}

if($order_data['orderMethod'] == '') $order_data['orderMethod'] = strtoupper($bankup_gd_order_data['payco_settle_type']);

if($bankup_gd_order_data['step'] > 0) response_log('�̹� �Ա�Ȯ�ε� �ֹ��Դϴ�');

### ������ ��ۺ�
if($settle_data['totalRemoteAreaDeliveryFeeAmt'] > 0) $add_delivery_bool = true;
$total_area_delivery = 0;

### item�� ��ȿ�� üũ
foreach($item_data as $item) {
	$item_query = $db->_query_print('SELECT * from '.GD_ORDER_ITEM.' WHERE sno=[i]', $item['sellerOrderProductReferenceKey']);
	$item_res = $db->_select($item_query);

	if(!empty($item_res)) {
		$order_item = $item_res[0];

		// gd_order_item ������ ���
		$backup_gd_order_item_data[] = $order_item;

		($order_data['orderChannel'] == 'PC') ? $isMobile = false : $isMobile = true;

		if(!$cfg) include "../../../conf/config.php";

		/*
		 * �����Ϸ��� ���
		 * A. ���谨�ܰ谡 [�ֹ�������]�� ��� �ֹ����ɿ��� üũ ����
		 * B. ���谨���谡 [�Ա�Ȯ�ν�]�� ��� ���Ǻ� �ֹ����ɿ��� üũ ����
		 *  B-1. ������� �ֹ��� ��� �ֹ����ɿ��� üũ ����(������� + ������ ����Ʈ ����)
		 *  B-2. ������°� �ƴ� �ֹ��� ��� �ֹ����ɿ��� üũ ����
		*/
		if($order_data['paymentCompletionYn'] === "Y") {
			if($cfg['stepStock'] === "0") $order_check_bool = false;//A.
			else {//B.
				//�������� Ȯ��
				foreach($payment_data as $tmp_payment) {
					if($tmp_payment['paymentMethodCode'] === "02") {//B-1.
						$order_check_bool = false;
						break;
					}
					else $order_check_bool = true;//B-2.
				}
			}
		}
		else $order_check_bool = false;


		// �ֹ����ɿ��� üũ
		if($order_check_bool === true) {
			$order_check = $payco->check_paycoOrderAbleComplet($order_data['orderMethod'], $order_item['goodsno'], $isMobile);

			if($order_check !== false) {
				exit($order_check.' goodsno('.$order_item['goodsno'].')');
			}
		}

		// gd_order_item update ������ ����
		if($order_data['paymentCompletionYn'] == 'Y') {
			$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
			$tmp_order_item['istep'] = '1';
			$tmp_order_item['cyn'] = 'y';
		}
		else {
			$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
			$tmp_order_item['istep'] = '0';
		}
		$arr_gd_order_item[] = $tmp_order_item;
	}
	else {
		$total_area_delivery = $item['productPaymentAmt'] - $item['originalProductPaymentAmt'];
	}
}

// �� ������ ��ۺ�� ��ۺ�item�� ���Ե� ������ ��ۺ� �ݾ��� �ٸ� ��� ����ó��
if($settle_data['totalRemoteAreaDeliveryFeeAmt'] != $total_area_delivery) {
	response_log('�� ������ ��ۺ�� ��ۺ�ITEM�� ���Ե� ������ ��ۺ� �ٸ��ϴ�.[total:'.$settle_data['totalRemoteAreaDeliveryFeeAmt'].'/item'.$total_area_delivery.']');
}

// ������ ��ۺ� �ִ� ��� �����ݾ� �� ��ۺ� ����
if($add_delivery_bool === true && $total_area_delivery > 0) {
	$arr_settle_log[] = $settle_data['sellerOrderReferenceKey'].' ('.date('Y-m-d H:i:s').')';
	$arr_settle_log[] = '-----------------------------------';
	$arr_settle_log[] = '������ ��ۺ� �߰� : '.number_format($total_area_delivery).'��';
	$arr_settle_log[] = '-----------------------------------';
	$arr_settle_log[] = '';

	if($order_data['totalPaymentAmt'] != $bankup_gd_order_data['settleprice']) {
		$add_query = $db->_query_print('UPDATE '.GD_ORDER.' SET settleprice=(settleprice + [i]), prn_settleprice=(prn_settleprice + [i]), delivery=(delivery + [i]) WHERE ordno=[i]', $total_area_delivery, $total_area_delivery, $total_area_delivery, $order_data['sellerOrderReferenceKey']);
		$rtn = $db->_query($add_query);
	}
}

// ���ݺ���üũ
if(forge_order_check($order_data['sellerOrderReferenceKey'], $order_data['totalPaymentAmt']) !== true) response_log('�����ݾ��� �߸��Ǿ����ϴ�. order_price('.$bankup_gd_order_data['settleprice'].') and settle_price('.$order_data['totalPaymentAmt'].')');


// ��������� ����
if($order_data['orderMethod'] == 'CHECKOUT') {
	/* �ּ��׸��� ������ ���� */
	$gd_order_ins['nameOrder'] = $order_data['memberName'];	//�ֹ��ڸ�
	$gd_order_ins['email'] = $order_data['memberEmail'];	//�ֹ��� �̸���
//	$gd_order_ins['phoneOrder'] = '';	//�ֹ��� ������ȭ��ȣ
//	$gd_order_ins['mobileOrder'] = '';	//�ֹ��� �޴��� ��ȣ

	$gd_order_ins['nameReceiver'] = $delivery_data['recipient'];	//�����ڸ�
	$gd_order_ins['phoneReceiver'] = $delivery_data['telephone'];	//������ ������ȭ��ȣ
	$gd_order_ins['mobileReceiver'] = $delivery_data['telephone'];	//������ �޴�����ȣ

	$gd_order_ins['zipcode'] = $delivery_data['zipcode'];	//�����ȣ
	$gd_order_ins['address'] = $delivery_data['address1'].' '.$delivery_data['address2'];	//������ּ�
//	$gd_order_ins['road_address'] = '';	//���θ��ּ�
	### address1�� �����ּ� �Ǵ� ���θ��ּҰ� ���޵Ǹ� ������ ���θ��� �����ϴ� ���� ����

	$gd_order_ins['memo'] = $delivery_data['deliveryMemo'];	//��ۿ�û����
}
else {
	//��������� gd_order_item update ������ ����
	if($order_data['paymentCompletionYn'] == 'Y') {
		$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
		$tmp_order_item['istep'] = '1';
		$tmp_order_item['cyn'] = 'y';
	}
	else {
		$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
		$tmp_order_item['istep'] = '0';
	}
	$arr_gd_order_item[] = $tmp_order_item;
}

$arr_settle_log[] = '-----------------------------------';
$arr_settle_log[] = '������ �ֹ���ȣ : '.$order_data['orderNo'];
$arr_settle_log[] = '-----------------------------------';
$arr_settle_log[] = '';

foreach($payment_data as $payment) {
	$arr_settle_log[] = $order_data['sellerOrderReferenceKey'].' ('.date('Y-m-d H:i:s').')';
	$arr_settle_log[] = '-----------------------------------';
	$arr_settle_log[] = '������ȣ : '.$payment['paymentTradeNo'];
	$arr_settle_log[] = '���������ڵ� : '.$payment['paymentMethodCode'];
	$arr_settle_log[] = '�������ܸ� : '.$payment['paymentMethodName'];
	$arr_settle_log[] = '�����ݾ� : '.$payment['paymentAmt'];
	$arr_settle_log[] = '�����Ͻ� : '.$payment['tradeYmdt'];
	$arr_settle_log[] = 'PG���ι�ȣ : '.$payment['pgAdmissionNo'];
	$arr_settle_log[] = 'PG�����Ͻ� : '.$payment['pgAdmissionYmdt'];
	$arr_settle_log[] = '����������� : '.$payment['easyPaymentYn'];
	$arr_settle_log[] = '-------------�󼼷α�--------------';

	$cdt_ymd[] = substr($payment['tradeYmdt'], 0, 4);
	$cdt_ymd[] = substr($payment['tradeYmdt'], 4, 2);
	$cdt_ymd[] = substr($payment['tradeYmdt'], 6, 2);
	$cdt_time[] = substr($payment['tradeYmdt'], 8, 2);
	$cdt_time[] = substr($payment['tradeYmdt'], 10, 2);
	$cdt_time[] = substr($payment['tradeYmdt'], 12, 2);

	$gd_order_ins['cdt'] = implode('-', $cdt_ymd).' '.implode(':', $cdt_time);
	unset($cdt_ymd, $cdt_time);


/*
	���θ� �������� �ڵ�
		a	������
		c	�ſ�ī��
		o	������ü
		v	�������
		d	��������
		h	�ڵ���
		p	����Ʈ
		u	�ſ�ī�� (�߱�)
		y	��������

		e	������ ����Ʈ

	������ �������� �ڵ�
		01	�ſ�ī��
		02	�������
		04	�ǽð� ������ü
		05	�޴���
		31	�ſ�ī�� �������
		60	�޴��� �������
		98	������ ����Ʈ
*/
	### �������ܺ� �߰����� ���
	switch($payment['paymentMethodCode']) {
		case '01' ://�ſ�ī��
		case '31' ://�ſ�ī�� �������
			$settle_method_data = $payment['cardSettleInfo'];
			$arr_settle_log[] = 'ī���� : '.$settle_method_data['cardCompanyName'];
			$arr_settle_log[] = 'ī����ڵ� : '.$settle_method_data['cardCompanyCode'];
			$arr_settle_log[] = 'ī���ȣ : '.$settle_method_data['cardNo'];
			$arr_settle_log[] = '�Һΰ��� : '.$settle_method_data['cardInstallmentMonthNumber'];
			$arr_settle_log[] = 'ī��� ���ι�ȣ : '.$settle_method_data['cardAdmissionNo'];

			$gd_order_ins['settlekind'] = 'c';
			break;
		
		case '02' ://�������
			$settle_method_data = $payment['nonBankbookSettleInfo'];
			$arr_settle_log[] = '����� : '.$settle_method_data['bankName'];
			$arr_settle_log[] = '�����ڵ� : '.$settle_method_data['bankCode'];
			$arr_settle_log[] = '���¹�ȣ : '.$settle_method_data['accountNo'];
			$arr_settle_log[] = '�Աݸ����� : '.substr($settle_method_data['paymentExpirationYmd'], 0, 4).'-'.substr($settle_method_data['paymentExpirationYmd'], 4,2).'-'.substr($settle_method_data['paymentExpirationYmd'], 6,2);

			$gd_order_ins['settlekind'] = 'v';
			$gd_order_ins['vAccount'] = $settle_method_data['bankName'].' '.$settle_method_data['accountNo'];
			$gd_order_ins['vAccount'] .= ' '.substr($settle_method_data['paymentExpirationYmd'], 0, 4).'-'.substr($settle_method_data['paymentExpirationYmd'], 4,2).'-'.substr($settle_method_data['paymentExpirationYmd'], 6,2);
			break;

		case '35' ://�ٷ���ü
			$settle_method_data = $payment['realtimeAccountTransferSettleInfo'];
			$arr_settle_log[] = '����� : '.$settle_method_data['bankName'];
			$arr_settle_log[] = '�����ڵ� : '.$settle_method_data['bankCode'];

			$gd_order_ins['settlekind'] = 'o';
			break;

		case '05' ://�޴���
		case '60' ://�޴��� �������
			$settle_method_data = $payment['cellphoneSettleInfo'];
			$arr_settle_log[] = '��Ż�� : '.$settle_method_data['companyName'];
			$arr_settle_log[] = '�޴�����ȣ : '.$settle_method_data['cellphoneNo'];

			$gd_order_ins['settlekind'] = 'h';
			break;

		case '98' ://������ ����Ʈ
			if(!isset($gd_order_ins['settlekind'])) $gd_order_ins['settlekind'] = 'e';
			$gd_order_ins['payco_use_point'] = $payment['paymentAmt'];//������ ����Ʈ ��� �ݾ�

			$arr_settle_log[] = '����� ������ ����Ʈ : '.$payment['paymentAmt'];

			// �ٸ� �������ܰ� ���� ����ϴ� ��� �ش� ������������ ���������ϱ� ����
			if(empty($gd_order_ins['settlekind'])) $gd_order_ins['settlekind'] = 'e';
			break;
		case '75' ://������ ����(�����̿�����)
		case '76' ://ī�� ����
		case '77' ://������ ����
			$gd_order_ins['payco_coupon_use_yn'] = 'Y';
			$gd_order_ins['payco_coupon_price'] = $payment['paymentAmt'];
			$arr_settle_log[] = '����� �����ݾ� : '.$payment['paymentAmt'];
			break;
		default :
			break;
	}
	$arr_settle_log[] = '�����ϷῩ�� : '.$order_data['paymentCompletionYn'];
	$arr_settle_log[] = '-----------------------------------';
}

if($order_data['paymentCompletionYn'] == 'Y') {
	$gd_order_ins['cyn'] = 'y';
	$gd_order_ins['step'] = '1';
	$gd_order_ins['step2'] = '0';
}
else {
	$gd_order_ins['step'] = '0';
	$gd_order_ins['step2'] = '0';
}

$gd_order_ins['settlelog'] = implode("\n", $arr_settle_log);
$gd_order_ins['sync_'] = '0';
$gd_order_ins['payco_order_no'] = $order_data['orderNo'];//������ �ֹ���ȣ

// �������� ���� �� �ֹ����� ����
if(empty($gd_order_ins) === false && empty($arr_gd_order_item) === false) {
	$order_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i]', $gd_order_ins, $order_data['sellerOrderReferenceKey']);

	$order_rtn = $db->_query($order_query);
	if(!$order_rtn) {
		response_log('�������� DB���� ����');
	}

	foreach($arr_gd_order_item as $gd_order_item) {
		if($gd_order_item['sno'] == 'item_'.$order_data['sellerOrderReferenceKey']) {
			$query = $db->_query_print('SELECT sno FROM '.GD_ORDER_ITEM.' WHERE ordno=[i]', $order_data['sellerOrderReferenceKey']);
			$arr_items = $db->_select($query);
		}
		else {
			$arr_items = $arr_gd_order_item;
		}

		foreach($arr_items as $_item) {
			$upd_item['istep'] = $gd_order_item['istep'];
			if(isset($gd_order_item['cyn'])) $upd_item['cyn'] = $gd_order_item['cyn'];

			$item_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE sno=[i]', $upd_item, $_item['sno']);
			$item_rtn = $db->_query($item_query);

			if(!$item_rtn) {
				if(order_data_restoration()) response_log('�������� DB���� ����');
				else response_log('�������� DB���� ���� (������ ��������)');
			}
		}
	}
}
else {
	response_log('�������� ���嵥���� ���� ����');
}

$query = "
SELECT * from
	".GD_ORDER." a
	LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
WHERE
	a.ordno='".$order_data['sellerOrderReferenceKey']."'
";
$data = $db->fetch($query);

// ��ǰ��� ����
$payco->adjustStock($order_data['sellerOrderReferenceKey']);

// �ֹ��α� ����
if($gd_order_ins['step'] > 0) orderLog($order_data['sellerOrderReferenceKey'], $r_step[$bankup_gd_order_data['step']]." > ".$r_step[$gd_order_ins['step']]);

// ��ǰ���Խ� ������ ���
if ($useEmoney === true){
	setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���", $order_data['sellerOrderReferenceKey']);
}

### �ֹ�Ȯ�θ���
if(function_exists('getMailOrderData')) {
	sendMailCase($order_data['memberEmail'],0,getMailOrderData($order_data['sellerOrderReferenceKey']));
}

// SMS ���� ����
$dataSms = $data;
if ($gd_order_ins['settlekind'] != "v") {
	sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
	sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
} else {
	sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
}


// ���Ű�� �α�/���
response_log('ok');
?>