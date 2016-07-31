<?
/*
 * ���̹� üũ�ƿ� API 4.0 ����
 */

$cfg['orderPeriod'] = $cfg['orderPeriod'] ? $cfg['orderPeriod'] : 30;

$__navercheckout_message_schema = array();

// ��������
$__navercheckout_message_schema['payMeansClassType'] = array(
	'�ſ�ī��' => '�ſ�ī��',
	'�ǽð�������ü' => '�ǽð�������ü',
	'�������Ա�' => '�������Ա�',
	'�޴���' => '�޴���',
	'����Ʈ����' => '����Ʈ����',
	'���̹� ĳ��' => '���̹� ĳ��',
	'�ſ�ī�� �������' => '�ſ�ī�� �������',
	'�޴��� �������' => '�޴��� �������',
	'���� �������' => '���� �������'
);

// ���/��ǰ ���� �ڵ�
$__navercheckout_message_schema['claimRequestReasonType'] = array(
	'INTENT_CHANGED' => '���� �ǻ� ���',
	'COLOR_AND_SIZE' => '���� �� ������ ����',
	'WRONG_ORDER' => '�ٸ� ��ǰ �߸� �ֹ�',
	'PRODUCT_UNSATISFIED' => '���� �� ��ǰ �Ҹ���',	// �Ǹ� ��ҽ� ��� (��ǰ���� ��밡��)
	'DELAYED_DELIVERY' => '��� ����',					// �Ǹ� ��ҽ� ��� (��ǰ���� ��밡��)
	'SOLD_OUT' => '��ǰ ǰ��',							// �Ǹ� ��ҽ� ��� (��ǰ���� ��밡��)
	'DROPPED_DELIVERY' => '��� ����',
	'BROKEN' => '��ǰ �ļ�',
	'INCORRECT_INFO' => '��ǰ ���� ����',
	'WRONG_DELIVERY' => '�����',
	'WRONG_OPTION' => '���� �� �ɼ��� �ٸ� ��ǰ �߸� ���',
	'ETC' => '��Ÿ',
);

// ��۹�� �ڵ�
$__navercheckout_message_schema['deliveryMethodType'] = array(
	/* �����/���� */
	'DELIVERY' => '�Ϲ� �ù�',
	'GDFW_ISSUE_SVC' => '�½��÷� ���� ���',
	'VISIT_RECEIPT' => '�湮 ����',
	'DIRECT_DELIVERY' => '���� ����',
	'QUICK_SVC' => '������',
	'NOTHING' => '��� ����',

	/* �ݼ��϶��� */
	'RETURN_DESIGNATED' => '���� ��ǰ �ù�',
	'RETURN_DELIVERY' => '�Ϲ� ��ǰ �ù�',
	'RETURN_INDIVIDUAL' => '���� �ݼ�',
);

