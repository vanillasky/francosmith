<?
class integrate_order_processor_checkout extends integrate_order_processor {

	function extractData($var = null) {

		// 주문 정보
		// 발주 상태(배송준비중) 를 체크하기 위해 추가 필드를 생성해야 하나, ord_status 필드를 활용합니다.
		// 즉, ori_status 와 ord_status 필드 두곳을 활용하여 주문 상태를 갱신 처리 합니다.
		$query = "
			INSERT
			INTO ".$this->temp_table_order."
			(
				channel,ordno,ord_date,m_id_out,ord_name,ord_phone,ord_mobile,pay_date,pay_method,
				m_no,dlv_date,fin_date,dlv_no,dlv_company,dlv_amount,dlv_message,dlv_method,
				rcv_name, rcv_phone, rcv_mobile, rcv_zipcode, rcv_address,
				reg_date,mod_date,
				cs_type,
				cs_regdt,cs_confirmdt,cs_reason,cs_reason_type,

				ord_amount,pay_amount,dis_amount,

				ori_status,ord_status
			)
			SELECT
				'checkout',O.OrderID,O.OrderDate,O.OrdererID,O.OrdererName,O.OrdererTel1,O.OrdererTel2,O.PaymentDate,O.PaymentMeans,
				MB.m_no,D.SendDate,D.DeliveredDate,D.TrackingNumber,D.DeliveryCompany ,PO.DeliveryFeeAmount ,PO.ShippingMemo,D.DeliveryMethod,
				PO.ShippingAddressName,PO.ShippingAddressTel1,PO.ShippingAddressTel2,PO.ShippingAddressZipCode,CONCAT(PO.ShippingAddressBaseAddress,' ',PO.ShippingAddressDetailedAddress),
				NOW(), null,
				PO.ClaimType,
				IF(PO.ClaimType = 'CANCEL',						C.ClaimRequestDate,
				IF(PO.ClaimType = 'RETURN',						R.ClaimRequestDate,
				IF(PO.ClaimType = 'EXCHANGE',					E.ClaimRequestDate,
				IF(PO.ClaimType = 'PURCHASE_DECISION_HOLDBACK', DH.ClaimRequestDate,
				IF(PO.ClaimType = 'ADMIN_CANCEL',				C.ClaimRequestDate,
				''))))),
				IF(PO.ClaimType = 'CANCEL',						C.CancelCompletedDate,
				IF(PO.ClaimType = 'RETURN',						R.CollectCompletedDate,
				IF(PO.ClaimType = 'EXCHANGE',					E.CollectCompletedDate,
				IF(PO.ClaimType = 'PURCHASE_DECISION_HOLDBACK', '',
				IF(PO.ClaimType = 'ADMIN_CANCEL',				C.CancelCompletedDate,
				''))))),
				IF(PO.ClaimType = 'CANCEL',						C.CancelDetailedReason,
				IF(PO.ClaimType = 'RETURN',						R.ReturnDetailedReason,
				IF(PO.ClaimType = 'EXCHANGE',					E.ExchangeDetailedReason,
				IF(PO.ClaimType = 'PURCHASE_DECISION_HOLDBACK', DH.DecisionHoldbackDetailedReason,
				IF(PO.ClaimType = 'ADMIN_CANCEL',				C.CancelDetailedReason,
				''))))),
				IF(PO.ClaimType = 'CANCEL',						C.CancelReason,
				IF(PO.ClaimType = 'RETURN',						R.ReturnReason,
				IF(PO.ClaimType = 'EXCHANGE',					E.ExchangeReason,
				IF(PO.ClaimType = 'PURCHASE_DECISION_HOLDBACK', DH.DecisionHoldbackReason,
				IF(PO.ClaimType = 'ADMIN_CANCEL',				C.CancelDetailedReason,
				''))))),
				SUM(PO.Quantity * PO.UnitPrice), SUM(PO.TotalPaymentAmount), SUM(PO.ProductDiscountAmount),
				PO.ProductOrderStatus,PO.PlaceOrderStatus

			FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO

			INNER JOIN ".GD_NAVERCHECKOUT_ORDERINFO." AS O
				ON PO.OrderID = O.OrderID

			LEFT JOIN ".GD_MEMBER." AS MB
				ON PO.MallMemberID=MB.m_id

			LEFT JOIN ".GD_NAVERCHECKOUT_DELIVERYINFO." AS D
				ON PO.ProductOrderID = D.ProductOrderID

			LEFT JOIN ".GD_NAVERCHECKOUT_CANCELINFO." AS C
				ON PO.ProductOrderID = C.ProductOrderID

			LEFT JOIN ".GD_NAVERCHECKOUT_RETURNINFO." AS R
				ON PO.ProductOrderID = R.ProductOrderID

			LEFT JOIN ".GD_NAVERCHECKOUT_EXCHANGEINFO." AS E
				ON PO.ProductOrderID = E.ProductOrderID

			LEFT JOIN ".GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO." AS DH
				ON PO.ProductOrderID = DH.ProductOrderID

			WHERE
				PO.sync_ = 0

			GROUP BY O.OrderID
		";

		$this->db->query($query);

		if ($this->db->affected() < 1)
			return false;

		// 상품 주문 정보
		$query = "
			INSERT
			INTO ".$this->temp_table_item."
			(
				channel,ordno,goodsnm,goodsno,`option`,ea,price,emoney,
				cs,
				cs_status
			)
			SELECT
				'checkout',B.ordno,C.ProductName,C.ProductID,C.ProductOption,C.Quantity,C.UnitPrice,C.eNamooEmoney,
				IF(C.ClaimStatus IN ('CANCEL_DONE','RETURN_DONE','EXCHANGE_DONE','ADMIN_CANCEL_DONE'), 'f',
				IF(C.ClaimStatus > '' , 'y', 'n')),
				C.ClaimStatus


			FROM ".$this->temp_table_order." AS B

			INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS C
				ON B.ordno = C.OrderID
		";
		$this->db->query($query);

		// 레코드 값 조정
		$this->adjustData();

		return true;

	}

