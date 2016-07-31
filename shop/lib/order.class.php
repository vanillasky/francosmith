<?
if(!defined('kTAXFREE')) define('kTAXFREE', 'taxfree');
if(!defined('kTAXALL')) define('kTAXALL', 'taxall');
if(!defined('kTAX')) define('kTAX', 'tax');
if(!defined('kVAT')) define('kVAT', 'vat');

class order implements ArrayAccess {
	private $_db;
	private $_data;
	private $_order_item;
	private $_order_cancel;
	private $_is_all_canceled; // @qni 2015-04 전체취소접수/완료 여부
	private $_is_all_canceling; // @qni 2015-04 전체취소접수 여부
	private $_is_all_cancelCompleted; // @qni 2015-04 전체취소완료 여부

	public function __construct() {
		$this->_db = Core::loader('db');
	}

	/**
	 * Whether a offset exists
	 * @param string $offset An offset to check for
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Offset to retrieve
	 * @param string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->_data[$offset];
	}

	/**
	 * Offset to set
	 * @param string $offset The offset to assign the value to
	 * @param mixed $value The value to set
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		$this->_data[$offset] = $value;
	}

	/**
	 * Offset to unset
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}

	public function load($ordno) {
		$query = "select b.m_id,b.dormant_regDate,a.* from " . GD_ORDER . " a left join " . GD_MEMBER . " b on a.m_no=b.m_no where ordno='$ordno'";
		$this->_data = $this->_db->fetch($query, 1);
		$this->_order_item = null;
		$this->_order_cancel = null;

		// 주문취소 여부 할당
		$this->_is_all_canceled = false;
		$this->_is_all_canceling = false;
		$this->_is_all_cancelCompleted = false;
		$this->_is_all_pgcancel = false;
		$this->isAllCanceled();
	}

	public function getOrderItems() {
		if (is_null($this->_order_item)) {
			$this->_loadOrderItems();
		}

		return (array) $this->_order_item;
	}

	private function _loadOrderItems() {
		$query = "select sno from " . GD_ORDER_ITEM . " where ordno='{$this['ordno']}' order by sno";
		$rs = $this->_db->query($query);

		$item = new order_item();

		$this->_order_item = array();

		while ($row = $this->_db->fetch($rs, 1)) {
			$_item = clone $item;
			$_item->load($row['sno']);
			$this->_order_item[] = $_item;
		}
	}

	// @qni 2015-05 주문취소 ROW
	public function getOrderCancel($sno) {
		$data = array();
		if(is_numeric($sno)) {
			foreach ($this->getOrderCancels() as $cancel) {
				if ($sno == $cancel['sno']) {
					$data = $cancel;
					break;
				}
			}
		}

		return $data;
	}

	// @qni 2015-05 주문취소 전체
	public function getOrderCancels() {
		if (is_null($this->_order_cancel)) {
			$this->_loadOrderCancels();
		}

		return (array) $this->_order_cancel;
	}

	private function _loadOrderCancels() {
		// 취소데이터를 가져오기 위한 item 테이블의 cancel필드 가져오기
		$cancels = array();
		foreach ($this->getOrderItems() as $item) {
			$cancels[] += $item['cancel'];
		}
		$cancels = array_unique($cancels);// 중복되는 취소SNO 제거

		if ($cancels) {
			$query = "select sno from " . GD_ORDER_CANCEL . " where sno in (" . implode(",",$cancels) .") order by sno";
			$rs = $this->_db->query($query);

			$cancel = new order_cancel();

			// 인스턴스 생성시 세금계산을 위한 값 초기화
			$cancel->taxfree_ratio = $this->getTaxFreeRatio();// 면세비율 할당

			$this->_order_cancel = array();

			while ($row = $this->_db->fetch($rs, 1)) {
				$_cancel = clone $cancel;
				$_cancel->load($row['sno']);
				$this->_order_cancel[] = $_cancel;
			}
		}
	}

	public function getDiscount() {
		return $this->getMemberDiscount() + $this['emoney'] + $this['coupon'] + $this['enuri'] + $this['ncash_emoney'] + $this['ncash_cash'] + $this['o_special_discount_amount'];
	}

	public function getDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this->getMemberDiscount()) {
			$discount_detail['회원할인'] = $this->getMemberDiscount();
		}

		if ($this['coupon']) {
			$discount_detail['쿠폰할인'] = array('%할인' => $this->getPercentCouponDiscount(), '금액할인' => $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'],);
		}

		if ($this['o_special_discount_amount']) {
			$discount_detail['상품할인'] = $this['o_special_discount_amount'];
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['쿠폰할인']['어바웃'] = $this['about_dc_sum'];
		}

		if ($this['emoney']) {
			$discount_detail['적립금사용'] = $this['emoney'];
		}

		if ($this['ncash_emoney']) {
			$discount_detail['네이버마일리지사용'] = $this['ncash_emoney'];
		}

		if ($this['ncash_cash']) {
			$discount_detail['네이버캐쉬사용'] = $this['ncash_cash'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {

				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {

					$_tmp = array();

					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}

					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	public function getPercentCouponDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += $item->getPercentCouponDiscount();
		}

		return $amount;
	}

	public function getMemberDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += $item->getMemberDiscount();
		}
		return $amount;
	}

	public function getAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += $item->getAmount();
		}
		return $amount;
	}

	public function getCanceledAmount() {
		$amount = 0;

		$cnt = 0; // all count;
		$cnt2 = 0; // canceled count;

		foreach ($this->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCanceled()) {
				$cnt2++;
			}
			$amount += $item->getCanceledAmount();
		}

		// 전체 주문 품목 취소일 경우
		// 배송비 - 에누리 - 적립금사용액 - 네이버마일리지 - 네이버캐시 - 금액할인 + 보증보험수수료 를 합산한다.
		if ($cnt == $cnt2) {
			$amount += $this->getDeliveryFee() - $this[enuri] - $this[emoney] - $this['ncash_emoney'] - $this['ncash_cash'] - ($this[coupon] - $this->getPercentCouponDiscount()) + $this[eggFee];
		}

		return $amount;
	}

	public function getCanceledDetailArray($format = false) {
		$amount = array();

		$cnt = 0; // all count;
		$cnt2 = 0; // canceled count;

		foreach ($this->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCanceled()) {
				$cnt2++;

				$amount['취소상품 판매금액'] += $item->getAmount();
				$amount['상품별할인 적용금액'] += $item->getDiscount();
			}
		}

		$amount['상품별할인 적용금액'] = $amount['상품별할인 적용금액'] * -1;

		// 전체 주문 품목 취소일 경우
		// + 배송비 - 에누리 - 적립금사용액 - 네이버마일리지 - 네이버캐시 - 금액할인 + 보증보험수수료 를 합산한다.
		if ($cnt == $cnt2) {
			if ($this[coupon] - $this->getPercentCouponDiscount()) {
				$amount['금액할인'] = -($this[coupon] - $this->getPercentCouponDiscount());
			}

			if ($this->getDeliveryFee()) {
				$amount['배송비'] = $this->getDeliveryFee();
			}

			if ($this[enuri]) {
				$amount['에누리'] = -$this[enuri];
			}

			if ($this[emoney]) {
				$amount['적립금'] = -$this[emoney];
			}

			if ($this[ncash_emoney]) {
				$amount['네이버 마일리지'] = -$this[ncash_emoney];
			}

			if ($this[ncash_cash]) {
				$amount['네이버 캐쉬'] = -$this[ncash_cash];
			}

			if ($this[eggFee]) {
				$amount['보증보험수수료'] = $this[eggFee];
			}
		}


		if ($format) {
			$tmp = array();
			foreach ($amount as $k => $v) {
				$operator = $v > 0 ? '+' : '-';
				if (sizeof($tmp) == 0)
					$operator = '';
				$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format(abs($v)));
			}
			return $tmp;
		}

		return $amount;
	}

	public function getCanceledCount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceled())
				$amount++;
		}
		return $amount;
	}

	public function getRefundedAmount() {
		$amount = array();

		foreach ($this->getOrderItems() as $item) {
			$amount = $amount + $item->getRefundedAmount();
		}

		return array_sum($amount);
	}

	public function getSettleAmount() {
		return $this->getAmount() + $this->getDeliveryFee() - $this->getDiscount() + $this[eggFee];
	}

	public function getRealSettleAmount() {
		// 최초결제금액 - 취소금액(환불예정 금액 포함)
		return $this->getSettleAmount() - $this->getCanceledAmount();
	}

	public function getDeliveryFeeDetailArray($format = false) {
		$delivery_fee_detail = array();

		$delivery_type_label = array(0 => '기본 배송비', 1 => '무료배송', 3 => '착불 배송비', 2 => '상품별 배송비', 4 => '고정 배송비', 5 => '수량별 배송비',);

		foreach ($this->getOrderItems() as $item) {

			/*
			  [0] : 기본배송비
			  [1] : 무료배송
			  [2] : 상품별 배송비
			  [4] : 고정 배송비
			  [5] : 수량별 배송비
			  [3] : 착불 배송비
			 */
			if (in_array($item['oi_delivery_type'], array(2, 4, 5))) {
				// 수량별 배송비는 구매 수량을 곱한다.
				$item_delivery_fee = $item['oi_delivery_type'] == 5 ? $item['oi_goods_delivery'] * $item['ea'] : $item['oi_goods_delivery'];
				$delivery_fee_detail[$delivery_type_label[$item['oi_delivery_type']]] += $item_delivery_fee;
			}
		}


		$total_item_delivery_fee = array_sum($delivery_fee_detail);
		if (($_fee = $this['delivery'] - $total_item_delivery_fee) > 0) {
			$delivery_fee_detail['기본배송비'] = $_fee;
		}

		if ($format) {
			$tmp = array();
			foreach ($delivery_fee_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';
				$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
			}

			return $tmp;
		}

		return $delivery_fee_detail;
	}

	public function getDeliveryFee() {
		return $this['delivery'];
	}

	public function getStepMsg() {
		return getStepMsg($this[step], $this[step2], $this[ordno]);
	}

	public function hasExchanged() {
		list($cnt) = $this->_db->fetch("select count(*) from " . GD_ORDER . " where oldordno='{$this[ordno]}'");

		return $cnt > 0 ? true : false;
	}


	/**
	 * 관리자 > 주문상세 > 결제금액정보
	 * 취소 및 부분취소와 관련된 금액을 세분화하는 작업
	 *
	 * @since 2015-04
	 * @author qnibus <qnibus@godo.co.kr>
	 * @link http://[userid].godo.co.kr/shop/admin/order/view.php?ordno=[ordno]
	 */

	 // @qni 2015-04 페이코 패치 설치 여부 확인
	private function isInstallPayco() {
		global $config;
		$payco = $config->load('payco');
		return !empty($payco);
	}

	// @qni 2015-04 페이코 패치 설치 여부 확인
	private function isPaycoPay() {
		return ($this['pg'] == 'payco'); // 페이코 진행시 주석제거
	}

	// @qni 2015-04 주문상품 취소여부 변수설정 (load 함수에서 초기화)
	private function isAllCanceled() {
		$cnt = $cnt2 = $cnt3 = $cnt4 = 0;
		foreach ($this->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCanceling()) {
				$cnt2++;
			}
			if ($item->hasCancelCompleted()) {
				$cnt3++;
			}
			if ($item->hasPgCanceled()) {
				$cnt4++;
			}
		}

		$this->_is_all_canceled = ($cnt == $cnt2 + $cnt3);
		$this->_is_all_canceling = ($cnt == $cnt2);
		$this->_is_all_cancelCompleted = ($cnt == $cnt3);
		$this->_is_all_pgcancel = ($cnt - $cnt4 == 1);
	}

	// @qni 2015-04 결제금액 > 페이코 결제 내역
	function getPaycoOrderDetailArray($format = false) {
		$payco_detail = $this->getPaycoOrderTypeArray();

		if ($this->isPaycoPay()) {
			$tmp = array();
			foreach ($payco_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				$k = str_replace('Payco', '<img src="../img/icon_payco.gif">', $k);

				if (is_array($v)) {
					$_tmp = array();



					foreach ($v as $k2 => $v2) {
						if ($v2) {
							if ($format) $_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
							else $_tmp[] = sprintf('%s', $k2);
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					if ($k) {
						if ($format) $tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
						else $tmp[] = sprintf('%s %s', $operator, $k);
					}
				}
			}
			return $tmp;
		}

		return $payco_detail;
	}
	function getPaycoOrderTypeArray() {
		$payco_detail = array();

		if ($this->isPaycoPay()) {
			$settlekind_label = array(
				"a" => "무통장",
				"c" => "신용카드",
				"o" => "계좌이체",
				"v" => "가상계좌",
				"d" => "전액할인",
				"h" => "핸드폰",
				"p" => "포인트",
				"u" => "신용카드 (중국)",
				"y" => "옐로페이",
				"e" => "페이코 포인트",
			);

			if ($this['payco_use_point']) {
				$payco_detail['Payco포인트'] = $this['payco_use_point'];
			}

			if ($this['payco_coupon_price']) {
				$payco_detail['Payco쿠폰'] = $this['payco_coupon_price'];
			}

			$payco_detail['Payco'.$settlekind_label[$this['settlekind']]] = $this['settleprice'] - $this['payco_use_point'] - $this['payco_coupon_price'];
			arsort($payco_detail);
		}

		return $payco_detail;
	}

	// @qni 2015-04 결제금액 > 상품할인
	public function getGoodsDiscount() {
		return $this['o_special_discount_amount'];
	}

	// @qni 2015-04 결제금액 > 상품할인 내역
	public function getGoodsDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this['o_special_discount_amount']) {
			$discount_detail['상품할인'] = $this['o_special_discount_amount'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 결제금액 > 쿠폰할인
	public function getCouponDiscount() {
		return $this['coupon'];
	}

	// @qni 2015-04 결제금액 > 쿠폰할인 내역(쿠폰 + 어바웃쿠폰)
	public function getCouponDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this['coupon']) {
			$discount_detail['쿠폰할인'] = array('%할인' => $this->getPercentCouponDiscount(), '금액할인' => $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'],);
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['쿠폰할인']['어바웃'] = $this['about_dc_sum'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s', $operator, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s원', $operator, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 결제금액 > 적립금(적립금 + 네이버캐쉬 + 네이버마일리지)
	public function getEmoneyDiscount() {
		return $this['emoney'] + $this->getNcashEmoneyDiscount() + $this->getNcashCashDiscount();
	}

	// @qni 2015-04 결제금액 > 적립금내 네이버마일리지 (디버그 적용)
	public function getNcashEmoneyDiscount() {
		$amount = 0;
		foreach ($this->getOrderCancels() as $cancel) {
			$amount += $cancel['rncash_emoney'];
		}

		return $this['ncash_emoney'] + $amount;
	}

	// @qni 2015-04 결제금액 > 적립금내 네이버캐시 (디버그 적용)
	public function getNcashCashDiscount() {
		$amount = 0;
		foreach ($this->getOrderCancels() as $cancel) {
			$amount += $cancel['rncash_cash'];
		}

		return $this['ncash_cash'] + $amount;
	}

	// @qni 2015-04 결제금액 > 적립금(적립금 + 네이버캐시 + 네이버마일리지) 내역
	public function getEmoneyDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this['emoney']) {
			$discount_detail['적립금사용'] = $this['emoney'];
		}

		if ($this->getNcashEmoneyDiscount()) {
			$discount_detail['네이버마일리지사용'] = $this->getNcashEmoneyDiscount();
		}

		if ($this->getNcashCashDiscount()) {
			$discount_detail['네이버캐쉬사용'] = $this->getNcashCashDiscount();
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 결제금액 > 결제금액
	public function getSettledAmount() {
		return $this->getSettleAmount() + $this->getEnuriAmount();
	}

	// @qni 2015-04 취소(환불)접수금액 > 취소(환불)접수금액
	public function getCancelingAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getSettleAmount();
			}
		}

		// 배송비 (페이코 및 전체주문취소일 경우 자동계산)
		$amount += $this->getCancelingDeliveryFee();

		// 적립금은 전체품목 취소일 경우 네이버 마일리지는 취소시 즉시 차감되도록 처리
		$amount -= $this->getCancelingEmoney();

		// 전체 주문 품목 취소인 경우
		// 배송비 - 에누리 - 적립금사용액 - 네이버마일리지 - 네이버캐시 - 금액할인 + 보증보험수수료 를 합산한다.
		if ($this->_is_all_canceled) {
			$amount += $this['eggFee'] - ($this['coupon'] - $this->getPercentCouponDiscount());
		}

		return $amount;
	}

	public function isCancelingEgg() {
		return $this->_is_all_canceled && $this['eggFee'];
	}

	// @qni 2015-04 취소(환불)접수금액 > 상품판매금액
	public function getCancelingGoodsAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getAmount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 취소(환불)접수금액 > 회원할인
	public function getCancelingMemberDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getMemberDiscount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 취소(환불)접수금액 > 상품할인
	public function getCancelingGoodsDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getSpecialDiscount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 취소(환불)접수금액 > 쿠폰할인 (부분취소시 정률할인 별도 계산 고려해야 함)
	public function getCancelingCouponDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getPercentCouponDiscount();
			}
		}

		// (쿠폰금액 - 주문정률할인금액 = 금액할인) - 어바웃할인금액 + 취소접수 상품정률할인금액
		if ($this->_is_all_canceled) {
			$amount += $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'];
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)접수금액 > 쿠폰할인 내역
	function getCancelingCouponDiscountDetailArray($format = false) {
		$discount_detail = array();
		$coupon_percent_amount = 0;
		$coupon_price_amount = 0;
		$coupon_about_amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$coupon_percent_amount += $item->getPercentCouponDiscount();
			}
		}

		if ($this->_is_all_canceled) {
			$coupon_price_amount = $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'];
		}

		if ($this['coupon']) {
			$discount_detail['쿠폰할인'] = array('%할인' => $coupon_percent_amount, '금액할인' => $coupon_price_amount, $this['about_dc_sum']);
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['쿠폰할인']['어바웃'] = $this['about_dc_sum'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s', $operator, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s원', $operator, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 취소(환불)접수금액 > 적립금
	public function getCancelingEmoney() {
		$amount = 0;

		// 네이버 마일리지/캐시 계산
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCanceling()) {
				$amount += $cancel['rncash_emoney'] + $cancel['rncash_cash'];
			}
		}

		// 주문전체가 취소로 된 경우 취소접수 적립금 반환
		if ($this->_is_all_canceled) {
			$amount += $this['emoney'];
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 적립금(적립금 + 네이버캐시 + 네이버마일리지) 내역
	public function getCancelingEmoneyDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this->_is_all_canceled && $this['emoney']) {
			$discount_detail['적립금사용'] = $this['emoney'];
		}

		// 네이버 마일리지/캐시 계산
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCanceling()) {
				if ($cancel['rncash_emoney']) {
					$discount_detail['네이버마일리지사용'] += $cancel['rncash_emoney'];
				}

				if ($cancel['rncash_cash']) {
					$discount_detail['네이버캐쉬사용'] += $cancel['rncash_cash'];
				}

			}
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 취소(환불)접수금액의 주문상품 갯수
	public function getCancelingCount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling())
				$amount++;
		}
		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 취소(환불)완료금액
	public function getCancelCompletedAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getSettleAmount();
			}
		}

		// 배송비 (페이코 및 전체주문취소일 경우 계산)
		$amount += $this->getCancelCompletedDeliveryFee();

		// 적립금은 전체품목 취소일 경우 네이버 마일리지는 취소시 즉시 차감되도록 처리
		$amount -= $this->getCancelCompletedEmoney();

		// 전체 주문 품목 취소일 경우
		// 배송비 - 에누리 - 적립금사용액 - 네이버마일리지 - 네이버캐시 - 금액할인 + 보증보험수수료 를 합산한다.
		if ($this->_is_all_cancelCompleted) {
			$amount += $this[eggFee] - ($this[coupon] - $this->getPercentCouponDiscount());
		}

		return $amount;
	}

	public function isCancelCompletedEgg() {
		return $this->_is_all_cancelCompleted && $this['eggFee'];
	}

	// @qni 2015-04 취소(환불)완료금액 > 상품판매금액
	public function getCancelCompletedGoodsAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getAmount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 회원할인
	public function getCancelCompletedMemberDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getMemberDiscount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 상품할인
	public function getCancelCompletedGoodsDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getSpecialDiscount();
			}
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 쿠폰할인
	public function getCancelCompletedCouponDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getPercentCouponDiscount();
			}
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 쿠폰할인 내역
	public function getCancelCompletedCouponDiscountDetailArray($format = false) {
		$discount_price = $coupon_percent_amount = 0;
		$discount_detail = array();

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$coupon_percent_amount += $item->getPercentCouponDiscount(); // 상품쿠폰 할인금액
			}
		}

		if ($this['coupon']) {
			if ($this->_is_all_cancelCompleted) {
				$discount_price = $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'];
			}
			$discount_detail['쿠폰할인'] = array('%할인' => $coupon_percent_amount, '금액할인' => $discount_price,);
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['쿠폰할인']['어바웃'] = $this['about_dc_sum'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s', $operator, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s원', $operator, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 취소(환불)완료금액 > 취소(환불)완료금액 내역 (실 취소(환불)금액 - 환불수수료)
	public function getCancelCompletedDetailArray($format = false) {
		$amount = array();

		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				$amount['환불수수료'] += $cancel['rfee'];
			}
		}

		$amount['환불금액'] += $this->getCancelCompletedAmount() - $amount['환불수수료'];
		ksort($amount);

		if ($format) {
			$tmp = array();
			foreach ($amount as $k => $v) {
				$operator = $v >= 0 ? '+' : '-';
				if (sizeof($tmp) == 0)
					$operator = '';
				$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format(abs($v)));
			}
			return $tmp;
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 적립금
	public function getCancelCompletedEmoney() {
		$amount = 0;

		// 네이버 마일리지/캐시 계산
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				$amount += $cancel['rncash_emoney'] + $cancel['rncash_cash'];
			}
		}

		// 주문전체가 취소로 된 경우 취소완료 적립금 반환
		if ($this->_is_all_cancelCompleted) {
			$amount += $this['emoney'];
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 적립금(적립금 + 네이버캐시 + 네이버마일리지) 내역
	public function getCancelCompletedEmoneyDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this->_is_all_cancelCompleted && $this['emoney']) {
			$discount_detail['적립금사용'] = $this['emoney'];
		}

		// 네이버 마일리지/캐시 계산
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				if ($cancel['rncash_emoney']) {
					$discount_detail['네이버마일리지사용'] += $cancel['rncash_emoney'];
				}

				if ($cancel['rncash_cash']) {
					$discount_detail['네이버캐쉬사용'] += $cancel['rncash_cash'];
				}

			}
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 취소(환불)완료금액 주문상품 갯수
	public function getCancelCompletedCount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount++;
			}
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 적립금 (현재까지 돌려준 적립금)
	public function getRefundedEmoney() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += array_sum($item->getRefundedEmoney());
		}

		return $amount;
	}

	// @qni 2015-04 최종결제금액 > 페이코결제내역
	function getPaycoSettleDetailArray($format = false) {
		$payco_detail = array();

		if ($this->isPaycoPay()) {
			$settlekind_label = array(
				"a" => "무통장",
				"c" => "신용카드",
				"o" => "계좌이체",
				"v" => "가상계좌",
				"d" => "전액할인",
				"h" => "핸드폰",
				"p" => "포인트",
				"u" => "신용카드 (중국)",
				"y" => "옐로페이",
				"e" => "페이코 포인트",
			);

			$payco_detail['Payco'.$settlekind_label[$this['settlekind']]] = $this->getCancelCompletedRealSettleAmount();
			arsort($payco_detail);

			if ($format) {
				$tmp = array();
				foreach ($payco_detail as $k => $v) {
					$operator = '+';
					if (sizeof($tmp) == 0)
						$operator = '';

					$k = str_replace('Payco', '<img src="../img/icon_payco.gif">', $k);

					if (is_array($v)) {
						$_tmp = array();
						foreach ($v as $k2 => $v2) {
							if ($v2) {
								$_tmp[] = sprintf('%s : %s원', $k2, number_format($v2));
							}
						}
						$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
					} else {
						$tmp[] = sprintf('%s %s(%s원)', $operator, $k, number_format($v));
					}
				}
				return $tmp;
			}
		}

		return $payco_detail;
	}

	// @qni 2015-04 최종결제금액 > 환불수수료
	public function getRefundedFeeAmount() {
		$amount = 0;

		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				$amount += $cancel['rfee'];
			}
		}

		return $amount;
	}

	// @qni 2015-04 최종결제금액 > 환불수수료 여부
	public function getRefundedFeeCount() {
		if ($this->getRefundedFeeAmount() > 0) {
			return true;
		}
		return false;
	}

	// @qni 2015-04 최종결제금액 > 최종실결제금액 = 최초주문금액 + 배송비 - 할인금액 + 보증보험수수료 - 취소완료금액
	public function getCancelCompletedRealSettleAmount() {
		// 최초결제금액 - 취소(환불)완료금액 (취소/환불접수금액 미포함) - 에누리
		return $this->getSettledAmount() - $this->getCancelCompletedAmount() - $this->getEnuriAmount();
	}

	// @qni 2015-04 최종결제금액 > 실제남은금액 (최종결제금액 + 환불수수료)
	public function getRealPrnSettleAmount() {
		// 뒤에 네이버캐시를 별도로 빼는 이유는 이미 getCancelCompletedAmount에 취소된 네이버캐시가 차감되고 order 테이블의 네이버캐시가 0이 되었기 때문
		return $this->getCancelCompletedRealSettleAmount() + $this->getRefundedFeeAmount();
	}

	// @qni 2015-04 취소(환불)접수금액 > 환불 후 예상결제금액 (최초결제금액 - 취소완료금액 - 취소접수금액)
	public function getCancelingRealPrnSettleAmount() {
		return $this->getRealPrnSettleAmount() - $this->getCancelingAmount();
	}

	// @qni 2015-04 최종결제금액 > 에누리 부호표기
	public function getEnuriSign() {
		if ($this['enuri'] > 0) {
			return '+ ';
		} else {
			return ' ';
		}
	}

	// @qni 2015-04 최종결제금액 > 에누리
	public function getEnuriAmount() {
		return $this['enuri'];
	}

	// @qni 2015-04 취소(환불)접수금액 > PAYCO 배송비
	public function getPaycoCancelingDeliveryFee() {
		$amount = 0;

		if ($this->isPaycoPay()) {
			$order_delivery_item = new orderDeliveryItem($this['ordno']);
			$delivery_price = $order_delivery_item->getCancelingDeliveryFee($this['ordno']);
			$amount = $delivery_price['total_cancel_delivery_price'];
			unset($order_delivery_item);
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)접수금액 > 배송비
	public function getCancelingDeliveryFee() {
		$amount = 0;
		$amount += $this->getPaycoCancelingDeliveryFee();

		// 전체 주문 품목 취소접수/취소완료이면서 페이코결제가 아닌 경우
		if ($this->_is_all_canceled && !$this->isPaycoPay()) {
			$amount += $this->getDeliveryFee();
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > PAYCO 배송비
	public function getPaycoCancelCompletedDeliveryFee() {
		$amount = 0;

		if ($this->isPaycoPay()) {
			$order_delivery_item = new orderDeliveryItem($this['ordno']);
			$delivery_price = $order_delivery_item->getCancelCompletedDeliveryFee($this['ordno']);
			$amount = $delivery_price['total_cancel_delivery_price'];
			unset($order_delivery_item);
		}

		return $amount;
	}

	// @qni 2015-04 취소(환불)완료금액 > 배송비
	public function getCancelCompletedDeliveryFee() {
		$amount = 0;
		$amount += $this->getPaycoCancelCompletedDeliveryFee();

		// 전체 주문 품목 취소일 경우
		if ($this->_is_all_cancelCompleted && !$this->isPaycoPay()) {
			$amount += $this->getDeliveryFee();
		}

		return $amount;
	}

	// @deprecated 페이코 오픈하면 사용할 예정이며 페이코 취소단계별 포인트 취합하는 부분임
	public function getPaycoCancelCompletedPoint($sno = 0) {
		$amount = 0;
		$settlekind_label = array(
			"a" => "무통장",
			"c" => "신용카드",
			"o" => "계좌이체",
			"v" => "가상계좌",
			"d" => "전액할인",
			"h" => "핸드폰",
			"p" => "포인트",
			"u" => "신용카드 (중국)",
			"y" => "옐로페이",
			"e" => "페이코 포인트",
		);

		$payco_price = $this['settleprice'] - $this['payco_use_point'];//1000
		$payco_point = $this['payco_use_point'];//12000

		if ($this->isPaycoPay()) {
			foreach($this->getOrderCancels() as $cancel) {
				if ($cancel->hasCancelCompleted()) {
					if ($payco_price <= $cancel['rprice']) {
						$payco_price = 0;
						$payco_point -= ($cancel['rprice'] - $payco_price);
					} else {
						$payco_price -= $cancel['rprice'];
					}
				}

				if ($cancel['sno'] == $sno) break;
			}
		}

		return $this['payco_use_point'] - $payco_point;
	}

	// @qni 2015-05 최초복합과세 > 과세/면세 상품결제금액
	// 아래 함수내 Canceled를 포함한 내역은 pgcancel필드가 r,y인 경우
	// @return array [0]면세/[1]과세 주문상품 결제금액 합산
	public function getSettleAmountsForTax() {
		$amounts = array();
		foreach($this->getOrderItems() as $item) {
			$key = $item['tax'] == 0 ? kTAXFREE : kTAXALL;
			$amounts[$key] += $item->getSettleAmountWithTax($item['tax']);
		}
		return $amounts;
	}

	// @qni 2015-05 최초복합과세 > 과세/면세 주문전체 할인금액
	// 마지막 상품할인(getPercentCouponDiscount)을 빼는 이유는 주문테이블의 coupon과 아이템테이블의 coupon이 중복부과 되기때문에 해당 부분을 빼줘야 정확한 할인금액이 산출
	public function getDiscountAmountForTax() {
		return $this->getCouponDiscount() + $this->getNaverDiscountAmountForTax() + $this[emoney] + $this['enuri'] - $this->getPercentCouponDiscount();
	}

	public function getNaverDiscountAmountForTax() {
		return $this->getNcashEmoneyDiscount() + $this->getNcashCashDiscount();
	}

	// @qni 2015-05 최초복합과세 > 면세비율
	public function getTaxFreeRatio() {
		$amounts = $this->getSettleAmountsForTax();
		return $amounts[kTAXFREE]/array_sum($amounts);
	}


	// @qni 2015-05 최초복합과세 > 면세할인금액
	public function getTaxFreeDiscountAmount() {
		return ceil($this->getDiscountAmountForTax() * $this->getTaxFreeRatio());
	}

	// @qni 2015-05 최초복합과세 > 면세결제금액
	public function getTaxFreeTmpSettleAmount() {
		$amounts = $this->getSettleAmountsForTax();
		return $amounts[kTAXFREE] - $this->getTaxFreeDiscountAmount();
	}

	// @qni 2015-05 최초복합과세 > 면세결제금액 (화면출력용)
	public function getTaxFreeSettleAmount() {
		// 남은 결제 면세금액이 취소 면세금액보다 작을 경우 과세에서 차감

		$amount = $this->getTaxFreeTmpSettleAmount();
		// 과세가 음수인 경우 면세에서 차감
		if ($this->getTaxTmpSettleAmount() < 0) {
			$amount += $this->getTaxTmpSettleAmount();
		}
		return $amount > 0 ? $amount : 0;
	}


	// @qni 2015-05 최초복합과세 > 과세할인금액
	public function getTaxDiscountAmount() {
		return $this->getDiscountAmountForTax() - $this->getTaxFreeDiscountAmount();
	}


	// @qni 2015-05 최초복합과세 > 과세추가금액
	public function getTaxAddAmount() {
		return $this->getDeliveryFee() + $this[eggFee];
	}


	// @qni 2015-05 최초복합과세 > 과세결제금액
	public function getTaxTmpSettleAmount() {
		$amounts = $this->getSettleAmountsForTax();
		return $amounts[kTAXALL] - $this->getTaxDiscountAmount() + $this->getTaxAddAmount();
	}
	public function getTaxSettleAmount() {
		$amount = $this->getTaxTmpSettleAmount();
		// 면세가 음수인 경우 과세에서 차감
		if ($this->getTaxFreeTmpSettleAmount() < 0) {
			$amount += $this->getTaxFreeTmpSettleAmount();
		}
		return $amount;
	}


	// @qni 2015-05 최초복합과세 > 면세금액
	public function getTaxFreeAmount() {
		return $this->getTaxFreeSettleAmount();
	}

	// @qni 2015-05 최초복합과세 > 과세금액
	public function getTaxAmount() {
		return ceil($this->getTaxSettleAmount() / 1.1);
	}

	// @qni 2015-05 최초복합과세 > 부가세
	public function getVatAmount() {
		return $this->getTaxSettleAmount() - $this->getTaxAmount();
	}

	// @qni 2015-05 마지막 취소주문 리스트의 SNO
	private function _getCancelLastSno() {
		$sno = 0;
		foreach($this->getOrderCancels() as $cancel) {
			$sno = $cancel['sno'];
		}
		return $sno;
	}


	/**
	 * getFirstTaxAmounts()
	 * 복합과세 > 최초 주문시의 복합과세금액 반환
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access public
	 * @return array 최초 복합과세금액
	 *		taxall 전체과세금액
	 *		tax 과세금액
	 *		vat 부가세
	 *		taxfree 면세금액
	 */
	public function getFirstTaxAmounts() {
		return array(
			kTAXALL => $this->getTaxSettleAmount(),
			kTAXFREE => $this->getTaxFreeAmount(),
			kTAX => $this->getTaxAmount(),
			kVAT => $this->getVatAmount(),
		);
	}

	/**
	 * _setCaculateVat()
	 * 복합과세 > 최종 과세면세 배열에 VAT 추가 후 배열 반환
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access private
	 * @param array $data 기계산된 과세/면세 배열
	 * @return array 최초 복합과세금액
	 *		taxall 전체과세금액
	 *		tax 과세금액
	 *		vat 부가세
	 *		taxfree 면세금액
	 * @error array
	 *		code 에러코드
	 *		msg 에러메시지
	 */
	private function _setCalculateVat($data) {
		if (array_key_exists(kTAXALL, $data)) {
			// 면세금액이 음수로 나오는 경우 과세에 부과
			if ($data[kTAXFREE] < 0) {
				if ($data[kTAXALL] >= abs($data[kTAXFREE])) {
					$data[kTAXALL] += $data[kTAXFREE];
					$data[kTAXFREE] = 0;
				} else {
					// 오류
					$data = array(
						'code' => '902',
						'msg' => '취소 할 금액이 없습니다.',
					);
				}
			}

			// 과세금액이 음수로 나오는 경우 면세에 부과
			if ($data[kTAXALL] < 0) {
				if ($data[kTAXFREE] >= abs($data[kTAXALL])) {
					$data[kTAXFREE] += $data[kTAXALL];
					$data[kTAXALL] = 0;
				} else {
					// 오류
					$data = array(
						'code' => '902',
						'msg' => '취소 할 금액이 없습니다.',
					);
				}
			}

			if (!array_key_exists('code', $data)) {
				// 과세계산
				$data[kTAX] = ceil($data[kTAXALL]/1.1);
				$data[kVAT] = $data[kTAXALL] - $data[kTAX];
			}
		}
		return $data;
	}


	/**
	 * _setCalculateCancelingTaxAmounts()
	 * 복합과세 > SNO이전 시점의 리얼기준으로 현재 취소상품 복합과세금액 산출
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access private
	 * @param int $sno 취소시점의 SNO
	 * @param array $realdata SNO 이전의 리얼데이터
	 * @return array 최초 복합과세금액
	 *		taxall 전체과세금액
	 *		tax 과세금액
	 *		vat 부가세
	 *		taxfree 면세금액
	 * @error array
	 *		code 에러코드
	 *		msg 에러메시지
	 */
	private function _setCalculateCancelingTaxAmounts($sno, $realdata = null) {
		$cancel = $this->getOrderCancel($sno);
		if (!$cancel instanceof order_cancel) {
			$data = array(
				'code' => '901',
				'msg' => '해당 주문내에 취소SNO가 존재하지 않습니다.',
			);
		} else {
			// realdata가 없는 경우 이전 리얼금액 호출
			if (is_null($realdata)) {
				$realdata = $this->getRealTaxAmounts($sno);
			}

			// 마지막 주문취소 상품인 경우 할인 및 배송비를 계산할 수 있도록 세팅후 cancel.class로 던져줌
			$discount[kTAXFREE] = $discount[kTAXALL] = 0;
			if ($this->_is_all_canceled && $sno == $this->_getCancelLastSno()) {
				$discount[kTAXALL] += $this->getDeliveryFee() + $this['eggFee']; // 현과세금액 = 현과세금액 + 배송비 + 보증보험수수료
				$discount[kTAXALL] -= $this->getTaxDiscountAmount(); // 과세할인금액 = 현과세금액 - 과세할인금액
				$discount[kTAXFREE] -= $this->getTaxFreeDiscountAmount(); // 면세할인금액 = 현면세금액 - 면세할인금액

				// 네이버는 첫 취소시 빠지기때문에 최종적으로 더해줘야 한다.
				$discount[kTAXALL] += ($this->getNcashCashDiscount() + $this->getNcashEmoneyDiscount());
			}
			$data = $cancel->getCancelTaxAmounts($discount);

			// 남은 결제 면세금액이 취소 면세금액보다 작을 경우 과세에서 차감
			if ($realdata[kTAXFREE] < $data[kTAXFREE]) {
				$difference = $data[kTAXFREE] - $realdata[kTAXFREE];
				$data[kTAXALL] += $difference;
				$data[kTAXFREE] = $realdata[kTAXFREE];
			}

			// 남은 결제 과세금액이 취소 과세금액보다 작을 경우 면세에서 차감
			if ($realdata[kTAXALL] < $data[kTAXALL]) {
				$difference = $data[kTAXALL] - $realdata[kTAXALL];
				$data[kTAXFREE] += $difference;
				$data[kTAXALL] = $realdata[kTAXALL];
			}
		}

		return $this->_setCalculateVat($data);
	}


	/**
	 * getRealTaxAmounts()
	 * 복합과세 > 현재 남아있는 복합과세금액 반환 (취소완료된 것만 계산)
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access public
	 * @param int $sno 있는경우(해당 SNO 이전시점의 리얼), 없는경우(취소완료된 주문의 리얼)
	 * @param boolean $is_enamoo 이나무 계산 기준으로 처리 (주문상태의 취소완료/접수 기준)
	 * @return array 리얼 복합과세금액
	 *		taxall 전체과세금액
	 *		tax 과세금액
	 *		vat 부가세
	 *		taxfree 면세금액
	 * @error array
	 *		code 에러코드
	 *		msg 에러메시지
	 * @use
	 *		getRealTaxAmounts(SNO)
	 *			1) 해당 SNO 인자값이 0인 경우 : PG상으로 취소완료 후의 현재 남아있는 복합과세금액 반환
	 *			2) 해당 SNO의 주문취소가 PG완료인 경우 : SNO 시점까지의 취소후 남아있는 복합과세금액 반환
	 *			3) 해당 SNO의 주문취소가 PG완료가 아닌 경우 : 1)번과 동일
	 */
	public function getRealTaxAmounts($sno = 0, $is_enamoo = false) {
		// 최초 복합과세금액 할당 (초기화)
		$realdata = $this->getFirstTaxAmounts();

		foreach($this->getOrderCancels() as $cancel) {
			// SNO가 있는 경우 이전 리얼금액 반환처리를 위한 구문
			if (is_numeric($sno) && $sno > 0 && $cancel['sno'] == $sno) break;

			// 취소시점 (이나무의 취소가 아니면 무조건 PG취소완료 기준으로 세팅
			$is_cancel = $cancel->hasPgCanceled();
			if ($is_enamoo) {
				$is_cancel = $cancel->hasCancelCompleted();
			}

			// 취소완료된 리얼금액 = 남은금액 - 취소금액
			if ($is_cancel) {
				$data = $this->_setCalculateCancelingTaxAmounts($cancel['sno'], $realdata);

				// 취소부분 차감되고 남은금액
				$realdata[kTAXFREE] -= $data[kTAXFREE];
				$realdata[kTAXALL] -= $data[kTAXALL];
			}
		}

		return $this->_setCalculateVat($realdata);
	}


	/**
	 * getCancelItemTaxWithSno()
	 * 복합과세 > SNO의 취소 된 주문상품의 복합과세금액 반환 (해당 SNO의 주문취소가 PG완료인 경우)
	 * 복합과세 > SNO의 취소 할 주문상품의 복합과세금액 반환 (해당 SNO의 주문취소가 PG완료가 아닌 경우)
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access public
	 * @param int $sno 취소시점의 SNO
	 * @return array 최초 복합과세금액
	 *		taxall 전체과세금액
	 *		tax 과세금액
	 *		vat 부가세
	 *		taxfree 면세금액
	 * @error array
	 *		code 에러코드
	 *		msg 에러메시지
	 */
	public function getCancelItemTaxWithSno($sno) {
		return $this->_setCalculateCancelingTaxAmounts($sno);
	}

	public function getRealTaxAmountsPaycoAdd($sno = 0, $is_enamoo = false) {
		// 최초 복합과세금액 할당 (초기화)
		$realdata = $this->getFirstTaxAmounts();

		foreach($this->getOrderCancels() as $cancel) {
			// SNO가 있는 경우 이전 리얼금액 반환처리를 위한 구문
			if (is_numeric($sno) && $sno > 0 && $cancel['sno'] == $sno) break;

			// 취소시점 (이나무의 취소가 아니면 무조건 PG취소완료 기준으로 세팅
			$is_cancel = $cancel->hasPgCanceled();
			if ($is_enamoo) {
				$is_cancel = $cancel->hasCancelCompleted();
			}

			// 취소완료된 리얼금액 = 남은금액 - 취소금액
			if ($is_cancel) {
				$data = $this->_setCalculateCancelingTaxAmounts($cancel['sno'], $realdata);

				// 취소부분 차감되고 남은금액
				$realdata[kTAXFREE] -= $data[kTAXFREE];
				$realdata[kTAXALL] -= $data[kTAXALL];
			}
		}

		if($this->isPaycoPay()){
			$realdata[kTAXALL] -= $this->getPaycoCancelCompletedDeliveryFee();
		}

		return $this->_setCalculateVat($realdata);
	}

	public function getRedifineDeliveryExclude($taxPrice)
	{
		$deliveryFee = $deliverySurtax = 0;
		$deliveryFee = $this->getDeliveryFee() - $this->getCancelCompletedDeliveryFee();
		$taxPrice['taxall'] -= (int)$deliveryFee;
		$taxPrice['tax'] = ceil($taxPrice['taxall']/1.1);
		$taxPrice['vat'] = $taxPrice['taxall'] - $taxPrice['tax'];

		return array($taxPrice['taxall'], $taxPrice['tax'], $taxPrice['vat']);
	}

	public function hasPaycoExchange()
	{
		list($settle_inflow) = $this->_db->fetch("select settleInflow from " . GD_ORDER . " where ordno='{$this['oldordno']}'");
		if($settle_inflow == 'payco') return true;
		else return false;
	}
}