// �ù�� �ڵ� (wdsl �� code Ÿ�� �ƴ�, ���̹��� Ȯ�� ��� ��� �߰��� ����� �־� string ���� �����صξ��ٰ� ��)
$__navercheckout_message_schema['deliveryCompanyType'] = array(
	'KOREX' => '�������',
	'CJGLS' => 'CJ�������',
	'KOREXG' => 'CJ�������(�����ù�)',
	'SAGAWA' => 'SC ������(�簡���ͽ��������ù�)',
	'YELLOW' => '���ο�ĸ(����)',
	'DHLDE' => 'DHL(����)',
	'KGB' => '�����ù�',
	'DONGBU' => 'KG������',
	'EPOST' => '��ü���ù�',
	'REGISTPOST' => '������',
	'HANJIN' => '�����ù�',
	'HYUNDAI' => '�����ù�',
	'KGBLS' => 'KGB �ù�',
	'HANARO' => '�ϳ����ù�',
	'INNOGIS' => 'GTX������',
	'DAESIN' => '����ù�',
	'ILYANG' => '�Ͼ������',
	'KDEXP' => '�浿�ù�',
	'CHUNIL' => 'õ���ù�',
	'DHL' => 'DHL',
	'FEDEX' => 'FEDEX',
	'GSMNTON' => 'GSMNTON',
	'WARPEX' => 'WarpEx',
	'WIZWA' => 'WIZWA',
	'EMS' => 'EMS',
	'ACIEXPRESS' => 'ACI',
	'EZUSA' => 'EZUSA',
	'PANTOS' => '�������佺',
	'SWGEXP' => '�����۷ι�',
	'TNT' => 'TNT',
	'UPS' => 'UPS',
	'CVSNET' => '�������ù�',
	'HDEXP' => '�յ��ù�',
	'CH1' => '��Ÿ �ù�',
);
$__navercheckout_message_schema['selectDeliveryCompanyType'] = array(
	'CJGLS' => 'CJ�������',
	'KOREXG' => 'CJ�������(�����ù�)',
	'DHLDE' => 'DHL(����)',
	'KGB' => '�����ù�',
	'DONGBU' => 'KG������',
	'EPOST' => '��ü���ù�',
	'REGISTPOST' => '������',
	'HANJIN' => '�����ù�',
	'HYUNDAI' => '�����ù�',
	'KGBLS' => 'KGB �ù�',
	'INNOGIS' => 'GTX������',
	'DAESIN' => '����ù�',
	'ILYANG' => '�Ͼ������',
	'KDEXP' => '�浿�ù�',
	'CHUNIL' => 'õ���ù�',
	'DHL' => 'DHL',
	'FEDEX' => 'FEDEX',
	'GSMNTON' => 'GSMNTON',
	'WARPEX' => 'WarpEx',
	'WIZWA' => 'WIZWA',
	'EMS' => 'EMS',
	'ACIEXPRESS' => 'ACI',
	'EZUSA' => 'EZUSA',
	'PANTOS' => '�������佺',
	'SWGEXP' => '�����۷ι�',
	'TNT' => 'TNT',
	'UPS' => 'UPS',
	'CVSNET' => '�������ù�',
	'HDEXP' => '�յ��ù�',
	'CH1' => '��Ÿ �ù�',
);

$__navercheckout_message_schema['claimStatusType'] = array(
	'CANCEL_REQUEST' => '��ҿ�û',
	'CANCELING' => '���ó����',
	'CANCEL_DONE' => '���ó���Ϸ�',	// �̶�, ��ǰ �ֹ����°� '���' �� �����
	'CANCEL_REJECT' => '���öȸ',
	'RETURN_REQUEST' => '��ǰ��û',
	'EXCHANGE_REQUEST' => '��ȯ��û',
	'COLLECTING' => '����ó����',
	'COLLECT_DONE' => '���ſϷ�',
	'EXCHANGE_REDELIVERING' => '��ȯ������',
	'RETURN_DONE' => '��ǰ�Ϸ�',		// �̶�, ��ǰ �ֹ����°� '��ǰ' �� �����
	'EXCHANGE_DONE' => '��ȯ�Ϸ�',		// �̶�, ��ǰ �ֹ����°� '��ȯ' �� �����
	'RETURN_REJECT' => '��ǰöȸ',
	'EXCHANGE_REJECT' => '��ȯ�ź�',
	'PURCHASE_DECISION_HOLDBACK' => '����Ȯ������',
	'PURCHASE_DECISION_HOLDBACK_REDELIVERING' => '����Ȯ������ ������',
	'PURCHASE_DECISION_REQUEST' => '����Ȯ����û',
	'PURCHASE_DECISION_HOLDBACK_RELEASE' => '����Ȯ����������',
	'ADMIN_CANCELING' => '���������',
	'ADMIN_CANCEL_DONE' => '������ҿϷ�',	// �̶�, ��ǰ �ֹ����°� '���' �� �����
);


