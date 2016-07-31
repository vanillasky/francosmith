<?php
require_once (dirname(__FILE__).'/naverCheckoutAPI.class.php');

class naverCheckoutAPI_4 extends naverCheckoutAPI {

	var $relayURL = 'http://navercheck.godo.co.kr/listen.shop.4.0.php';	// 4.0 api

	function naverCheckoutAPI_4() {
		parent::naverCheckoutAPI();
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

			if ($this->requestResult['result'] === false) {
				$this->error = $this->requestResult['error'];
				return false;
			}

			return true;
		}
		else {
			$this->error = 'API 통신에 실패했습니다';
			return false;
		}
	}

	function sync($OrderID) {
		$request = array(
			'mode'=>'sync',
			'OrderID'=>$OrderID,
		);
		$this->_httpRequest($request);
	}

	/**
	 * 중계 서버(relayUrl)를 호출, 그 결과를 상점에 반영
	 * @param object $method
	 * @param object $params
	 * @return
	 */
	function request($method, $params) {

		$params = $this->_getParams($method, $params);

		if(!$this->_httpRequest($params)) {
			return false;
		}

		return true;

	}

	/**
	 * 각 operation 별 파라미터를 정리
	 * @param object $method
	 * @param object $params
	 * @return
	 */
	function _getParams($method, $params) {

		$_set = array(
			'PlaceProductOrder' => array(
							'ProductOrderID' => true,
							),
			'DelayProductOrder' => array(
							'ProductOrderID' => true,
							'DispatchDueDate' => true,
							'DispatchDelayReasonCode' => true,
							'DispatchDelayDetailReason' => false,
							),
			'ShipProductOrder' => array(
							'ProductOrderID' => true,
							'DeliveryMethodCode' => true,
							'DeliveryCompanyCode' => false,
							'TrackingNumber' => false,
							'DispatchDate' => true,
							),
			'CancelSale' => array(
							'ProductOrderID' => true,
							'CancelReasonCode' => true,
							),
			'ApproveCancelApplication' => array(
							'ProductOrderID' => true,
							'EtcFeeDemandAmount' => true,
							'Memo' => false,
							),
			'RequestReturn' => array(
							'ProductOrderID' => true,
							'ReturnReasonCode' => true,
							'CollectDeliveryMethodCode' => true,
							'CollectDeliveryCompanyCode' => false,
							'CollectTrackingNumber' => false,
							),
			'ApproveReturnApplication' => array(
							'ProductOrderID' => true,
							'EtcFeeDemandAmount' => true,
							'Memo' => false,
							),
			'ApproveExchangeApplication' => array(
							'ProductOrderID' => true,
							'EtcFeeDemandAmount' => true,
							'Memo' => false,
							),
			'ApproveCollectedExchange' => array(
							'ProductOrderID' => true,
							'CollectDeliveryMethodCode' => true,
							),
			'ReDeliveryExchange' => array(
							'ProductOrderID' => true,
							'ReDeliveryMethodCode' => true,
							'ReDeliveryCompanyCode' => false,
							'ReDeliveryTrackingNumber' => false,
							),
			'GetMigratedProductOrderList' => array(
							'OldOrderID' => true,
							),
		);

		// 불필요한 값이 있을 수 있으므로...
		$_params = array();

		foreach ($_set[$method] as $k => $req) {
			if ($req && !isset($params[$k])) {
				// 필수값 없음.

			}

			$_params[$k] = iconv('euc-kr','utf-8',$params[$k]);

		}

		$_params['mode'] = $method;

		return $_params;

	}


	/**
	 * 주문서의 상품별 적립금 계산
	 *
	 * @param string $ProductOrderID 네이버 상품 주문번호
	 */
	function setOrderEmoney($ProductOrderID) {

		$db = Core::loader('db');
		@include_once dirname(__FILE__)."/../conf/config.pay.php";

		// 상품주문 정보
		$query = "
		SELECT

			O.PaymentMeans,

			PO.MallMemberID,
			PO.ProductName,
			PO.ProductOption,
			PO.Quantity,
			PO.UnitPrice,
			PO.ProductDiscountAmount,

			G.goodsno,
			G.use_emoney

		FROM ".GD_NAVERCHECKOUT_ORDERINFO." AS O

		INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		ON O.OrderID = PO.OrderID

		INNER JOIN ".GD_MEMBER." AS MB
		ON PO.MallMemberID = MB.m_id

		INNER JOIN ".GD_GOODS." AS G
		ON PO.ProductID = G.goodsno

		WHERE PO.ProductOrderID = '$ProductOrderID' AND PO.ProductOrderStatus NOT IN ('CANCELED','RETURNED','CANCELED_BY_NOPAYMENT')
		";

		if ($data = $db->fetch($query,1)) {

			// 적립금을 지급하기 위한 조건 확인
			if($set['emoney']['useyn'] == 'n') return; // 적립금 사용여부
			elseif($set['emoney']['limit'] == 1 && $data['PaymentMeans'] == '포인트결제') return; //적립금 결제시 상품 적립금 미지급
			else {

				// 옵션 추출
				$tmpOption = explode('/', $data['ProductOption']);
				$tmp = explode(':', $tmpOption[0]); $opt1 = $tmp[1];
				$tmp = explode(':', $tmpOption[1]); $opt2 = $tmp[1];

				if($data['use_emoney'] == "1") {
					list($reserve) = $db->fetch("SELECT reserve FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$data['goodsno']."' AND opt1 = '$opt1' AND opt2 = '$opt2' and go_is_deleted <> '1' and go_is_display = '1' ");
					$productReserve = $reserve * $data['Quantity']; // 구입 수량만큼
				}
				else {
					if($set['emoney']['goods_emoney']) {
						if($set['emoney']['chk_goods_emoney'] == "0") {
							$productReserve = (($data['UnitPrice'] / 100 ) * $set['emoney']['goods_emoney']) * $data['Quantity'];
						}
						else {
							$productReserve = $set['emoney']['goods_emoney'] * $data['Quantity'];
						}
					}
					else $productReserve = 0;
				}
			}

			settype($productReserve, "integer");

			// 적립금 DB에 저장
			$db->query("UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." SET eNamooEmoney = '$productReserve' WHERE ProductOrderID = '$ProductOrderID'");

		}

	}


	/**
	 * 재고 삭감
	 * @param object $ProductOrderID
	 * @return
	 */
	function cutStock($ProductOrderID) {

		// 재고연동 기능을 사용을 체크합니다.
		$config = Core::loader('config');
		$config_checkoutapi = $config->load('checkoutapi');
		if($config_checkoutapi['linkStock']!='y') {
			return;
		}

		$db = Core::loader('db');

		// 재고 삭감이 가능한 상품 주문정보를 가져옴.
		$query = "
		SELECT

			PO.ProductOption, PO.Quantity, PO.eNamooStockProcess, PO.OptionCode,
			G.goodsno

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_GOODS." AS G
		ON G.goodsno = PO.ProductID
		WHERE
			PO.ProductOrderID = '$ProductOrderID'
		AND PO.eNamooStockProcess = 'none'
		AND G.usestock = 'o'
		";

		if (!($productorder = $db->fetch($query,1))) return;

		// 옵션분리
		$opt = unserialize($productorder['OptionCode']);

		// 옵션재고삭감 -> 상품전체재고조정 -> 재고삭감완료처리
		$query = $db->_query_print(
				'update '.GD_GOODS_OPTION.' set stock = stock - [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
				(int)$productorder['Quantity'],
				(string)$productorder['goodsno'],
				(string)$opt[0],
				(string)$opt[1]);

		if ($db->query($query)) {

			// 전체 재고 조정
			$query = $db->_query_print(
					'UPDATE '.GD_GOODS.' SET totstock = (SELECT SUM(stock) FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] and go_is_deleted <> \'1\') WHERE goodsno=[s]',
					$productorder['goodsno'],
					$productorder['goodsno']);

			if ($db->query($query)) {
				// 재고 삭감 완료
				$query = $db->_query_print('UPDATE '.GD_NAVERCHECKOUT_PRODUCTORDERINFO.' SET eNamooStockProcess = "done" WHERE ProductOrderID=[s]',$ProductOrderID);
				$db->query($query);

			}
			else {
				// 옵션 재고 rollback.
				$query = $db->_query_print(
						'update '.GD_GOODS_OPTION.' set stock = stock + [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
						(int)$productorder['Quantity'],
						(string)$productorder['goodsno'],
						(string)$opt[0],
						(string)$opt[1]);
				$db->query($query);
			}

		}

	}


	/**
	 * 재고를 원복
	 * @param object $ProductOrderID
	 * @return
	 */
	function backStock($ProductOrderID) {

		// 재고연동 기능을 사용을 체크합니다.
		$config = Core::loader('config');
		$config_checkoutapi = $config->load('checkoutapi');
		if($config_checkoutapi['linkStock']!='y') {
			return;
		}

		$db = Core::loader('db');

		// 재고 원복이 가능한(재고가 삭감된) 상품 주문정보를 가져옴.
		$query = "
		SELECT

			PO.ProductOption, PO.Quantity, PO.eNamooStockProcess, PO.OptionCode,
			G.goodsno

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_GOODS." AS G
		ON G.goodsno = PO.ProductID
		WHERE
			PO.ProductOrderID = '$ProductOrderID'
		AND PO.eNamooStockProcess = 'done'
		AND G.usestock = 'o'
		";

		if (!($productorder = $db->fetch($query,1))) return;

		// 옵션분리
		$opt = unserialize($productorder['OptionCode']);

		// 옵션재고원복 -> 상품전체재고조정 -> 재고원복완료처리
		$query = $db->_query_print(
				'update '.GD_GOODS_OPTION.' set stock = stock + [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
				(int)$productorder['Quantity'],
				(string)$productorder['goodsno'],
				(string)$opt[0],
				(string)$opt[1]);

		if ($db->query($query)) {

			// 전체 재고 조정
			$query = $db->_query_print(
					'UPDATE '.GD_GOODS.' SET totstock = (SELECT SUM(stock) FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] and go_is_deleted <> \'1\') WHERE goodsno=[s]',
					$productorder['goodsno'],
					$productorder['goodsno']);

			if ($db->query($query)) {
				// 재고 원복 완료
				$query = $db->_query_print('UPDATE '.GD_NAVERCHECKOUT_PRODUCTORDERINFO.' SET eNamooStockProcess = "back" WHERE ProductOrderID=[s]',$ProductOrderID);
				$db->query($query);

			}
			else {
				// 옵션 재고 rollback.
				$query = $db->_query_print(
						'update '.GD_GOODS_OPTION.' set stock = stock - [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
						(int)$productorder['Quantity'],
						(string)$productorder['goodsno'],
						(string)$opt[0],
						(string)$opt[1]);
				$db->query($query);
			}

		}

	}


	/**
	 * 적립금 적립
	 *
	 * @param integer $orderNo 네이버 주문서 DB 주문번호
	 * @param integer $mode 적립:1, 회수:-1
	 */
	function setEmoney($ProductOrderID, $mode=1) {

		$db = Core::loader('db');

		// 지급 내역, 대상 조회
		$query = "
		SELECT
			PO.ProductOrderID, PO.eNamooEmoney,
			MB.m_id, MB.m_no,

			LE.sno, LE.emoney

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_MEMBER." AS MB
		ON PO.MallMemberID = MB.m_id
		INNER JOIN ".GD_LOG_EMONEY." AS LE
		ON LE.ordno = PO.ProductOrderID
		WHERE PO.ProductOrderID = '$ProductOrderID' AND PO.eNamooEmoney > 0
		";

		if (!($data = $db->fetch($query,1))) return;

		$msg = ($mode > 0) ? "구매완료로 인해 구매적립금 적립 - 네이버 체크아웃" : "구매취소로 인해 구매적립금 환원 - 네이버 체크아웃";

		$dormantMember = false;
		$dormant = Core::loader('dormant');
		$dormantMember = $dormant->checkDormantMember(array('m_no'=>$data[m_no]), 'm_no');

		if ($mode > 0) {
			// 지급 (지급 기록이 없을때)
			if (is_null($data['sno'])) {
				if($dormantMember === true){
					$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($data[m_no], $data[eNamooEmoney]);
					$db->query($dormantEmoneyQuery);
				}
				else {
					$db->query("UPDATE ".GD_MEMBER." SET emoney = emoney + $data[eNamooEmoney] WHERE m_no = '$data[m_no]'");
				}
				$db->query("INSERT INTO ".GD_LOG_EMONEY." SET
					m_no	= '$data[m_no]',
					ordno	= '$ProductOrderID',
					emoney	= '$data[eNamooEmoney]',
					memo	= '$msg',
					regdt	= NOW()
				");
			}
		}
		else {
			// 차감 (지급 기록이 있을때)
			if (!is_null($data['sno'])) {
				if($dormantMember === true){
					$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($data[m_no], $data[emoney]);
					$db->query($dormantEmoneyQuery);
				}
				else {
					$db->query("UPDATE ".GD_MEMBER." SET emoney = emoney - $data[emoney] WHERE m_no = '$data[m_no]'");
				}
				$db->query("INSERT INTO ".GD_LOG_EMONEY." SET
					m_no	= '$data[m_no]',
					ordno	= '$ProductOrderID',
					emoney	= '-$data[emoney]',
					memo	= '$msg',
					regdt	= NOW()
				");

				$db->query("UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." SET eNamooEmoney = '0' WHERE ProductOrderID = '$ProductOrderID'");
			}

		}

	}

	/**
	 * 구매완료 쿠폰 발급
	 *
	 * @param integer $orderNo 네이버 주문서 DB 주문번호
	 */
	function setCoupon($ProductOrderID) {
		$db = Core::loader('db');

		$query = "
		SELECT
			PO.ProductOrderID, PO.ProductID,
			MB.m_id, MB.m_no

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_MEMBER." AS MB
		ON PO.MallMemberID = MB.m_id
		INNER JOIN ".GD_GOODS." AS G
		ON PO.ProductID = G.goodsno

		WHERE PO.ProductOrderID = '$ProductOrderID'
		";

		if (!($data = $db->fetch($query,1))) return;

		$query = "SELECT category, CHAR_LENGTH(category) clen FROM ".GD_GOODS_LINK." WHERE hidden = 0 AND goodsno = '$data[ProductID]'";
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
						AND (((b.category in(".implode(',',$arrCategory).") OR c.goodsno = '$data[ProductID]') AND a.goodstype='1') OR a.goodstype='0')";

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
?>
