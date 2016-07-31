<?php
/**
 * 중계서버와 통신하는 모뮬
 * @author sunny, oneorzero
 */
class naverCheckoutAPI {
	var $relayURL = 'http://navercheck.godo.co.kr/listen.shop.php'; // 중계서버 주소
	var $cryptKey;
	var $shopNo;
	var $error;
	var $requestResult;
	var $checkoutCfg;
	var $nc;
	var $noEmoney = false;

	function naverCheckoutAPI() {
		global $checkoutCfg;
		if(!$checkoutCfg):
			if(file_exists(dirname(__FILE__).'../conf/naverCheckout.cfg.php'))
				require dirname(__FILE__).'../conf/naverCheckout.cfg.php';
		endif;
		$this->checkoutCfg = $checkoutCfg;

		$config = Core::loader('config');
		$checkoutapi = $config->load('checkoutapi');
		$this->cryptKey = $checkoutapi['cryptkey'];
		$godo = $config->load('godo');
		$this->shopNo = $godo['sno'];

		include_once(SHOPROOT.'/lib/httpSock.class.php');
	}

	/*
		기존에 가지고 있는 주문데이터를 다시 갱신시킨다.
	*/
	function SyncOrder($orderNo) {
		$request = array(
			'mode'=>'SyncOrder',
			'orderNo'=>$orderNo,
		);
		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbOrder($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}


	/*
		주문접수처리를 한다
	*/
	function PlaceOrder($orderNo) {
		$request = array(
			'mode'=>'PlaceOrder',
			'orderNo'=>$orderNo,
		);
		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbOrder($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	/*
		주문발송처리를 한다
	*/
	function ShipOrder($orderNo,$ShippingCompleteDate,$ShippingCompany,$TrackingNumber) {

		$request = array(
			'mode'=>'ShipOrder',
			'orderNo'=>$orderNo,
			'ShippingCompleteDate'=>$ShippingCompleteDate,
			'ShippingCompany'=>$ShippingCompany,
			'TrackingNumber'=>$TrackingNumber,
		);
		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbOrder($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];

			return false;
		}
	}

	/*
		판매취소처리를 한다
	*/
	function CancelSale($orderNo,$CancelReason,$CancelReasonDetail) {
		$request = array(
			'mode'=>'CancelSale',
			'orderNo'=>$orderNo,
			'CancelReason'=>$CancelReason,
			'CancelReasonDetail'=>iconv('euc-kr','utf-8',$CancelReasonDetail),
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbOrder($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	/*
		주문취소처리를 한다
	*/
	function CancelOrder($orderNo,$CancelReason,$CancelReasonDetail) {
		$request = array(
			'mode'=>'CancelOrder',
			'orderNo'=>$orderNo,
			'CancelReason'=>$CancelReason,
			'CancelReasonDetail'=>iconv('euc-kr','utf-8',$CancelReasonDetail),
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbOrder($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	/*
		발송취소처리를 한다
	*/
	function CancelShipping($orderNo) {
		$request = array(
			'mode'=>'CancelShipping',
			'orderNo'=>$orderNo,
		);
		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbOrder($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	function AnswerCustomerInquiry($inquiryNo,$AnswerContent) {
		$request = array(
			'mode'=>'AnswerCustomerInquiry',
			'inquiryNo'=>$inquiryNo,
			'AnswerContent'=>iconv('euc-kr','utf-8',$AnswerContent),
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if($result['result']) {
			$this->_updateDbInquiry($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	function _updateDbInquiry($data) {
		$db = Core::loader('db');
		$inquiryNo = (string)$data['inquiryNo'];
		$updateInquiryData = array(
			'orderNo'=>$data['orderNo'],
			'Category1'=>$data['Category1'],
			'Category2'=>$data['Category2'],
			'CustomerID'=>$data['CustomerID'],
			'Email'=>$data['Email'],
			'InquiryDateTimeRaw'=>$data['InquiryDateTimeRaw'],
			'InquiryDateTime'=>$data['InquiryDateTime'],
			'InquiryID'=>$data['InquiryID'],
			'IsAnswered'=>$data['IsAnswered'],
			'Answerable'=>$data['Answerable'],
			'LastAnswerDateTimeRaw'=>$data['LastAnswerDateTimeRaw'],
			'LastAnswerDateTime'=>$data['LastAnswerDateTime'],
			'MobilePhoneNumber'=>$data['MobilePhoneNumber'],
			'OrdererName'=>$data['OrdererName'],
			'OrderID'=>$data['OrderID'],
			'Title'=>$data['Title'],
		);

		$dataItem = &$data['InquiryItem'];
		$updateInquiryItemData=array();
		foreach($dataItem as $eachItem) {
			$updateInquiryItemData[] = array(
				'inquiryNo'=>(string)$inquiryNo,
				'seq'=>(string)$eachItem['seq'],
				'InquiryContent'=>(string)$eachItem['InquiryContent'],
				'AnswerContentNaver'=>(string)$eachItem['AnswerContentNaver'],
				'AnswerContentShop'=>(string)$eachItem['AnswerContentShop'],
				'AnswerDateTimeRaw'=>(string)$eachItem['AnswerDateTimeRaw'],
				'AnswerDateTime'=>(string)$eachItem['AnswerDateTime'],
			);
		}

		if(!(count($updateInquiryItemData)>0)) {
			exit;
		}

		// gd_navercheckout_inquiry insert작업
		$query = $db->_query_print('update gd_navercheckout_inquiry set [cv] where inquiryNo=[s]',$updateInquiryData,$inquiryNo);
		$db->query($query);

		// gd_navercheckout_inquiry_item 정리작업
		$db->_query_print('delete from gd_navercheckout_inquiry_item where inquiryNo=[s] and seq > [s]',$inquiryNo,count($updateInquiryItemData));
		$db->query($query);

		// gd_navercheckout_inquiry_item insert작업
		$cols = $colsupdate = array_keys($updateInquiryItemData[0]);
		array_shift($colsupdate); array_shift($colsupdate); // inquiryNo,seq 값을 뺀다
		$onUpdate = array();
		foreach($colsupdate as $eachCol) {
			$onUpdate[] = "$eachCol = values($eachCol)";
		}
		$onUpdate = implode(',',$onUpdate);

		$query = $db->_query_print('insert into gd_navercheckout_inquiry_item [c] values [vs]',$cols,$updateInquiryItemData)." on duplicate key update {$onUpdate}";
		$db->query($query);

	}

	function _updateDbOrder($data) {
		$db = Core::loader('db');
		$orderNo = (string)$data['orderNo'];
		$updateOrderData = array(
			'ORDER_OrderDateTimeRaw'=>(string)$data['ORDER_OrderDateTimeRaw'],
			'ORDER_OrderDateTime'=>(string)$data['ORDER_OrderDateTime'],
			'ORDER_OrderID'=>(string)$data['ORDER_OrderID'],
			'ORDER_OrderStatusCode'=>(string)$data['ORDER_OrderStatusCode'],
			'ORDER_OrderStatus'=>(string)$data['ORDER_OrderStatus'],
			'ORDER_OrdererName'=>(string)$data['ORDER_OrdererName'],
			'ORDER_OrdererID'=>(string)$data['ORDER_OrdererID'],
			'ORDER_OrdererTel'=>(string)$data['ORDER_OrdererTel'],
			'ORDER_OrdererEmail'=>(string)$data['ORDER_OrdererEmail'],
			'ORDER_Repayment'=>(string)$data['ORDER_Repayment'],
			'ORDER_TotalProductAmount'=>(string)$data['ORDER_TotalProductAmount'],
			'ORDER_ShippingFee'=>(string)$data['ORDER_ShippingFee'],
			'ORDER_MallOrderAmount'=>(string)$data['ORDER_MallOrderAmount'],
			'ORDER_NaverDiscountAmount'=>(string)$data['ORDER_NaverDiscountAmount'],
			'ORDER_TotalOrderAmount'=>(string)$data['ORDER_TotalOrderAmount'],
			'ORDER_CashbackDiscountAmount'=>(string)$data['ORDER_CashbackDiscountAmount'],
			'ORDER_PaymentAmount'=>(string)$data['ORDER_PaymentAmount'],
			'ORDER_PaymentMethod'=>(string)$data['ORDER_PaymentMethod'],
			'ORDER_PaymentDateRaw'=>(string)$data['ORDER_PaymentDateRaw'],
			'ORDER_PaymentDate'=>(string)$data['ORDER_PaymentDate'],
			'ORDER_Escrow'=>(string)$data['ORDER_Escrow'],
			'ORDER_ShippingFeeType'=>(string)$data['ORDER_ShippingFeeType'],
			'ORDER_OriginalTotalProductAmount'=>(string)$data['ORDER_OriginalTotalProductAmount'],
			'ORDER_OriginalShippingFee'=>(string)$data['ORDER_OriginalShippingFee'],
			'ORDER_OriginalMallOrderAmount'=>(string)$data['ORDER_OriginalMallOrderAmount'],
			'ORDER_OriginalNaverDiscountAmount'=>(string)$data['ORDER_OriginalNaverDiscountAmount'],
			'ORDER_OriginalTotalOrderAmount'=>(string)$data['ORDER_OriginalTotalOrderAmount'],
			'ORDER_OriginalCashbackDiscountAmount'=>(string)$data['ORDER_OriginalCashbackDiscountAmount'],
			'ORDER_OriginalPaymentAmount'=>(string)$data['ORDER_OriginalPaymentAmount'],
			'ORDER_OriginalPaymentMethod'=>(string)$data['ORDER_OriginalPaymentMethod'],
			'ORDER_OriginalPaymentDateRaw'=>(string)$data['ORDER_OriginalPaymentDateRaw'],
			'ORDER_OriginalPaymentDate'=>(string)$data['ORDER_OriginalPaymentDate'],
			'ORDER_OriginalEscrow'=>(string)$data['ORDER_OriginalEscrow'],
			'ORDER_OriginalShippingFeeType'=>(string)$data['ORDER_OriginalShippingFeeType'],
			'ORDER_SaleCompleteDateRaw'=>(string)$data['ORDER_SaleCompleteDateRaw'],
			'ORDER_SaleCompleteDate'=>(string)$data['ORDER_SaleCompleteDate'],
			'ORDER_PaymentDueDateRaw'=>(string)$data['ORDER_PaymentDueDateRaw'],
			'ORDER_PaymentDueDate'=>(string)$data['ORDER_PaymentDueDate'],
			'ORDER_PaymentNumber'=>(string)$data['ORDER_PaymentNumber'],
			'ORDER_PaymentBank'=>(string)$data['ORDER_PaymentBank'],
			'ORDER_PaymentSender'=>(string)$data['ORDER_PaymentSender'],
			'ORDER_SellingCode'=>(string)$data['ORDER_SellingCode'],
			'ORDER_OrderExtraData'=>(string)$data['ORDER_OrderExtraData'],
			'SHIPPING_Recipient'=>(string)$data['SHIPPING_Recipient'],
			'SHIPPING_ZipCode'=>(string)$data['SHIPPING_ZipCode'],
			'SHIPPING_ShippingAddress1'=>(string)$data['SHIPPING_ShippingAddress1'],
			'SHIPPING_ShippingAddress2'=>(string)$data['SHIPPING_ShippingAddress2'],
			'SHIPPING_RecipientTel1'=>(string)$data['SHIPPING_RecipientTel1'],
			'SHIPPING_RecipientTel2'=>(string)$data['SHIPPING_RecipientTel2'],
			'SHIPPING_ShippingMessage'=>(string)$data['SHIPPING_ShippingMessage'],
			'DELIVERY_SendDateRaw'=>(string)$data['DELIVERY_SendDateRaw'],
			'DELIVERY_SendDate'=>(string)$data['DELIVERY_SendDate'],
			'DELIVERY_PickupDateRaw'=>(string)$data['DELIVERY_PickupDateRaw'],
			'DELIVERY_PickupDate'=>(string)$data['DELIVERY_PickupDate'],
			'DELIVERY_ShippingCompleteDateRaw'=>(string)$data['DELIVERY_ShippingCompleteDateRaw'],
			'DELIVERY_ShippingCompleteDate'=>(string)$data['DELIVERY_ShippingCompleteDate'],
			'DELIVERY_ShippingCompany'=>(string)$data['DELIVERY_ShippingCompany'],
			'DELIVERY_EtcShipping'=>(string)$data['DELIVERY_EtcShipping'],
			'DELIVERY_TrackingNumber'=>(string)$data['DELIVERY_TrackingNumber'],
			'DELIVERY_ShippingProcessStatus'=>(string)$data['DELIVERY_ShippingProcessStatus'],
			'DELIVERY_ShippingStatus'=>(string)$data['DELIVERY_ShippingStatus'],
			'CANCEL_CancelReason'=>(string)$data['CANCEL_CancelReason'],
			'CANCEL_CancelRequester'=>(string)$data['CANCEL_CancelRequester'],
			'CANCEL_RefundPended'=>(string)$data['CANCEL_RefundPended'],
			'CANCEL_RefundBank'=>(string)$data['CANCEL_RefundBank'],
			'CANCEL_RefundAccountOwner'=>(string)$data['CANCEL_RefundAccountOwner'],
			'CANCEL_RefundAccountNumber'=>(string)$data['CANCEL_RefundAccountNumber'],
			'CANCEL_CancelRequestDateRaw'=>(string)$data['CANCEL_CancelRequestDateRaw'],
			'CANCEL_CancelRequestDate'=>(string)$data['CANCEL_CancelRequestDate'],
			'RETURN_ReturnReason'=>(string)$data['RETURN_ReturnReason'],
			'RETURN_ReturnStatusCode'=>(string)$data['RETURN_ReturnStatusCode'],
			'RETURN_ReturnStatus'=>(string)$data['RETURN_ReturnStatus'],
			'RETURN_ReturnDateRaw'=>(string)$data['RETURN_ReturnDateRaw'],
			'RETURN_ReturnDate'=>(string)$data['RETURN_ReturnDate'],
			'RETURN_ReturnShippingCompany'=>(string)$data['RETURN_ReturnShippingCompany'],
			'RETURN_ReturnTrackingNumber'=>(string)$data['RETURN_ReturnTrackingNumber'],
			'RETURN_ReturnShippingFeeType'=>(string)$data['RETURN_ReturnShippingFeeType'],
			'RETURN_ReceivedDateRaw'=>(string)$data['RETURN_ReceivedDateRaw'],
			'RETURN_ReceivedDate'=>(string)$data['RETURN_ReceivedDate'],
			'RETURN_RefundBank'=>(string)$data['RETURN_RefundBank'],
			'RETURN_RefundAccountOwner'=>(string)$data['RETURN_RefundAccountOwner'],
			'RETURN_RefundAccountNumber'=>(string)$data['RETURN_RefundAccountNumber'],
			'RETURN_Protest'=>(string)$data['RETURN_Protest'],
			'RETURN_ReturnRequestDateRaw'=>(string)$data['RETURN_ReturnRequestDateRaw'],
			'RETURN_ReturnRequestDate'=>(string)$data['RETURN_ReturnRequestDate'],
			'EXCHANGE_ExchangeReason'=>(string)$data['EXCHANGE_ExchangeReason'],
			'EXCHANGE_ExchangeStatusCode'=>(string)$data['EXCHANGE_ExchangeStatusCode'],
			'EXCHANGE_ExchangeStatus'=>(string)$data['EXCHANGE_ExchangeStatus'],
			'EXCHANGE_ReturnDateRaw'=>(string)$data['EXCHANGE_ReturnDateRaw'],
			'EXCHANGE_ReturnDate'=>(string)$data['EXCHANGE_ReturnDate'],
			'EXCHANGE_ReturnShippingCompany'=>(string)$data['EXCHANGE_ReturnShippingCompany'],
			'EXCHANGE_ReturnTrackingNumber'=>(string)$data['EXCHANGE_ReturnTrackingNumber'],
			'EXCHANGE_ReturnShippingFeeType'=>(string)$data['EXCHANGE_ReturnShippingFeeType'],
			'EXCHANGE_ReceivedDateRaw'=>(string)$data['EXCHANGE_ReceivedDateRaw'],
			'EXCHANGE_ReceivedDate'=>(string)$data['EXCHANGE_ReceivedDate'],
			'EXCHANGE_ResendDateRaw'=>(string)$data['EXCHANGE_ResendDateRaw'],
			'EXCHANGE_ResendDate'=>(string)$data['EXCHANGE_ResendDate'],
			'EXCHANGE_ResendShippingCompany'=>(string)$data['EXCHANGE_ResendShippingCompany'],
			'EXCHANGE_ResendTrackingNumber'=>(string)$data['EXCHANGE_ResendTrackingNumber'],
			'EXCHANGE_Protest'=>(string)$data['EXCHANGE_Protest'],
			'EXCHANGE_ExchangeRequestDateRaw'=>(string)$data['EXCHANGE_ExchangeRequestDateRaw'],
			'EXCHANGE_ExchangeRequestDate'=>(string)$data['EXCHANGE_ExchangeRequestDate'],
			'EXCHANGE_ResendRecipient'=>(string)$data['EXCHANGE_ResendRecipient'],
			'EXCHANGE_ResendRecipientTel'=>(string)$data['EXCHANGE_ResendRecipientTel'],
			'EXCHANGE_ResendShippingAddress'=>(string)$data['EXCHANGE_ResendShippingAddress'],
		);
		if($refDataOrder['ORDER_MallMemberID']) $updateOrderData['ORDER_MallMemberID'] = $refDataOrder['ORDER_MallMemberID'];
		$dataProduct = &$data['product'];
		$updateOrderProductData=array();
		foreach($dataProduct as $eachProduct) {
			$updateOrderProductData[] = array(
				'orderNo'=>(string)$orderNo,
				'seq'=>(string)$eachProduct['seq'],
				'ProductName'=>(string)$eachProduct['ProductName'],
				'ProductID'=>(string)$eachProduct['ProductID'],
				'ProductOption'=>(string)$eachProduct['ProductOption'],
				'Quantity'=>(string)$eachProduct['Quantity'],
				'UnitPrice'=>(string)$eachProduct['UnitPrice'],
				'ReturnRequested'=>(string)$eachProduct['ReturnRequested'],
			);
		}

		if(!(count($updateOrderProductData)>0)) {
			exit;
		}

		$query = $db->_query_print('update gd_navercheckout_order set [cv] where orderNo=[s]',$updateOrderData,$orderNo);
		$db->query($query);

		$cols = $colsupdate = array_keys($updateOrderProductData[0]);
		array_shift($colsupdate); array_shift($colsupdate); // orderNo,seq 값을 뺀다
		$onUpdate = array();
		foreach($cols as $eachCol) {
			$onUpdate[] = "$eachCol = values($eachCol)";
		}
		$onUpdate = implode(',',$onUpdate);

		$query = $db->_query_print('insert into gd_navercheckout_order_product [c] values [vs]',$cols,$updateOrderProductData)." on duplicate key update {$onUpdate}";
		$db->query($query);

		if($data['ORDER_OrderStatusCode']=='OD0037') {
			if(!$this->noEmoney) {
				$this->setEmoney($orderNo); // 적립금 적립
				$this->setCoupon($orderNo); // 쿠폰 지급
			}
		}
		if(in_array($data['ORDER_OrderStatusCode'], array('OD0032', 'OD0033', 'OD0036'))) $this->setEmoney($orderNo, -1); // 적립금 회수

		// 재고삭감관련 체크를 합니다
		if($data['ORDER_OrderStatusCode']=='OD0007') {
			$this->cutStock($orderNo);
		}

		// 재고삭감관련 체크를 합니다
		if(in_array($data['ORDER_OrderStatusCode'],array('OD0003','OD0004','OD0005','OD0006'))) {
			$this->backStock($orderNo);
		}
		if($data['ORDER_OrderStatusCode']) {
			$db->query("INSERT INTO gd_env SET category = 'ncom_api_test', name = '".date("Y-m-d H:i:s")."', value = '".$orderNo." - ".$data['ORDER_OrderStatusCode']."'");
		}
	}


	function SyncInquiry($inquiryNo) {
		$request = array(
			'mode'=>'SyncInquiry',
			'inquiryNo'=>$inquiryNo,
		);

		$this->_httpRequest($request);
		$result = &$this->requestResult;

		if($result['result']) {
			$this->_updateDbInquiry($result['data']);
			return true;
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	function _decrypt($data) {
		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->cryptKey);
		return @unserialize($xxtea->decrypt(base64_decode($data)));
	}

	function _encrypt($data) {
		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->cryptKey);
		return base64_encode($xxtea->encrypt(serialize($data)));
	}

	function _httpRequest($request) {
		$this->error='';
		$requestPost = array(
			'shopNo'=> $this->shopNo,
			'enc'=> $this->_encrypt(&$request),
		);

		$httpSock = new httpSock($this->relayURL,'POST',$requestPost);
		$httpSock->send();

		if(strncmp($httpSock->resContent,'APIRESULT',9)==0) {
			$this->requestResult = iconv_recursive('utf-8','euc-kr',$this->_decrypt(substr($httpSock->resContent,9)));
			return true;
		}
		else {
			$this->error = 'API 통신에 실패했습니다';
			return false;
		}
	}

	function cutStock($orderNo) {
		// 재고연동 기능을 사용을 체크합니다.
		$config = Core::loader('config');
		$config_checkoutapi = $config->load('checkoutapi');
		if($config_checkoutapi['linkStock']!='y') {
			return;
		}

		// 재고관련 FLAG값과 주문아이디를 가지고 옵니다.
		$db = Core::loader('db');
		$orderNo = (int)$orderNo;
		$query = "select stockProcess from gd_navercheckout_order where orderNo='{$orderNo}'";
		$result = $db->_select($query);

		// 이미 재고가 삭감되어있는지 체크합니다.
		if($result[0]['stockProcess']!='none') {
			return;
		}

		$query = $db->_query_print('
			select
				g.goodsno,op.ProductOption,op.Quantity
			from
				gd_navercheckout_order_product as op
				inner join gd_goods as g on op.ProductID = g.goodsno
			where
				op.orderNo=[s] and g.usestock="o"
		',$orderNo);
		$result = $db->_select($query);
		foreach($result as $v) {
			$tmpOption = explode('/',$v['ProductOption']);
			$tmp = explode(':',$tmpOption[0]); $opt1 = $tmp[1];
			$tmp = explode(':',$tmpOption[1]); $opt2 = $tmp[1];

			$query = $db->_query_print(
				'update gd_goods_option set stock = stock - [i] where goodsno=[s] and opt1=[s] and opt2=[s]'
			,(int)$v['Quantity'],(string)$v['goodsno'],(string)$opt1,(string)$opt2);
			$db->query($query);
			$goodsno = $v['goodsno'];
		}
		if($result) {
			$query = $db->_query_print('
				update gd_goods set
					totstock = (select sum(stock) from gd_goods_option where goodsno=[s] and go_is_deleted <> \'1\')
				where goodsno=[s]
			',$goodsno,$goodsno);
			$db->query($query);
		}
		$query = $db->_query_print('update gd_navercheckout_order set stockProcess="done" where orderNo=[s]',$orderNo);
		$db->query($query);
	}

	function backStock($orderNo) {
		$config = Core::loader('config');
		$config_checkoutapi = $config->load('checkoutapi');
		if($config_checkoutapi['linkStock']!='y') {
			return;
		}

		$db = Core::loader('db');
		$orderNo = (int)$orderNo;
		$query = "select stockProcess from gd_navercheckout_order where orderNo='{$orderNo}'";
		$result = $db->_select($query);

		if($result[0]['stockProcess']!='done') {
			return;
		}

		$query = $db->_query_print('
			select
				g.goodsno,op.ProductOption,op.Quantity
			from
				gd_navercheckout_order_product as op
				inner join gd_goods as g on op.ProductID = g.goodsno
			where
				op.orderNo=[s] and g.usestock="o"
		',$orderNo);
		$result = $db->_select($query);
		foreach($result as $v) {
			$tmpOption = explode('/',$v['ProductOption']);
			$tmp = explode(':',$tmpOption[0]); $opt1 = $tmp[1];
			$tmp = explode(':',$tmpOption[1]); $opt2 = $tmp[1];

			$query = $db->_query_print(
				'update gd_goods_option set stock = stock + [i] where goodsno=[s] and opt1=[s] and opt2=[s]'
			,(int)$v['Quantity'],(string)$v['goodsno'],(string)$opt1,(string)$opt2);
			$db->query($query);

			$goodsno = $v['goodsno'];
		}
		if($result) {
			$query = $db->_query_print('
				update gd_goods set
					totstock = (select sum(stock) from gd_goods_option where goodsno=[s] and go_is_deleted <> \'1\')
				where goodsno=[s]
			',$goodsno,$goodsno);
			$db->query($query);
		}
		$query = $db->_query_print('update gd_navercheckout_order set stockProcess="back" where orderNo=[s]',$orderNo);
		$db->query($query);

	}

	/**
	 * 네이버 암호화 처리
	 *
	 * @param string $md 모드(encrypt:암호화,decrypt:복호화)
	 * @param string $tt 대상 문자
	 * @param string $ts timestamp
	 * @return string 결과 (형식: DONE|||결과값, ERRO|||에러사유)
	 */
	function ncCrypt($md, $tt, $ts='')
	{
		if (class_exists('NHNAPISCL', false) === false) {
			$incPath = dirname(__FILE__);
			ini_set('include_path',"$incPath/pear:$incPath/nhnlib");
			include 'nhnapi-simplecryptlib.php';
		}
		if (is_a($this->nc, 'NHNAPISCL') === false) {
			$this->nc = new NHNAPISCL();
		}

		$md = trim($md); // mode
		$tt = trim($tt); // text
		$ck = trim($this->checkoutCfg['connectId']); // cert key
		$ts = (trim($ts) != '') ? trim($ts) : $this->nc->getTimestamp(); // timestamp

		if(!$md) { return 'ERRO|||처리타입이 전달되지 않았습니다.'; }
		if(!$tt) { return 'ERRO|||암/복호화 할 문자열이 전달되지 않았습니다.'; }
		if(!$ck) { return 'ERRO|||키 값이 전달되지 않았습니다.'; }

		switch($md) {
			case 'encrypt' :
				$secret = $this->nc->generateKey($ts, $ck);
				$rtnVal = $this->nc->encrypt($secret, $tt);

				return $rtnVal;
				break;

			case 'decrypt' :
				$secret = $this->nc->generateKey($ts, $ck);
				$rtnVal = $this->nc->decrypt($secret, $tt);

				if($rtnVal) $rtnVal = 'DONE|||'.$rtnVal;
				else $rtnVal = 'ERRO|||결과값이 없습니다.';

				return $rtnVal;
				break;
		}
	}

	/*
		네이버 체크아웃 회원 확인 - Using in '/shop/proc/indb.naver.php'
	*/
	function CompareMember($MallUserSSN,$MallUserName,$NCUserNo)
	{
		$request = array(
			'mode'			=> 'CompareMember',
			'MallUserSSN'	=> $MallUserSSN,
			'MallUserName'	=> iconv("EUC-KR", "UTF-8", $MallUserName),
			'NCUserNo'		=> $NCUserNo,
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;

		if($result['result']) {
			if($result['data']['IdenticalPerson'] == true) return "Y";
			else return "N";
		}
		else {
			$this->error=$result['error'];
			return false;
		}
	}

	/**
	 * 쇼핑몰회원확인>가맹점 회원 정보 제공 동의 철회(이용자가 동의를 철회하거나 탈퇴시)
	 *
	 * @param string $MallUserID 가맹점 회원 아이디
	 * @param string $MallUserNo 가맹점 회원 고유 번호
	 */
	function CancelMallUserAgreement($MallUserID, $MallUserNo)
	{
		$request = array(
			'mode'			=> 'CancelMallUserAgreement',
			'MallUserID'	=> iconv('euc-kr','utf-8',$MallUserID),
			'MallUserNo'	=> iconv('euc-kr','utf-8',$MallUserNo),
			'NCMallID'		=> iconv('euc-kr','utf-8',$this->checkoutCfg['naverId']),
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if ($result['result'] === false) {
			$this->error = preg_replace('/Transaction ID.*$/is','',$result['error']);
			$this->errorCode = $result['errorCode'];
		}
		return ($result['result'] ? true : false);
	}

	/**
	 * 원클릭>가맹점 회원 가입 완료 알림
	 *
	 * @param string $MallUserID 가맹점 회원 아이디
	 * @param string $MallUserNo 가맹점 회원 고유 번호
	 * @param string $NCUserNo 네이버 회원 고유 번호
	 * @param string $Timestamp timestamp
	 */
	function JoinComplete($MallUserID, $MallUserNo, $NCUserNo, $Timestamp)
	{
		// NCUserNo 복호화 & EUC-KR로 변환
		$temp_ar = explode('|||', $this->ncCrypt('decrypt',$NCUserNo,$Timestamp));
		if($temp_ar[0] == "ERRO") $NCUserNo = '';
		else $NCUserNo = $temp_ar[1];

		$request = array(
			'mode'			=> 'JoinComplete',
			'MallUserID'	=> iconv('euc-kr','utf-8',$MallUserID),
			'MallUserNo'	=> iconv('euc-kr','utf-8',$MallUserNo),
			'NCUserNo'		=> $NCUserNo,
			'NCMallID'		=> iconv('euc-kr','utf-8',$this->checkoutCfg['naverId']),
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if ($result['result'] === false) {
			$this->error = preg_replace('/Transaction ID.*$/is','',$result['error']);
		}
		return ($result['result'] ? true : false);
	}

	/**
	 * 원클릭>가맹점 회원 탈퇴 완료 알림(탈퇴시)
	 *
	 * @param string $MallUserID 가맹점 회원 아이디
	 * @param string $MallUserNo 가맹점 회원 고유 번호
	 */
	function LeaveMallUser($MallUserID, $MallUserNo)
	{
		$request = array(
			'mode'			=> 'LeaveMallUser',
			'MallUserID'	=> iconv('euc-kr','utf-8',$MallUserID),
			'MallUserNo'	=> iconv('euc-kr','utf-8',$MallUserNo),
			'NCMallID'		=> iconv('euc-kr','utf-8',$this->checkoutCfg['naverId']),
		);

		if(!$this->_httpRequest($request)) {
			return false;
		}
		$result = &$this->requestResult;
		if ($result['result'] === false) {
			$this->error = preg_replace('/Transaction ID.*$/is','',$result['error']);
		}
		return ($result['result'] ? true : false);
	}

	/**
	 * 쇼핑몰회원확인>가맹점회원상태조회, 원클릭>가맹점회원중복조회 응답 XML
	 *
	 * @param string $ResponseType 성공여부(SUCCESS/ERROR)
	 * @param string $ErrorMessage 오류 메시지
	 * @param string $MallUserStatus 회원상태(VALID:회원존재, INVALIID:회원미존재
	 */
	function memberStatusXML($ResponseType, $ErrorMessage, $MallUserStatus='')
	{

		$res = '
		<'.'?xml version="1.0" encoding="UTF-8" standalone="no"?'.'>
		<Response>
		<ResponseType>'.iconv('EUC-KR', 'UTF-8', $ResponseType).'</ResponseType>
		<ErrorMessage>'.iconv('EUC-KR', 'UTF-8', $ErrorMessage).'</ErrorMessage>
		<MallUserStatus>'.iconv('EUC-KR', 'UTF-8', $MallUserStatus).'</MallUserStatus>
		</Response>
		';
		echo $res;

		$this->ncLog('onclick_status', 'ResponseType => '.$ResponseType.', ErrorMessage => '.$ErrorMessage.', MallUserStatus => '.$MallUserStatus);
		$this->ncLog('onclick_status', "END");
		exit;
	}

	/**
	 * 주문서의 상품별 적립금 계산
	 *
	 * @param integer $orderNo 네이버 주문서 DB 주문번호
	 */
	function setOrderEmoney($orderNo)
	{
		$db = Core::loader('db');
		@include_once dirname(__FILE__)."/../conf/config.pay.php";

		// 주문정보 읽기
			$query = "SELECT * FROM gd_navercheckout_order WHERE orderNo = '$orderNo'";
			$data = $db->fetch($query);

		// 적립금을 지급하기 위한 조건 확인
			if($set['emoney']['useyn'] == 'n') return; // 적립금 사용여부
			elseif($set['emoney']['limit'] == 1 && $data['ORDER_CashbackDiscountAmount']) return; //적립금 결제시 상품 적립금 미지급
			elseif(!$data['ORDER_MallMemberID']) return; // 회원정보 유무
			else {
				// 적립금 계산
					$query = "SELECT op.ProductID, op.ProductOption, op.Quantity, op.UnitPrice, g.use_emoney FROM gd_navercheckout_order_product AS op LEFT JOIN ".GD_GOODS." AS g ON op.ProductID = g.goodsno WHERE op.orderNo = '$orderNo' AND op.ReturnRequested = 'n'";
					$result = $db->query($query);
					while($opData = $db->fetch($result)) {
						// 옵션 추출
							$tmpOption = explode('/', $opData['ProductOption']);
							$tmp = explode(':', $tmpOption[0]); $opt1 = $tmp[1];
							$tmp = explode(':', $tmpOption[1]); $opt2 = $tmp[1];

						if($opData['use_emoney'] == "1") {
							list($reserve) = $db->fetch("SELECT reserve FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$opData['ProductID']."' AND opt1 = '$opt1' AND opt2 = '$opt2' and go_is_deleted <> '1' and go_is_display = '1' ");
							$productReserve = $reserve * $opData['Quantity']; // 구입 수량만큼
						}
						else {
							if($set['emoney']['goods_emoney']) {
								if($set['emoney']['chk_goods_emoney'] == "0") {
									$productReserve = (($opData['UnitPrice'] / 100 ) * $set['emoney']['goods_emoney']) * $opData['Quantity'];
								}
								else {
									$productReserve = $set['emoney']['goods_emoney'] * $opData['Quantity'];
								}
							}
							else $productReserve = 0;
						}
						// 적립금 DB에 저장
						$db->query("UPDATE gd_navercheckout_order_product SET emoney = '$productReserve' WHERE orderNo = '$orderNo' AND ProductID = '".$opData['ProductID']."' AND ProductOption = '".$opData['ProductOption']."'");
					}
			}
	}

	/**
	 * 적립금 적립
	 *
	 * @param integer $orderNo 네이버 주문서 DB 주문번호
	 * @param integer $mode 적립:1, 회수:-1
	 */
	function setEmoney($orderNo, $mode=1) {
		$db = Core::loader('db');

		$totalEmoney = 0;

		// 취소하지 않은 주문의 상품을 읽기
		if($mode > 0) $query = "SELECT emoney FROM gd_navercheckout_order_product WHERE orderNo = '$orderNo' AND ReturnRequested = 'n'";
		// 취소한 주문의 상품을 읽기
		else $query = "SELECT emoney FROM gd_navercheckout_order_product WHERE orderNo = '$orderNo' AND ReturnRequested = 'y'";

		$result = $db->query($query);
		while($opData = $db->fetch($result)) {
			$totalEmoney = $totalEmoney + $opData['emoney']; // 해당 주문에 대한 각 상품의 적립금 합산
		}

		$totalEmoney = $totalEmoney * $mode;

		if(!$totalEmoney) return;

		// 로그를 작성하기 위한 정보
			$msg = ($mode > 0) ? "구매완료로 인해 구매적립금 적립 - 네이버 체크아웃" : "구매취소로 인해 구매적립금 환원 - 네이버 체크아웃";
			list($ORDER_MallMemberID) = $db->fetch("SELECT ORDER_MallMemberID FROM gd_navercheckout_order WHERE orderNo = '$orderNo'");
			if(!$ORDER_MallMemberID) return;
			list($m_no) = $db->fetch("SELECT m_no FROM ".GD_MEMBER." WHERE m_id = '$ORDER_MallMemberID'");

			$dormantMember = false;
			$dormant = Core::loader('dormant');
			$dormantMember = $dormant->checkDormantMember(array('m_no'=>$m_no), 'm_no');

		// 적립금 적용 및 로그 작성
			if($dormantMember === true){
				$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($m_no, $totalEmoney);
				$db->query($dormantEmoneyQuery);
			}
			else {
				$db->query("UPDATE ".GD_MEMBER." SET emoney = emoney + $totalEmoney WHERE m_no = '$m_no'");
			}

			$db->query("INSERT INTO ".GD_LOG_EMONEY." SET
				m_no	= '$m_no',
				ordno	= '$orderNo',
				emoney	= '$totalEmoney',
				memo	= '$msg',
				regdt	= NOW()
			");

		// 환불을 한 상품의 경우는 적립금을 0으로 수정
		if($mode < 0) $db->query("UPDATE gd_navercheckout_order_product SET emoney = '0' WHERE orderNo = '$orderNo' AND ReturnRequested = 'y'");
	}

	/**
	 * 구매완료 쿠폰 발급
	 *
	 * @param integer $orderNo 네이버 주문서 DB 주문번호
	 */
	function setCoupon($orderNo) {
		$db = Core::loader('db');

		$query = "SELECT m.m_no FROM gd_navercheckout_order AS op LEFT JOIN gd_member AS m ON op.ORDER_MallMemberID = m.m_id WHERE op.orderNo = '$orderNo'";
		list($m_no) = $db->fetch($query);

		if($m_no) {
			$query = "SELECT ProductID FROM gd_navercheckout_order_product WHERE orderNo = '$orderNo'";
			$res = $db->query($query);
			while($tmp = $db->fetch($res)) $arr_goodsno[] = $tmp['ProductID'];

			$query = "SELECT category, CHAR_LENGTH(category) clen FROM ".GD_GOODS_LINK." WHERE hidden = 0 AND goodsno IN (".implode(',',$arr_goodsno).")";
			$res = $db->query($query);
			while($tmp = $db->fetch($res)) for($i = 3; $i <= $tmp['clen']; $i += 3) $arrCategory[] = "'".substr($tmp['category'], 0, $i)."'";
			if(count($arrCategory) > 0)$arrCategory = array_unique($arrCategory);
			else $arrCategory = array();

			$query	=	"SELECT a.*
						FROM
							".GD_COUPON." a
							LEFT JOIN ".GD_COUPON_CATEGORY." b ON a.couponcd = b.couponcd
							LEFT JOIN ".GD_COUPON_GOODSNO." c ON a.couponcd = c.couponcd
						WHERE a.coupontype = 3
							AND ((a.sdate <= '".date("Y-m-d H:i:s")."' AND a.edate >= '".date("Y-m-d H:i:s")."' AND a.priodtype='0') OR a.priodtype='1')
							AND (((b.category in(".implode(',',$arrCategory).") OR c.goodsno in (".implode(',',$arr_goodsno).")) AND a.goodstype='1') OR a.goodstype='0')";

			$res = $db->query($query);
			$i=0;

			while($data = $db->fetch($res)){
				$query = "select a.sno from ".GD_COUPON_APPLY." a left join ".GD_COUPON_APPLYMEMBER." b on a.sno=b.applysno where a.couponcd='$data[couponcd]' and b.m_no = '$m_no' order by a.regdt desc limit 1";
				list($applysno) = $db->fetch($query);
				$query = "select count(*) from ".GD_COUPON_ORDER." where applysno='$applysno' and m_no = '$m_no'";
				list($cnt) = $db->fetch($query);

				if(!$applysno){
					$newapplysno = new_uniq_id('sno',GD_COUPON_APPLY);
					$query = "INSERT INTO ".GD_COUPON_APPLY." SET
								sno				= '$newapplysno',
								couponcd		= '$data[couponcd]',
								membertype		= '2',
								member_grp_sno  = '',
								regdt			= now()";
					$db->query($query);
					$query = "insert into ".GD_COUPON_APPLYMEMBER." set m_no='$m_no', applysno ='$newapplysno'";
					$db->query($query);
				}else if($cnt == 0){
					$query = "update ".GD_COUPON_APPLY." set regdt=now() where sno='$applysno'";
					$db->query($query);
				}
			}
		}
	}

	/**
	 * 로그 저장
	 *
	 * @param string $type 타입(구분키)
	 * @param string $msg 기록데이터
	 */
	function ncLog($type,$msg)
	{
		if ( $msg == 'START' ) {
			$msg = 'INFO [__datetime__] <__type__> START'.chr(10);
			$msg .= 'DEBUG [__datetime__] <__type__> Connect IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
			$msg .= 'DEBUG [__datetime__] <__type__> Request URL : '.$_SERVER['REQUEST_URI'];
		}
		else if ( $msg == 'END' ) {
			$msg = 'INFO [__datetime__] <__type__> END';
		}
		else {
			$msg = 'DEBUG [__datetime__] <__type__> Msg : '.$msg;
		}
		$msg = str_replace( array('__datetime__', '__type__'), array(date('Y-m-d_H:i:s:B'), $type), $msg ).chr(10);
		error_log($msg, 3, dirname(__FILE__).'/../log/naverCheckout/nc_'.date('Ymd').'.log');
		@chmod( $tmp, 0707 );
	}
}

?>
