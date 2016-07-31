<?php
if($_POST['mode']=='test') exit('DONE');

include("../lib/library.php");
include("../conf/config.php");

// 암호화된 문자열을 처리한다
$checkoutapi = $config->load('checkoutapi');

$xxtea = Core::loader('xxtea');
$xxtea -> setKey($checkoutapi['cryptkey']);

$request = unserialize($xxtea->decrypt(base64_decode($_POST['enc'])));
$request = iconv_recursive('utf-8','euc-kr',$request);

unset($request[data][shopNo]);

function __empty($var) {
	if (is_array($var)) foreach ($var as $v) if (!empty($v)) return false;
	return true;
}

switch($request['mode']) {

	// 주문내역저장
	case 'syncronizeProductOrderData':

		if (empty($request['data'])) exit;

		$target = array(
		'O' => GD_NAVERCHECKOUT_ORDERINFO,
		'PO' => GD_NAVERCHECKOUT_PRODUCTORDERINFO,
		'D' => GD_NAVERCHECKOUT_DELIVERYINFO,
		'C' => GD_NAVERCHECKOUT_CANCELINFO,
		'R' => GD_NAVERCHECKOUT_RETURNINFO,
		'E' => GD_NAVERCHECKOUT_EXCHANGEINFO,
		'DH' => GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO
		);

		$ProductOrderID = $request['data']['PO_ProductOrderID'];

		foreach ($request['data'] as $field => $value) {

			$tmp = explode('_',$field);

			$field = $tmp[1];
			$table = $target[$tmp[0]];

			$queries[$table][$field] = $value;

		}

		foreach ($queries as $table => $values) {

			if (__empty($values)) {
				$query = "
				DELETE FROM $table WHERE ProductOrderID = '$ProductOrderID'
				";
			}
			else {

				$insert_query = '';
				$update_query = '';

				foreach($values as $k => $v) {

					$insert_query .= "$k = '".mysql_real_escape_string($v)."',";

					if ($k != 'OrderID' && $k != 'ProductOrderID')
						$update_query .= "$k = '".mysql_real_escape_string($v)."',";

				}

				$insert_query = preg_replace('/,$/','',$insert_query);
				$update_query = preg_replace('/,$/','',$update_query);

				$query = "
					INSERT INTO
						$table
					SET
						$insert_query

					ON DUPLICATE KEY
					UPDATE
						$update_query
				";
			}

			if (! $db->query($query)) exit('ERROR');

		}

		// 주문 상태에 따른 적립금, 재고 처리 (각 처리 타이밍은 변경될 수 있음)
		$naverCheckoutAPI = Core::loader('naverCheckoutAPI_4');
		$naverCheckoutAPI->setOrderEmoney($ProductOrderID);

		switch ($request['data']['PO_ProductOrderStatus']) {
			case 'PAYMENT_WAITING' :
				// 주문 접수시 재고 삭감
				if($cfg['stepStock']=='0') $naverCheckoutAPI->cutStock($ProductOrderID);
				break;
			case 'PAYED' :
				// 결제 완료시 재고 삭감
				$naverCheckoutAPI->cutStock($ProductOrderID);
				break;

			case 'PURCHASE_DECIDED' :
				// 구매 확정시 적립금, 쿠폰 지급
				if(!$naverCheckoutAPI->noEmoney) {
					$naverCheckoutAPI->setEmoney($ProductOrderID); // 적립금 적립
					$naverCheckoutAPI->setCoupon($ProductOrderID); // 쿠폰 지급
				}
				break;

			case 'CANCELED':
			case 'RETURNED':
			case 'CANCELED_BY_NOPAYMENT':
				// 취소, 반품, 미입금 취소시(접수 상태가 아닌 완료 상태) 적립금 회수 및 재고 원복
				$naverCheckoutAPI->setEmoney($ProductOrderID, -1);
				$naverCheckoutAPI->backStock($ProductOrderID);
				break;
		}

		exit('DONE');
	break;

	// 구매평가내역저장
	case 'syncronizeReviewData':

		if (empty($request['data'])) exit;

		$insert_query = '';
		$update_query = '';

		foreach ($request['data'] as $field => $value) {

			$insert_query .= "$field = '".mysql_real_escape_string($value)."',";

			if ($k != 'PurchaseReviewId')
				$update_query .= "$field = '".mysql_real_escape_string($value)."',";

		}

		$insert_query = preg_replace('/,$/','',$insert_query);
		$update_query = preg_replace('/,$/','',$update_query);

		$query = "
			INSERT INTO
				".GD_NAVERCHECKOUT_PURCHASEREVIEW."

			SET

				$insert_query

			ON DUPLICATE KEY UPDATE

				$update_query
		";

		$db->query($query);

		exit('DONE');
		break;

	/**
	 * 아래 SyncInquiry, SyncOrder 케이스는 네이버 체크아웃 4.0 패치(2012. 03. 15) 후 삭제
	 */
	// 문의내역
	case 'SyncInquiry':

		$insertData = array(
			'inquiryNo'=>$request['data']['inquiryNo'],
			'orderNo'=>$request['data']['orderNo'],
			'Category1'=>$request['data']['Category1'],
			'Category2'=>$request['data']['Category2'],
			'CustomerID'=>$request['data']['CustomerID'],
			'Email'=>$request['data']['Email'],
			'InquiryDateTimeRaw'=>$request['data']['InquiryDateTimeRaw'],
			'InquiryDateTime'=>$request['data']['InquiryDateTime'],
			'InquiryID'=>$request['data']['InquiryID'],
			'IsAnswered'=>$request['data']['IsAnswered'],
			'Answerable'=>$request['data']['Answerable'],
			'LastAnswerDateTimeRaw'=>$request['data']['LastAnswerDateTimeRaw'],
			'LastAnswerDateTime'=>$request['data']['LastAnswerDateTime'],
			'MobilePhoneNumber'=>$request['data']['MobilePhoneNumber'],
			'OrdererName'=>$request['data']['OrdererName'],
			'OrderID'=>$request['data']['OrderID'],
			'Title'=>$request['data']['Title'],
		);

		$refDataInquiryItem = &$request['data']['InquiryItem'];
		$insertInquiryItemData=array();
		foreach($refDataInquiryItem as $eachItem) {
			$insertInquiryItemData[] = array(
				'inquiryNo'=>(string)$insertData['inquiryNo'],
				'seq'=>(string)$eachItem['seq'],
				'InquiryContent'=>(string)$eachItem['InquiryContent'],
				'AnswerContentNaver'=>(string)$eachItem['AnswerContentNaver'],
				'AnswerContentShop'=>(string)$eachItem['AnswerContentShop'],
				'AnswerDateTimeRaw'=>(string)$eachItem['AnswerDateTimeRaw'],
				'AnswerDateTime'=>(string)$eachItem['AnswerDateTime'],
			);
		}

		if(!(count($insertInquiryItemData)>0)) {
			exit;
		}

		// gd_navercheckout_inquiry insert작업
		$cols = array_keys($insertData);
		array_shift($cols); // inquiryNo 값을 뺀다
		$onUpdate = array();
		foreach($cols as $eachCol) {
			$onUpdate[] = "$eachCol = values($eachCol)";
		}
		$onUpdate = implode(',',$onUpdate);

		$query = $db->_query_print('insert into gd_navercheckout_inquiry set [cv]',$insertData)." on duplicate key update {$onUpdate}";
		$db->query($query);


		// gd_navercheckout_inquiry_item 정리작업
		$db->_query_print('delete from gd_navercheckout_inquiry_item where inquiryNo=[s] and seq > [s]',$insertData['inquiryNo'],count($insertInquiryItemData));
		$db->query($query);

		// gd_navercheckout_inquiry_item insert작업
		$cols = $colsupdate = array_keys($insertInquiryItemData[0]);
		array_shift($colsupdate); array_shift($colsupdate); // inquiryNo,seq 값을 뺀다
		$onUpdate = array();
		foreach($colsupdate as $eachCol) {
			$onUpdate[] = "$eachCol = values($eachCol)";
		}
		$onUpdate = implode(',',$onUpdate);

		$query = $db->_query_print('insert into gd_navercheckout_inquiry_item [c] values [vs]',$cols,$insertInquiryItemData)." on duplicate key update {$onUpdate}";
		$db->query($query);

		exit('DONE');
	break;

	// 주문내역
	case 'SyncOrder':
		$refDataOrder = &$request['data'];

		$insertOrderData = array(
			'orderNo'=>(string)$refDataOrder['orderNo'],
			'ORDER_OrderDateTimeRaw'=>(string)$refDataOrder['ORDER_OrderDateTimeRaw'],
			'ORDER_OrderDateTime'=>(string)$refDataOrder['ORDER_OrderDateTime'],
			'ORDER_OrderID'=>(string)$refDataOrder['ORDER_OrderID'],
			'ORDER_OrderStatusCode'=>(string)$refDataOrder['ORDER_OrderStatusCode'],
			'ORDER_OrderStatus'=>(string)$refDataOrder['ORDER_OrderStatus'],
			'ORDER_OrdererName'=>(string)$refDataOrder['ORDER_OrdererName'],
			'ORDER_OrdererID'=>(string)$refDataOrder['ORDER_OrdererID'],
			'ORDER_OrdererTel'=>(string)$refDataOrder['ORDER_OrdererTel'],
			'ORDER_OrdererEmail'=>(string)$refDataOrder['ORDER_OrdererEmail'],
			'ORDER_Repayment'=>(string)$refDataOrder['ORDER_Repayment'],
			'ORDER_TotalProductAmount'=>(string)$refDataOrder['ORDER_TotalProductAmount'],
			'ORDER_ShippingFee'=>(string)$refDataOrder['ORDER_ShippingFee'],
			'ORDER_MallOrderAmount'=>(string)$refDataOrder['ORDER_MallOrderAmount'],
			'ORDER_NaverDiscountAmount'=>(string)$refDataOrder['ORDER_NaverDiscountAmount'],
			'ORDER_TotalOrderAmount'=>(string)$refDataOrder['ORDER_TotalOrderAmount'],
			'ORDER_CashbackDiscountAmount'=>(string)$refDataOrder['ORDER_CashbackDiscountAmount'],
			'ORDER_PaymentAmount'=>(string)$refDataOrder['ORDER_PaymentAmount'],
			'ORDER_PaymentMethod'=>(string)$refDataOrder['ORDER_PaymentMethod'],
			'ORDER_PaymentDateRaw'=>(string)$refDataOrder['ORDER_PaymentDateRaw'],
			'ORDER_PaymentDate'=>(string)$refDataOrder['ORDER_PaymentDate'],
			'ORDER_Escrow'=>(string)$refDataOrder['ORDER_Escrow'],
			'ORDER_ShippingFeeType'=>(string)$refDataOrder['ORDER_ShippingFeeType'],
			'ORDER_OriginalTotalProductAmount'=>(string)$refDataOrder['ORDER_OriginalTotalProductAmount'],
			'ORDER_OriginalShippingFee'=>(string)$refDataOrder['ORDER_OriginalShippingFee'],
			'ORDER_OriginalMallOrderAmount'=>(string)$refDataOrder['ORDER_OriginalMallOrderAmount'],
			'ORDER_OriginalNaverDiscountAmount'=>(string)$refDataOrder['ORDER_OriginalNaverDiscountAmount'],
			'ORDER_OriginalTotalOrderAmount'=>(string)$refDataOrder['ORDER_OriginalTotalOrderAmount'],
			'ORDER_OriginalCashbackDiscountAmount'=>(string)$refDataOrder['ORDER_OriginalCashbackDiscountAmount'],
			'ORDER_OriginalPaymentAmount'=>(string)$refDataOrder['ORDER_OriginalPaymentAmount'],
			'ORDER_OriginalPaymentMethod'=>(string)$refDataOrder['ORDER_OriginalPaymentMethod'],
			'ORDER_OriginalPaymentDateRaw'=>(string)$refDataOrder['ORDER_OriginalPaymentDateRaw'],
			'ORDER_OriginalPaymentDate'=>(string)$refDataOrder['ORDER_OriginalPaymentDate'],
			'ORDER_OriginalEscrow'=>(string)$refDataOrder['ORDER_OriginalEscrow'],
			'ORDER_OriginalShippingFeeType'=>(string)$refDataOrder['ORDER_OriginalShippingFeeType'],
			'ORDER_SaleCompleteDateRaw'=>(string)$refDataOrder['ORDER_SaleCompleteDateRaw'],
			'ORDER_SaleCompleteDate'=>(string)$refDataOrder['ORDER_SaleCompleteDate'],
			'ORDER_PaymentDueDateRaw'=>(string)$refDataOrder['ORDER_PaymentDueDateRaw'],
			'ORDER_PaymentDueDate'=>(string)$refDataOrder['ORDER_PaymentDueDate'],
			'ORDER_PaymentNumber'=>(string)$refDataOrder['ORDER_PaymentNumber'],
			'ORDER_PaymentBank'=>(string)$refDataOrder['ORDER_PaymentBank'],
			'ORDER_PaymentSender'=>(string)$refDataOrder['ORDER_PaymentSender'],
			'ORDER_SellingCode'=>(string)$refDataOrder['ORDER_SellingCode'],
			'ORDER_OrderExtraData'=>(string)$refDataOrder['ORDER_OrderExtraData'],
			'SHIPPING_Recipient'=>(string)$refDataOrder['SHIPPING_Recipient'],
			'SHIPPING_ZipCode'=>(string)$refDataOrder['SHIPPING_ZipCode'],
			'SHIPPING_ShippingAddress1'=>(string)$refDataOrder['SHIPPING_ShippingAddress1'],
			'SHIPPING_ShippingAddress2'=>(string)$refDataOrder['SHIPPING_ShippingAddress2'],
			'SHIPPING_RecipientTel1'=>(string)$refDataOrder['SHIPPING_RecipientTel1'],
			'SHIPPING_RecipientTel2'=>(string)$refDataOrder['SHIPPING_RecipientTel2'],
			'SHIPPING_ShippingMessage'=>(string)$refDataOrder['SHIPPING_ShippingMessage'],
			'DELIVERY_SendDateRaw'=>(string)$refDataOrder['DELIVERY_SendDateRaw'],
			'DELIVERY_SendDate'=>(string)$refDataOrder['DELIVERY_SendDate'],
			'DELIVERY_PickupDateRaw'=>(string)$refDataOrder['DELIVERY_PickupDateRaw'],
			'DELIVERY_PickupDate'=>(string)$refDataOrder['DELIVERY_PickupDate'],
			'DELIVERY_ShippingCompleteDateRaw'=>(string)$refDataOrder['DELIVERY_ShippingCompleteDateRaw'],
			'DELIVERY_ShippingCompleteDate'=>(string)$refDataOrder['DELIVERY_ShippingCompleteDate'],
			'DELIVERY_ShippingCompany'=>(string)$refDataOrder['DELIVERY_ShippingCompany'],
			'DELIVERY_EtcShipping'=>(string)$refDataOrder['DELIVERY_EtcShipping'],
			'DELIVERY_TrackingNumber'=>(string)$refDataOrder['DELIVERY_TrackingNumber'],
			'DELIVERY_ShippingProcessStatus'=>(string)$refDataOrder['DELIVERY_ShippingProcessStatus'],
			'DELIVERY_ShippingStatus'=>(string)$refDataOrder['DELIVERY_ShippingStatus'],
			'CANCEL_CancelReason'=>(string)$refDataOrder['CANCEL_CancelReason'],
			'CANCEL_CancelRequester'=>(string)$refDataOrder['CANCEL_CancelRequester'],
			'CANCEL_RefundPended'=>(string)$refDataOrder['CANCEL_RefundPended'],
			'CANCEL_RefundBank'=>(string)$refDataOrder['CANCEL_RefundBank'],
			'CANCEL_RefundAccountOwner'=>(string)$refDataOrder['CANCEL_RefundAccountOwner'],
			'CANCEL_RefundAccountNumber'=>(string)$refDataOrder['CANCEL_RefundAccountNumber'],
			'CANCEL_CancelRequestDateRaw'=>(string)$refDataOrder['CANCEL_CancelRequestDateRaw'],
			'CANCEL_CancelRequestDate'=>(string)$refDataOrder['CANCEL_CancelRequestDate'],
			'RETURN_ReturnReason'=>(string)$refDataOrder['RETURN_ReturnReason'],
			'RETURN_ReturnStatusCode'=>(string)$refDataOrder['RETURN_ReturnStatusCode'],
			'RETURN_ReturnStatus'=>(string)$refDataOrder['RETURN_ReturnStatus'],
			'RETURN_ReturnDateRaw'=>(string)$refDataOrder['RETURN_ReturnDateRaw'],
			'RETURN_ReturnDate'=>(string)$refDataOrder['RETURN_ReturnDate'],
			'RETURN_ReturnShippingCompany'=>(string)$refDataOrder['RETURN_ReturnShippingCompany'],
			'RETURN_ReturnTrackingNumber'=>(string)$refDataOrder['RETURN_ReturnTrackingNumber'],
			'RETURN_ReturnShippingFeeType'=>(string)$refDataOrder['RETURN_ReturnShippingFeeType'],
			'RETURN_ReceivedDateRaw'=>(string)$refDataOrder['RETURN_ReceivedDateRaw'],
			'RETURN_ReceivedDate'=>(string)$refDataOrder['RETURN_ReceivedDate'],
			'RETURN_RefundBank'=>(string)$refDataOrder['RETURN_RefundBank'],
			'RETURN_RefundAccountOwner'=>(string)$refDataOrder['RETURN_RefundAccountOwner'],
			'RETURN_RefundAccountNumber'=>(string)$refDataOrder['RETURN_RefundAccountNumber'],
			'RETURN_Protest'=>(string)$refDataOrder['RETURN_Protest'],
			'RETURN_ReturnRequestDateRaw'=>(string)$refDataOrder['RETURN_ReturnRequestDateRaw'],
			'RETURN_ReturnRequestDate'=>(string)$refDataOrder['RETURN_ReturnRequestDate'],
			'EXCHANGE_ExchangeReason'=>(string)$refDataOrder['EXCHANGE_ExchangeReason'],
			'EXCHANGE_ExchangeStatusCode'=>(string)$refDataOrder['EXCHANGE_ExchangeStatusCode'],
			'EXCHANGE_ExchangeStatus'=>(string)$refDataOrder['EXCHANGE_ExchangeStatus'],
			'EXCHANGE_ReturnDateRaw'=>(string)$refDataOrder['EXCHANGE_ReturnDateRaw'],
			'EXCHANGE_ReturnDate'=>(string)$refDataOrder['EXCHANGE_ReturnDate'],
			'EXCHANGE_ReturnShippingCompany'=>(string)$refDataOrder['EXCHANGE_ReturnShippingCompany'],
			'EXCHANGE_ReturnTrackingNumber'=>(string)$refDataOrder['EXCHANGE_ReturnTrackingNumber'],
			'EXCHANGE_ReturnShippingFeeType'=>(string)$refDataOrder['EXCHANGE_ReturnShippingFeeType'],
			'EXCHANGE_ReceivedDateRaw'=>(string)$refDataOrder['EXCHANGE_ReceivedDateRaw'],
			'EXCHANGE_ReceivedDate'=>(string)$refDataOrder['EXCHANGE_ReceivedDate'],
			'EXCHANGE_ResendDateRaw'=>(string)$refDataOrder['EXCHANGE_ResendDateRaw'],
			'EXCHANGE_ResendDate'=>(string)$refDataOrder['EXCHANGE_ResendDate'],
			'EXCHANGE_ResendShippingCompany'=>(string)$refDataOrder['EXCHANGE_ResendShippingCompany'],
			'EXCHANGE_ResendTrackingNumber'=>(string)$refDataOrder['EXCHANGE_ResendTrackingNumber'],
			'EXCHANGE_Protest'=>(string)$refDataOrder['EXCHANGE_Protest'],
			'EXCHANGE_ExchangeRequestDateRaw'=>(string)$refDataOrder['EXCHANGE_ExchangeRequestDateRaw'],
			'EXCHANGE_ExchangeRequestDate'=>(string)$refDataOrder['EXCHANGE_ExchangeRequestDate'],
			'EXCHANGE_ResendRecipient'=>(string)$refDataOrder['EXCHANGE_ResendRecipient'],
			'EXCHANGE_ResendRecipientTel'=>(string)$refDataOrder['EXCHANGE_ResendRecipientTel'],
			'EXCHANGE_ResendShippingAddress'=>(string)$refDataOrder['EXCHANGE_ResendShippingAddress'],
			'ORDER_MallMemberID'=>(string)$refDataOrder['ORDER_MallMemberID'],
		);
		$refDataOrderProduct = &$request['data']['product'];
		$insertOrderProductData=array();
		foreach($refDataOrderProduct as $eachProduct) {
			$insertOrderProductData[] = array(
				'orderNo'=>(string)$refDataOrder['orderNo'],
				'seq'=>(string)$eachProduct['seq'],
				'ProductName'=>(string)$eachProduct['ProductName'],
				'ProductID'=>(string)$eachProduct['ProductID'],
				'ProductOption'=>(string)$eachProduct['ProductOption'],
				'Quantity'=>(string)$eachProduct['Quantity'],
				'UnitPrice'=>(string)$eachProduct['UnitPrice'],
				'ReturnRequested'=>(string)$eachProduct['ReturnRequested'],
			);
		}

		if(!(count($insertOrderProductData)>0)) {
			exit;
		}

		// gd_navercheckout_order insert작업
		$cols = array_keys($insertOrderData);
		array_shift($cols); // orderNo 값을 뺀다
		$onUpdate = array();
		foreach($cols as $eachCol) {
			$onUpdate[] = "$eachCol = values($eachCol)";
		}
		$onUpdate = implode(',',$onUpdate);

		$query = $db->_query_print('insert into gd_navercheckout_order set [cv]',$insertOrderData)." on duplicate key update {$onUpdate}";
		$db->query($query);

		// gd_navercheckout_order_product 정리작업
		$query = $db->_query_print('delete from gd_navercheckout_order_product where orderNo=[s] and seq > [s]',$refDataOrder['orderNo'],count($insertOrderProductData));
		$db->query($query);

		// gd_navercheckout_order_product insert작업
		$cols = $colsupdate = array_keys($insertOrderProductData[0]);
		array_shift($colsupdate); array_shift($colsupdate); // orderNo,seq 값을 뺀다
		$onUpdate = array();
		foreach($cols as $eachCol) {
			$onUpdate[] = "$eachCol = values($eachCol)";
		}
		$onUpdate = implode(',',$onUpdate);

		$query = $db->_query_print('insert into gd_navercheckout_order_product [c] values [vs]',$cols,$insertOrderProductData)." on duplicate key update {$onUpdate}";
		$db->query($query);

		// 적립금 미리 계산
		$naverCheckoutAPI = &load_class('naverCheckoutAPI','naverCheckoutAPI');
		$naverCheckoutAPI->setOrderEmoney($refDataOrder['orderNo']);

		if($refDataOrder['ORDER_OrderStatusCode']=='OD0037') {
			$naverCheckoutAPI->setEmoney($refDataOrder['orderNo']); // 적립금 적립
			$naverCheckoutAPI->setCoupon($refDataOrder['orderNo']); // 쿠폰 지급
		}
		if(in_array($refDataOrder['ORDER_OrderStatusCode'], array('OD0032', 'OD0033', 'OD0036'))) $naverCheckoutAPI->setEmoney($refDataOrder['orderNo'], -1); // 적립금 회수

		exit('DONE');
	break;
}
?>
