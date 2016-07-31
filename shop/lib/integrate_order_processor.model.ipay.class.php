<?
require_once dirname(__FILE__)."/auctionIpay.service.class.php";

class integrate_order_processor_ipay extends integrate_order_processor {



	function extractData($var = null) {
		/*
		 * 주문통합 이전까지 주문 번호를 수집하지 않았으므로, old_ordno 필드를 이용하여 ipayno 를 주문번호를 업데이트 한다.
		 * 수정 : 주문번호가 결제번호당 여러개 이므로, 결제번호를 주문번호 대신으로 사용합니다.
		 */
		// 주문데이터
		$query = "
			INSERT
			INTO ".$this->temp_table_order."
			(
				channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,
				reg_date,mod_date
			)

			SELECT
				'ipay',B.auctionpayno,'','','','','','','','','','','','',B.payprice,'','','',B.shippingprice,'','','','',IF(B.orderdate,B.orderdate,B.regdt ),'','','','','',B.paymenttype,'','','','','','',
				NOW(), null

			FROM ".GD_AUCTIONIPAY." AS B

			WHERE
				B.sync_ = 0 AND B.auctionpayno IS NOT NULL
		";
		$this->db->query($query);

		if ($this->db->affected() < 1)
			return false;

		// 주문 상품 정보
		$query = "
			INSERT
			INTO ".$this->temp_table_item."
			(
				channel,ordno,goodsnm,goodsno,`option`,ea,price,
				cs
			)
			SELECT
				'ipay', B.ordno,C.goodsnm,C.goodsno,C.option,C.ea,C.price,
				IF (C.responsetype = 1, 'f','n')
			FROM ".$this->temp_table_order." AS B

			INNER JOIN ".GD_AUCTIONIPAY." AS A
				ON B.ordno = A.auctionpayno

			INNER JOIN ".GD_AUCTIONIPAY_ITEM." AS C
				ON A.ipaysno = C.ipaysno
		";

		$this->db->query($query);

		// 레코드 값 조정
		$this->adjustData();

		return true;
	}

	function adjustData() {

		$api = $this->getApiInstance();

		$query = "SELECT O.ordno, SUM(OI.price * OI.ea) AS ord_amount FROM ".$this->temp_table_order." AS O INNER JOIN ".$this->temp_table_item." AS OI ON O.ordno = OI.ordno GROUP BY O.ordno";

		$rs = $this->db->query($query);

		while ($row = $this->db->fetch($rs,1)) {

			$data = array(
				'DurationType'=>'Nothing',
				'SearchType'=>'PayNo',
				'SearchValue'=>$row['ordno']
			);

			$ip = $api->request('GetIpayPaidOrderList',$data);
			$order = $ip['GetOrderListResponseT'];
			if (empty($order)) continue;
			if (isset($order[0]) && isset($order[1])) $order = $order[0];

			$query = "
			UPDATE ".$this->temp_table_order."
			SET
				ordno			= '$order[PayNo]',
				m_id_out		= '$order[BuyerId]',
				ord_name		= '".$this->db->_escape($order['BuyerName'])."',
				rcv_name		= '".$this->db->_escape($order['BuyerName'])."',
				rcv_phone		= '".$this->db->_escape($order['DistTel'])."',
				rcv_mobile		= '".$this->db->_escape($order['DistMobileTel'])."',
				rcv_zipcode		= '".$this->db->_escape($order['DistPostNo'])."',
				rcv_address		= '".$this->db->_escape($order['DistAddressPost'].' '.$order['DistAddressDetail'])."',
				ori_status		= '".$this->db->_escape($order['OrderStatus'])."',
				ord_amount		= '$row[ord_amount]'

			WHERE ordno = $order[PayNo]
			";
			$this->db->query($query);

		}


		$_tmp = array(
						0	=> array('입금확인중'),				// 주문접수
						1	=> array('결제완료'),	// 입금확인 (반드시 공백으로 입력)
						2	=> array('배송준비중'),	// 배송준비중
						3	=> array('배송중'),		// 배송중
						4	=> array('배송완료','구매결정대기','송금완료','거래완료'),	// 배송완료(판매완료, 송금완료 등등등)

						// 취소
						10	=> array(),	// 신청,접수,진행중,등등 완료가 아닌 경우
						11	=> array('취소완료','구매거부','미입금구매취소'),	// 완료

						// 환불
						20	=> array(),	// 신청,접수,진행중,등등 완료가 아닌 경우
						21	=> array('환불완료'),	// 완료

						// 반품
						30	=> array(),	// 신청,접수,진행중,등등 완료가 아닌 경우
						31	=> array(),		// 완료

						// 교환
						40	=> array(),	// 신청,접수,진행중,등등 완료가 아닌 경우
						41	=> array(),	// 완료

						// 결제오류
						50	=> array(),
						51	=> array(),
						54	=> array()
		);

		// 주문상태 조정
		foreach ($_tmp as $_status => $_cond) {

			if (empty($_cond)) continue;

			$_cond = array_map(create_function('$var','return "\'".$var."\'";'), $_cond);

			$query = "
			UPDATE ".$this->temp_table_order." SET
				ord_status = $_status
			WHERE ori_status IN (".implode(',',$_cond).")
			";
			$this->db->query($query);
		}

		// 기타 등등 조정
		$query = "
			UPDATE ".$this->temp_table_order." SET
				old_ordno = '',
				pay_method = IF(pay_method = 'A','a',
							 IF(pay_method = 'C','c',
							 IF(pay_method = 'M','h',
							 IF(pay_method = 'D','o',''))))
		";
		$this->db->query($query);

	}