	function adjustData() {


		$_tmp = array(
			// 주문 접수
			0 => array(array('cs_type' => '', 'ori_status' => 'PAYMENT_WAITING')),
			// 입금확인
			1 => array(array('cs_type' => '', 'ori_status' => 'PAYED', 'ord_status' => 'NOT_YET')),
			// 배송준비중
			2 => array(array('cs_type' => '', 'ori_status' => 'PAYED', 'ord_status' => 'OK')),
			// 배송중
			3 => array(array('cs_type' => '', 'ori_status' => 'DELIVERING')),
			// 배송완료, 구매확정
			4 => array(array('cs_type' => '', 'ori_status' => 'DELIVERED'),array('cs_type' => '', 'ori_status' => 'PURCHASE_DECIDED')),

			// 취소 (완료가 아닐때)
			10 => array(array('cs_type' => 'CANCEL')),
			// 취소완료
			11 => array(array('cs_type' => 'CANCEL', 'ori_status' => 'CANCELED'),array('cs_type' => 'CANCEL', 'ori_status' => 'CANCELED_BY_NOPAYMENT')),

			// 반품 (완료가 아닐때)
			30 => array(array('cs_type' => 'RETURN')),
			// 반품완료
			31 => array(array('cs_type' => 'RETURN', 'ori_status' => 'PAYMENT_WAITING')),

			// 교환 (완료가 아닐때)
			40 => array(array('cs_type' => 'EXCHANGE')),
			// 교환완료
			41 => array(array('cs_type' => 'EXCHANGE', 'ori_status' => 'EXCHANGED')),
			);

		// 주문상태 조정
		foreach ($_tmp as $_status => $_conds) {
			foreach($_conds as $_cond) {

				$_where = array();
				foreach ($_cond as $_fld => $_val) $_where[] = " $_fld = '$_val' ";
				if (sizeof($_where) === 0) continue;

				$query = "
				UPDATE ".$this->temp_table_order." SET
					ord_status = $_status
				WHERE ".implode(' AND ', $_where);

				$this->db->query($query);
			}
		}

		// 기타 등등 조정
		$query = "
			UPDATE ".$this->temp_table_order." SET
				old_ordno = '',
				pay_method = IF(pay_method = '신용카드' OR pay_method = '신용카드 간편결제','c',
							 IF(pay_method = '무통장입금','a',
							 IF(pay_method = '실시간계좌이체','o',
							 IF(pay_method = '휴대폰' OR pay_method = '휴대폰 간편결제','h',
							 IF(pay_method = '포인트결제','d',
							 IF(pay_method = '네이버 캐쉬','NAVER_CASH',''))))))
		";
		$this->db->query($query);

	}

	function setSyncComplete() {

		if ($this->update('checkout')) {

			$query = "
			UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS A
			INNER JOIN ".$this->temp_table_order." AS B
			ON A.OrderID = B.ordno

			SET A.sync_ = 1,A.uptdt_ = '".date('Y-m-d H:i:s',$this->now)."'
			";
			$this->db->query($query);

			$this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);

		}

	}

	// 체크아웃 객체 리턴.
	function &getApiInstance() {
        static $ins = null;

        if ($ins === null)
			$ins = Core::loader('naverCheckoutAPI_4');

        return $ins;

	}

	function getProductOrderIDList($ordno) {

		// 주문번호 길이로 3.0 인지, 4.0 인지 체크.
		if (strlen((string)$ordno) >= 15) {
			// 4.0
			$query = "
			SELECT
				ProductOrderID
			FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO."
			WHERE OrderID = '$ordno'
			";
			$rs = $this->db->query($query);
			$ordnos = array();
			while ($row = $this->db->fetch($rs,1)) {
				$ordnos[] = $row['ProductOrderID'];
			}
			return $ordnos;
		}
		else {
			// 3.0 (마이그레이션 필요함)
			return false;
		}

	}

	// 주문 처리 관련 메서드 (사용하지 않거나 지원되지 않는 메서드는 삭제해도 무방함)
	function setOrderDeliveryReady($ordno) {	// 발주처리

		$api = $this->getApiInstance();

		if (($ordnos = $this->getProductOrderIDList($ordno)) === false) {
			msg('네이버 체크아웃 3.0 주문건은 마이그레이이션 후 가능합니다.',-1);
			exit;
		}

		foreach ($ordnos as $ProductOrderID) {
			$param = array(
				'ProductOrderID' => $ProductOrderID
			);
			$rs = $api->request( 'PlaceProductOrder' , $param );
		}

	}

	function setOrderDelivery($ordno,$extra) {

		$api = $this->getApiInstance();

		if (($ordnos = $this->getProductOrderIDList($ordno)) === false) {
			msg('네이버 체크아웃 3.0 주문건은 마이그레이이션 후 가능합니다.',-1);
			exit;
		}

		$DispatchDate = date('Ymd');

		foreach ($ordnos as $ProductOrderID) {
			$param = array(
				'ProductOrderID' => $ProductOrderID,
				'DeliveryMethodCode' => 'DELIVERY',	// 일반 택배로 고정
				'DeliveryCompanyCode' => $extra['dlv_company'],
				'TrackingNumber' => $extra['dlv_no'],
				'DispatchDate' => $DispatchDate
			);
			$api->request( 'ShipProductOrder' , $param );
		}

	}



}
?>