$__navercheckout_message_schema['holdbackClassType'] = array(
	'RETURN_DELIVERYFEE' => 'RETURN_DELIVERYFEE',
	'EXTRAFEEE' => 'EXTRAFEEE',
	'RETURN_DELIVERYFEE_AND_EXTRAFEEE' => 'RETURN_DELIVERYFEE_AND_EXTRAFEEE',
	'RETURN_PRODUCT_NOT_DELIVERED' => 'RETURN_PRODUCT_NOT_DELIVERED',
	'ETC' => 'ETC',
	'EXCHANGE_DELIVERYFEE' => 'EXCHANGE_DELIVERYFEE',
	'EXCHANGE_EXTRAFEE' => 'EXCHANGE_EXTRAFEE',
	'EXCHANGE_PRODUCT_READY' => 'EXCHANGE_PRODUCT_READY',
	'EXCHANGE_PRODUCT_NOT_DELIVERED' => 'EXCHANGE_PRODUCT_NOT_DELIVERED',
	'SELLER_CONFIRM_NEED' => '�Ǹ���Ȯ���ʿ�',
	'PURCHASER_CONFIRM_NEED' => '������Ȯ���ʿ�',
	'SELLER_REMIT' => '�Ǹ��� ���� �۱�',
	'ETC2' => 'ETC2',
	'EXCHANGE_HOLDBACK' => '��ȯ ����Ȯ������',
);

$__navercheckout_message_schema['holdbackStatusType'] = array(
	'NOT_YET' => '�̺���',
	'HOLDBACK' => '������',
	'RELEASED' => '��������',
);



$__navercheckout_message_schema['addressType'] = array(
	'DOMESTIC' => '����',
	'FOREIGN' => '�ؿ�',
);

$__navercheckout_message_schema['claimType'] = array(
	'CANCEL' => '���',
	'RETURN' => '��ǰ',
	'EXCHANGE' => '��ȯ',
	'PURCHASE_DECISION_HOLDBACK' => '����Ȯ������',
	'ADMIN_CANCEL' => '���� ���',
);

// �߼���������
$__navercheckout_message_schema['delayedDispatchReasonType'] = array(
	'PRODUCT_PREPARE' => '��ǰ �غ� ��',
	'CUSTOMER_REQUEST' => '�� ��û',
	'CUSTOM_BUILD' => '�ֹ� ����',
	'RESERVED_DISPATCH' => '���� �߼�',
	'ETC' => '��Ÿ',
);

$__navercheckout_message_schema['placeOrderStatusType'] = array(
	'NOT_YET' => '���� ��Ȯ��',
	'OK' => '���� Ȯ��',
	'CANCEL' => '���� Ȯ������',
);

$__navercheckout_message_schema['productOrderStatusType'] = array(
	'PAYMENT_WAITING' => '�Աݴ��',
	'CANCELED_BY_NOPAYMENT' => '���Ա����',
	'PAYED' => '�����Ϸ�',
	'DELIVERING' => '�����',
	'DELIVERED' => '��ۿϷ�',
	'PURCHASE_DECIDED' => '����Ȯ��',
	'CANCELED' => '���',
	'RETURNED' => '��ǰ',
	'EXCHANGED' => '��ȯ',
);



$__navercheckout_message_schema['productOrderChangeType'] = array(
	'PAY_WAITING' => '�Ա� ���',
	'PAYED' => '���� �Ϸ�',
	'DISPATCHED' => '�߼� ó��',
	'CANCEL_REQUESTED' => '��� ��û',
	'RETURN_REQUESTED' => '��ǰ ��û',
	'EXCHANGE_REQUESTED' => '��ȯ ��û',
	'HOLDBACK_REQUESTED' => '���� Ȯ�� ���� ��û',
	'CANCELED' => '���',
	'RETURNED' => '��ǰ',
	'EXCHANGED' => '��ȯ',
	'PURCHASE_DECIDED' => '���Ա����'
);

$__navercheckout_message_schema['PurchaseReviewScore'] = array(
	'0' => '�Ҹ���',
	'1' => '����',
	'2' => '����',
);

// �̻� wdsl ��Ű�� ����

// ���� Ŭ���� ���̺� �ʵ���� ����
$__navercheckout_message_schema['claimRETURN'] = array(
	'ClaimStatus' => array('name'=>'Ŭ���� ����','schema'=>'claimStatusType'),
	'ClaimRequestDate' => 'Ŭ���� ��û��',
	'RequestChannel' => '���� ä��',
	'ReturnReason' => array('name'=>'��ǰ ����','schema'=>'claimRequestReasonType'),
	'ReturnDetailedReason' => '��ǰ �� ����',
	'HoldbackStatus' => array('name'=>'���� ����','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'���� ����','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '���� �� ����',
	'CollectAddressAddressType' => array('name'=>'������(from) �ּ� ����(�ؿ�/����)','schema'=>'addressType'),
	'CollectAddressZipCode' => '������(from) �����ȣ',
	'CollectAddressBaseAddress' => '������(from) �⺻ �ּ�',
	'CollectAddressDetailedAddress' => '������(from) �� �ּ�',
	'CollectAddressCity' => '������(from) ����',
	'CollectAddressState' => '������(from) ��(state)',
	'CollectAddressCountry' => '������(from) ����',
	'CollectAddressTel1' => '������(from) ����ó 1',
	'CollectAddressTel2' => '������(from) ����ó 2',
	'CollectAddressName' => '������(from) �̸�',
	'ReturnReceiveAddressAddressType' => array('name'=>'������(to) �ּ� ����(�ؿ�/����)','schema'=>'addressType'),
	'ReturnReceiveAddressZipCode' => '������(to) �����ȣ',
	'ReturnReceiveAddressBaseAddress' => '������(to) �⺻ �ּ�',
	'ReturnReceiveAddressDetailedAddress' => '������(to) �� �ּ�',
	'ReturnReceiveAddressCity' => '������(to) ����',
	'ReturnReceiveAddressState' => '������(to) ��(state)',
	'ReturnReceiveAddressCountry' => '������(to) ����',
	'ReturnReceiveAddressTel1' => '������(to) ����ó 1',
	'ReturnReceiveAddressTel2' => '������(to) ����ó 2',
	'ReturnReceiveAddressName' => '������(to) �̸�',
	'CollectStatus' => '���� ����',
	'CollectDeliveryMethod' => array('name'=>'���� ���','schema'=>'deliveryMethodType'),
	'CollectDeliveryCompany' => '���� �ù��',
	'CollectTrackingNumber' => '���� ���� ��ȣ',
	'CollectCompletedDate' => '���� �Ϸ���',
	'EtcFeeDemandAmount' => '��Ÿ ��� û����',
	'EtcFeePayMethod' => '��Ÿ ��� ���� ���',
	'EtcFeePayMeans' => '��Ÿ ��� ���� ����',
	'RefundStandbyStatus' => 'ȯ�� ��� ����',
	'RefundStandbyReason' => 'ȯ�� ��� ����',
	'RefundRequestDate' => 'ȯ�� ��û��'
);

$__navercheckout_message_schema['claimCANCEL'] = array(
	'ClaimStatus' => array('name'=>'Ŭ���� ����','schema'=>'claimStatusType'),
	'ClaimRequestDate' => 'Ŭ���� ��û��',
	'RequestChannel' => '���� ä��',
	'CancelReason' => array('name'=>'��� ����','schema'=>'claimRequestReasonType'),
	'CancelDetailedReason' => '��� �� ����',
	'CancelCompletedDate' => '��� �Ϸ���',
	'CancelApprovalDate' => '��� ������',
	'HoldbackStatus' => array('name'=>'���� ����','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'���� ����','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '���� �� ����',
	'EtcFeeDemandAmount' => '��Ÿ ��� û����',
	'EtcFeePayMethod' =>'��Ÿ ��� ���� ���',
	'EtcFeePayMeans' => '��Ÿ ��� ���� ����',
	'RefundStandbyStatus' => 'ȯ�� ��� ����',
	'RefundStandbyReason' => 'ȯ�� ��� ����',
	'RefundRequestDate' => 'ȯ�� ��û��'
);

$__navercheckout_message_schema['claimADMIN_CANCEL'] = array(
	'ClaimStatus' => array('name'=>'Ŭ���� ����','schema'=>'claimStatusType'),
	'ClaimRequestDate' => 'Ŭ���� ��û��',
	'RequestChannel' => '���� ä��',
	'CancelReason' => array('name'=>'��� ����','schema'=>'claimRequestReasonType'),
	'CancelDetailedReason' => '��� �� ����',
	'CancelCompletedDate' => '��� �Ϸ���',
	'CancelApprovalDate' => '��� ������',
	'HoldbackStatus' => array('name'=>'���� ����','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'���� ����','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '���� �� ����',
	'EtcFeeDemandAmount' => '��Ÿ ��� û����',
	'EtcFeePayMethod' =>'��Ÿ ��� ���� ���',
	'EtcFeePayMeans' => '��Ÿ ��� ���� ����',
	'RefundStandbyStatus' => 'ȯ�� ��� ����',
	'RefundStandbyReason' => 'ȯ�� ��� ����',
	'RefundRequestDate' => 'ȯ�� ��û��'
);

$__navercheckout_message_schema['claimEXCHANGE'] = array(
	'ClaimStatus' => array('name'=>'Ŭ���� ����','schema'=>'claimStatusType'),
	'ClaimRequestDate' => 'Ŭ���� ��û��',
	'RequestChannel' => '���� ä��',
	'ExchangeReason' => array('name'=>'��ȯ ����','schema'=>'claimRequestReasonType'),
	'ExchangeDetailedReason' => '��ȯ �� ����',
	'HoldbackStatus' => array('name'=>'���� ����','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'���� ����','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '���� �� ����',
	'CollectAddressAddressType' => array('name'=>'������(from) �ּ� ����(�ؿ�/����)','schema'=>'addressType'),
	'CollectAddressZipCode' => '������(from) �����ȣ',
	'CollectAddressBaseAddress' => '������(from) �⺻ �ּ�',
	'CollectAddressDetailedAddress' => '������(from) �� �ּ�',
	'CollectAddressCity' => '������(from) ����',
	'CollectAddressState' => '������(from) ��(state)',
	'CollectAddressCountry' => '������(from) ����',
	'CollectAddressTel1' => '������(from) ����ó 1',
	'CollectAddressTel2' => '������(from) ����ó 2',
	'CollectAddressName' => '������(from) �̸�',
	'ReturnReceiveAddressAddressType' => array('name'=>'������(to) �ּ� ����(�ؿ�/����)','schema'=>'addressType'),
	'ReturnReceiveAddressZipCode' => '������(to) �����ȣ',
	'ReturnReceiveAddressBaseAddress' => '������(to) �⺻ �ּ�',
	'ReturnReceiveAddressDetailedAddress' => '������(to) �� �ּ�',
	'ReturnReceiveAddressCity' => '������(to) ����',
	'ReturnReceiveAddressState' => '������(to) ��(state)',
	'ReturnReceiveAddressCountry' => '������(to) ����',
	'ReturnReceiveAddressTel1' => '������(to) ����ó 1',
	'ReturnReceiveAddressTel2' => '������(to) ����ó 2',
	'ReturnReceiveAddressName' => '������(to) �̸�',
	'CollectStatus' => '���� ����',
	'CollectDeliveryMethod' => array('name'=>'���� ���','schema'=>'deliveryMethodType'),
	'CollectDeliveryCompany' => '���� �ù��',
	'CollectTrackingNumber' => '���� ���� ��ȣ',
	'CollectCompletedDate' => '���� �Ϸ���',
	'ReDeliveryStatus' => '���� ����',
	'ReDeliveryMethod' => array('name'=>'���� ���','schema'=>'deliveryMethodType'),
	'ReDeliveryCompany' => '���� �ù��',
	'ReDeliveryTrackingNumber' => '���� ���� ��ȣ',
	'EtcFeeDemandAmount' => '��Ÿ ��� û����',
	'EtcFeePayMethod' =>'��Ÿ ��� ���� ���',
	'EtcFeePayMeans' => '��Ÿ ��� ���� ����',
);

$__navercheckout_message_schema['claimPURCHASE_DECISION_HOLDBACK'] = array(
	'ClaimStatus' => array('name'=>'Ŭ���� ����','schema'=>'claimStatusType'),
	'ClaimRequestDate' => 'Ŭ���� ��û��',
	'DecisionHoldbackReason' => '���� Ȯ�� ���� ����',
	'DecisionHoldbackDetailedReason' => '���� Ȯ�� ���� �� ����',
	'DecisionHoldbackTreatMemo' => '���� Ȯ�� ���� ó�� �޸�',
	'ReDeliveryExpectedDate' => '���� ������',
	'ReDeliveryMethod' => array('name'=>'���� ���','schema'=>'deliveryMethodType'),
	'ReDeliveryCompany' => '���� �ù��',
	'ReDeliveryTrackingNumber' => '���� ���� ��ȣ'
);

// ���º� �˻��� ���� ���̸�, wdsl ���ǿʹ� ������
$__navercheckout_message_schema['extra_productOrderStatusType'] = array(
	'�Աݴ��'	=> "(PO.ProductOrderStatus = 'PAYMENT_WAITING' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'�Ա�Ȯ��'	=> "(PO.ProductOrderStatus = 'PAYED' AND PO.PlaceOrderStatus = 'NOT_YET' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'����غ���'=> "(PO.ProductOrderStatus = 'PAYED' AND PO.PlaceOrderStatus = 'OK' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'�����'	=> "(PO.ProductOrderStatus = 'DELIVERING' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'��ۿϷ�'	=> "(PO.ProductOrderStatus = 'DELIVERED' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'����Ȯ��'	=> "(PO.ProductOrderStatus = 'PURCHASE_DECIDED' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT','PURCHASE_DECISION_HOLDBACK_RELEASE'))",

	'�����ü'	=> "((PO.ClaimType = 'CANCEL' AND PO.ClaimStatus <> 'CANCEL_REJECT') OR PO.ClaimType = 'ADMIN_CANCEL')",
	'��ҿ�û'	=> "(PO.ProductOrderStatus = 'PAYED' AND PO.ClaimType = 'CANCEL' AND PO.ClaimStatus = 'CANCEL_REQUEST' AND C.HoldbackStatus = 'HOLDBACK' AND C.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'���ó����'=> "((PO.ProductOrderStatus = 'PAYED' AND PO.ClaimType = 'CANCEL' AND PO.ClaimStatus = 'CANCELING' AND C.HoldbackStatus = 'HOLDBACK' AND C.HoldbackReason = 'PURCHASER_CONFIRM_NEED') OR (PO.ClaimType = 'ADMIN_CANCEL' AND PO.ClaimStatus = 'ADMIN_CANCELING'))",
	'��ҿϷ�'	=> "(PO.ProductOrderStatus IN ('CANCELED' , 'CANCELED_BY_NOPAYMENT'))",

	'��ǰ��ü'		=> "(PO.ClaimType = 'RETURN' AND PO.ClaimStatus <> 'RETURN_REJECT')",
	'��ǰ��û'		=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'RETURN' AND PO.ClaimStatus = 'RETURN_REQUEST' AND R.HoldbackStatus = 'HOLDBACK' AND R.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'��ǰ������'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'RETURN' AND PO.ClaimStatus = 'COLLECTING' AND R.HoldbackStatus = 'HOLDBACK' AND R.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'��ǰ���ſϷ�'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'RETURN' AND PO.ClaimStatus = 'COLLECT_DONE' AND R.HoldbackStatus = 'HOLDBACK' AND R.HoldbackReason = 'PURCHASER_CONFIRM_NEED')",
	'��ǰ�Ϸ�'		=> "(PO.ProductOrderStatus = 'RETURNED')",

	'��ȯ��ü'		=> "(PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus <> 'EXCHANGE_REJECT')",
	'��ȯ��û'		=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'EXCHANGE_REQUEST' AND E.HoldbackStatus = 'HOLDBACK' AND E.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'��ȯ������'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'COLLECTING' AND E.HoldbackStatus = 'HOLDBACK' AND E.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'��ȯ���ſϷ�'=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'COLLECT_DONE' AND E.HoldbackStatus = 'HOLDBACK' AND E.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'��ȯ����غ���'=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'COLLECT_DONE' AND E.HoldbackStatus = 'RELEASED')",
	'��ȯ������'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'EXCHANGE_REDELIVERING' AND E.HoldbackStatus = 'RELEASED')",
	'��ȯ�Ϸ�'		=> "(PO.ProductOrderStatus = 'EXCHANGED')",

);

return $__navercheckout_message_schema;
?>