	function setSyncComplete() {

		if ($this->update('ipay')) {

			$this->db->query("
				UPDATE ".GD_AUCTIONIPAY." AS A
				INNER JOIN ".$this->temp_table_order." AS B
				ON A.auctionpayno = B.ordno

				SET A.sync_ = 1,A.uptdt_ = '".date('Y-m-d H:i:s',$this->now)."'
			");

			$this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);

		}
	}

	// ipay api 객체 리턴.
	function &getApiInstance() {
        static $ins = null;

        if ($ins === null)
            $ins = & new auctionIpayService();

        return $ins;

	}

	function _execApi($method, $data) {
		$api = $this->getApiInstance();
		return $api->request($method, $data);
	}

	// 주문 처리 관련 메서드 (사용하지 않거나 지원되지 않는 메서드는 삭제해도 무방함)
	function setOrderDeliveryReady($ordno) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'IpayConfirmReceivingOrder';

		foreach ($orders as $order) {

			$data = array(
				'OrderNo'=>$order['OrderNo']
			);

			$this->_execApi($method, $data);
		}

		return true;

	}

	function setOrderDelivery($ordno, $extra) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'DoIpayShippingGeneral';

		foreach ($orders as $order) {

			$data = array(
				'OrderNo'=>$order['OrderNo'],
				'SendDate'=>date('Y-m-d'),
				'InvoiceNo'=>$extra['dlv_no'],
				'MessageForBuyer'=>'',
				'ShippingMethodClassficationType'=>'Door2Door',
				'DeliveryAgency'=>$extra['dlv_company'],
				'ShippingEtcMethod'=>'Nothing',
				'ShippingEtcAgencyName'=>'',
			);

			$this->_execApi($method, $data);
		}

		return true;

	}

    function setOrderCancelFin($ordno,$extra) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'DoIpayShippingGeneral';

		foreach ($orders as $order) {

			if ($extra['reject'] == 1) {
				// 판매거부
				$method = 'IpayDenySell';
				$param = array(
					'ItemID' => $order['ItemNo'],
					'OrderNo' => $order['OrderNo'],
					'DenySellReason' => $extra['cs_reason_code']
				);
			}
			else {
				// 취소 승인
				$method = 'IpayConfirmCancelApprovalList';
				$param = array(
					'BuyerID' => $order['BuyerId'],	// 취소 클레임번호
					'OrderNo' => $order['OrderNo']	// 주문번호
				);
			}

			$this->_execApi($method, $param);

		}

		return true;


    }

    function setOrderReturnFin($ordno,$extra) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'DoIpayReturnApproval';

		foreach ($orders as $order) {

			$param = array(
				'OrderNo' => $order['OrderNo']				// 주문번호
			);

			$this->_execApi($method, $param);

		}

		return true;

    }

	function setOrderExchangeFin($ordno,$extra) {

		// 재발송 해야 하므로 옥션 판매관리에서 처리하도록 안내.

	}

	/*
	 * 결제수단별 결제정보 및 가상계좌조회 정보를 받아 옵니다.
	 */
	function GetIpayAccountNumb($ordno){

		$method = 'GetIpayAccountNumb';
		$data = array(
			'payNo'	=>	$ordno
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * 구매결정되어 송금이 완료된 주문건에 대해 환불처리합니다.
	 */
	function DoIpayOrderDecisionCancel($ordno){

		$method = 'DoIpayOrderDecisionCancel';
		$data = array(
			'OrderNo'	=>	$ordno,
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * 판매거부.
	 */
	function IpayDenySell($itemId, $orderNo, $enamooCancelReasonCode){
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$enamooCancelReason = array(
			'1' => 6,
			'2' => 1,
			'3' => 6,
			'4' => 6,
			'5' => 6,
			'6' => 6,
			'7' => 6,
			'8' => 2,
			'9' => 6,
			'10' => 6
		);
		$denySellReason = array(
			0 => 'LowerThanWishPrice',	// 결제가격이 상품가격보다 적음
			1 => 'RunOutOfStock',	// 재고부족
			2 => 'ManufacturingDefect',	// 제품결함
			3 => 'SoldToOtherBuyer',	// 재고부족(다른구매자가 구매)
			4 => 'SellToOtherDitstributionChannel',	// 재고부족(다른 판매채널에서 구매)
			5 => 'UnreliableBuyer',	// 구매자를 신뢰할수 없음
			6 => 'OtherReason'	// 기타사유
		);
		$method = 'IpayDenySell';
		$data = array(
			'SellerID'			=>	$auctionIpayCfg['sellerid'],
			'ItemID'			=>	$itemId,
			'OrderNo'			=>	$orderNo,
			'DenySellReason'	=>	$denySellReason[$enamooCancelReason[$enamooCancelReasonCode]]
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * 발주확인.
	 */
	function IpayConfirmReceivingOrder($OrderNo)
	{
		$method = 'IpayConfirmReceivingOrder';
		$data = array(
			'OrderNo'	=>	$OrderNo,
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * 개별발송.
	 */
	function DoIpayShippingGeneral($OrderNo, $deliveryNo, $invoiceNo)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayShippingGeneral';
		$deliveryAgency = array(
			'37' => 'etc',
			'38' => 'etc',
			'15' => 'cjgls',
			'36' => 'etc',
			'35' => 'etc',
			'1' => 'kgbls',
			'2' => 'ktlogistics',
			'23' => 'etc',
			'24' => 'nothing',
			'39' => 'kyungdong',
			'3' => 'etc',
			'34' => 'etc',
			'27' => 'nedex',
			'26' => 'etc',
			'33' => 'daesin',
			'4' => 'korex',
			'29' => 'korex',
			'21' => 'dongbu',
			'25' => 'ajutb',
			'7' => 'ajutb',
			'5' => 'etc',
			'17' => 'sagawa',
			'6' => 'hth',
			'31' => 'etc',
			'16' => 'sedex',
			'8' => 'yellow',
			'30' => 'epost',
			'18' => 'epost',
			'9' => 'epost',
			'100' => 'epost',
			'32' => 'innogis',
			'10' => 'etc',
			'22' => 'ilyang',
			'28' => 'etc',
			'19' => 'chonil',
			'11' => 'etc',
			'20' => 'hanaro',
			'12' => 'hanjin',
			'13' => 'hyundai',
			'14' => 'etc'
		);

		$deliveryAgencyName = array(
			'37' => 'ACI Express',
			'38' => 'AirBoyExpress',
			'15' => 'CJ GLS(HTH통합)',
			'36' => 'CVSnet',
			'35' => 'GSM 국제택배',
			'1' => 'KGB택배',
			'2' => 'KT로지스',
			'23' => 'WIZWA',
			'24' => '[자체배송]',
			'39' => '경동택배',
			'3' => '고려택배',
			'34' => '나이트맨',
			'27' => '네덱스',
			'26' => '다젠',
			'33' => '대신택배',
			'4' => '대한통운',
			'29' => '대한통운(미국상사)',
			'21' => '동부익스프레스',
			'25' => '동원로엑스-동원택배',
			'7' => '동원로엑스-로엑스택배(구:아주)',
			'5' => '로젠택배',
			'17' => '사가와 익스프레스',
			'6' => '삼성택배HTH(구)',
			'31' => '스피디익스프레스',
			'16' => '신세계(쎄덱스)',
			'8' => '앨로우캡',
			'30' => '우체국EMS',
			'18' => '우체국등기',
			'9' => '우체국택배',
			'100' => '우체국택배(연동)',
			'32' => '이노지스택배',
			'10' => '이젠택배',
			'22' => '일양택배',
			'28' => '조이익스프레스',
			'19' => '천일택배',
			'11' => '트라넷',
			'20' => '하나로택배',
			'12' => '한진택배',
			'13' => '현대택배',
			'14' => '훼미리택배'
		);

		$data = array(
			'SellerID'							=> $auctionIpayCfg['sellerid'],
			'OrderNo'							=> $OrderNo,
			'RemittanceMethodType'				=> 'Emoney',
			'SendDate'							=> date('Y-m-d'),
			'InvoiceNo'							=> $invoiceNo,
			'MessageForBuyer'					=> '',
			'ShippingMethodClassficationType'	=> 'Door2Door',
			//'DeliveryAgency'					=> $deliveryAgency[$deliveryNo],
			'DeliveryAgency'					=> 'etc',
			'DeliveryAgencyName'				=> $deliveryAgencyName[$deliveryNo],
			'ShippingEtcMethod'					=> 'Nothing',
			'ShippingEtcAgencyName'				=> ''
		);
		return $this->_execApi($method,$data);
	}

	function DoIpayReturnRequestBySeller($orderNo, $enamooCancelReasonCode, $returnReasonDetail)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayReturnRequestBySeller';

		$returnBySellerReasonCode = array(
			'1' => 'ChangeOfMind',
			'2' => 'GoodsFault',
			'3' => 'GoodsFault',
			'4' => 'GoodsFault',
			'5' => 'GoodsFault',
			'6' => 'GoodsFault',
			'7' => 'GoodsFault',
			'8' => 'GoodsFault',
			'9' => 'GoodsFault',
			'10' => 'ChangeOfMind'
		);

		$data = array(
			'SellerID'							=> $auctionIpayCfg['sellerid'],
			'OrderNo'							=> $orderNo,
			'ReturnBySellerDeliveryMethod'		=> 'Buyer',
			'ReturnBySellerReasonCode'			=> $returnBySellerReasonCode[$enamooCancelReasonCode],
			'ReturnReasonDetail'				=> $returnReasonDetail
		);
		return $this->_execApi($method,$data);
	}

	function DoIpayReturnApproval($orderNo)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayReturnApproval';

		$data = array(
			'SellerID'							=> $auctionIpayCfg['sellerid'],
			'OrderNo'							=> $orderNo
		);
		return $this->_execApi($method,$data);
	}

	function GetIpayAgreementStatus($ipayCartNo, $ipayItemNo)
	{
		$method = 'GetIpayAgreementStatus';
		$data = array(
			'ipayCartNo'	=> $ipayCartNo,
			'ipayItemNo'	=> $ipayItemNo
		);
		return $this->_execApi($method,$data);
	}

	function GetIpayReceiptStatus($ipayCartNo, $ipayItemNo)
	{
		$method = 'GetIpayReceiptStatus';
		$data = array(
			'ipayCartNo'	=> $ipayCartNo,
			'ipayItemNo'	=> $ipayItemNo
		);

		return $this->_execApi($method,$data);
	}

	function IpayChangeShippingType($orderNo, $deliveryNo, $invoiceNo)
	{
		$method = 'IpayChangeShippingType';

		$deliveryAgencyName = array(
			'37' => 'ACI Express',
			'38' => 'AirBoyExpress',
			'15' => 'CJ GLS(HTH통합)',
			'36' => 'CVSnet',
			'35' => 'GSM 국제택배',
			'1' => 'KGB택배',
			'2' => 'KT로지스',
			'23' => 'WIZWA',
			'24' => '[자체배송]',
			'39' => '경동택배',
			'3' => '고려택배',
			'34' => '나이트맨',
			'27' => '네덱스',
			'26' => '다젠',
			'33' => '대신택배',
			'4' => '대한통운',
			'29' => '대한통운(미국상사)',
			'21' => '동부익스프레스',
			'25' => '동원로엑스-동원택배',
			'7' => '동원로엑스-로엑스택배(구:아주)',
			'5' => '로젠택배',
			'17' => '사가와 익스프레스',
			'6' => '삼성택배HTH(구)',
			'31' => '스피디익스프레스',
			'16' => '신세계(쎄덱스)',
			'8' => '앨로우캡',
			'30' => '우체국EMS',
			'18' => '우체국등기',
			'9' => '우체국택배',
			'100' => '우체국택배(연동)',
			'32' => '이노지스택배',
			'10' => '이젠택배',
			'22' => '일양택배',
			'28' => '조이익스프레스',
			'19' => '천일택배',
			'11' => '트라넷',
			'20' => '하나로택배',
			'12' => '한진택배',
			'13' => '현대택배',
			'14' => '훼미리택배'
		);

		$data = array(
			'OrderNo'						=> $orderNo,
			'ShippingMethodClassfication'	=> 'Door2Door',
			'DeliveryAgency'				=> '0',
			'DeliveryAgencyName'			=> $deliveryAgencyName[$deliveryNo],
			'ShippingEtcMethod'				=> 'Nothing',
			'ShippingEtcAgencyName'			=> '',
			'InvoiceNo'						=> $invoiceNo,
			'MessageForBuyer'				=> ''
		);
		return $this->_execApi($method,$data);
	}

	function DoIpayOrderDecisionRequest($orderNo)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayOrderDecisionRequest';

		$data = array(
			'SellerID'               => $auctionIpayCfg['sellerid'],
			'OrderNo'                => $orderNo,
			'SellerManagementNumber' => '',
			'RequestReason'          => ''
		);
		return $this->_execApi($method,$data);
	}
}
?>