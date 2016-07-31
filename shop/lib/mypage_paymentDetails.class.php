<?php
/*
 * mypage_paymentDetails CLASS
 *
 * @author mypage_paymentDetails.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-05-19
 */
class mypage_paymentDetails {

	var $order;

	function mypage_paymentDetails($ordno)
	{
		global $order;

		if(!is_object($order)){
			$order = Core::loader('order');
			$order->load($ordno);
		}

		//order class object
		$this->order = $order;
	}

	/*
	* 총 주문금액 = 주문금액(상품금액) - 취소완료된 주문금액(상품판매금액)
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getGoodsPrice()
	{
		$goodsprice = 0;
		$goodsprice = $this->order->getAmount() - $this->order->getCancelCompletedGoodsAmount();

		return $goodsprice;
	}

	/*
	* 적립금 사용금액 = 사용적립금 - 취소완료된 사용적립금
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getUseEmoney()
	{
		$emoney = 0;
		$emoney = $this->order['emoney'];
		$cnt = $cnt2 = 0;
		foreach ($this->order->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCancelCompleted()) {
				$cnt2++;
			}
		}
		if($cnt == $cnt2){
			$emoney -= $this->order['emoney'];
		}

		return $emoney;
	}

	/*
	* 결제금액 = 결제금액 - 취소완료된 결제금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getRealPrnSettlePrice()
	{
		$realPrnSettlePrice = 0;
		$multitax = array();
		$multitax = $this->order->getRealTaxAmountsPaycoAdd(0, true);

		$realPrnSettlePrice = $multitax['taxall'] + $multitax['taxfree'];

		return $realPrnSettlePrice;
	}

	/*
	* 취소완료된 취소금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getCanceled_price()
	{
		$canceled_price = 0;
		$canceled_price = $this->order->getCancelCompletedAmount();

		return $canceled_price;
	}

	/*
	* 상품할인 = 상품할인 - 취소완료된 상품할인
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getGoodsDc()
	{
		$goodsDc = 0;
		$goodsDc = $this->order->getGoodsDiscount() - $this->order->getCancelCompletedGoodsDiscount();

		return $goodsDc;
	}

	/*
	* 에누리
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getUseEnuri()
	{
		$enuri = 0;
		$enuri = $this->order->getEnuriAmount();

		return $enuri;
	}

	/*
	* 상품조정금액 = 결제금액 - 상품금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getDiffPrice()
	{
		$diffPrice = 0;
		$diffPrice = $this->order->getAmount()- $this->order['goodsprice'];

		return $diffPrice;
	}

	/*
	* 회원할인 = 회원할인 - 취소완료된 회원할인
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getMemberDc()
	{
		$memberdc = 0;
		$memberdc = $this->order->getMemberDiscount() - $this->order->getCancelCompletedMemberDiscount();

		return $memberdc;
	}

	/*
	* 쿠폰할인 = 쿠폰할인 - 취소완료된 쿠폰할인
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getCoupon()
	{
		$coupon = 0;
		$coupon = $this->order->getCouponDiscount() - $this->order->getCancelCompletedCouponDiscount();

		return $coupon;
	}

	/*
	* 배송비 = 배송비 - 취소완료된 배송비
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getDelivery()
	{
		$delivery = 0;
		$delivery = $this->order->getDeliveryFee() - $this->order->getCancelCompletedDeliveryFee();

		return $delivery;
	}

	/*
	* 취소접수금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getCancelingAmount()
	{
		$canceling_price = 0;
		$canceling_price = $this->order->getCancelingAmount();

		return $canceling_price;
	}

	/*
	* 취소시 결제금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getCancelingRealPrnSettleAmount()
	{
		$cancelingRealPrnSettleAmount = 0;
		$cancelingRealPrnSettleAmount = $this->order->getCancelingRealPrnSettleAmount();

		return $cancelingRealPrnSettleAmount;
	}

	/*
	* 상품할인금액, 회원할인금액, 쿠폰할인금액, 에누리금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getDiscount($type='')
	{
		$goodsDc = $memberdc = $coupon = $enuri = $emoney = 0;
		$goodsDc = $this->getGoodsDc();
		$memberdc = $this->getMemberDc();
		$coupon = $this->getCoupon();
		$enuri = $this->getUseEnuri();
		if($type == 'total'){
			$emoney = $this->getUseEmoney();
			return array($goodsDc, $memberdc, $coupon, $enuri, $emoney);
		}
		else {
			return array($goodsDc, $memberdc, $coupon, $enuri);
		}
	}

	/*
	* 취소금액, 취소접수금액, 취소시 결제금액
	* @param
	* @return int
	* @date 2015-05-19
	*/
	function getCancelMultiPrice()
	{
		$canceled_price = $canceling_price = $cancelingRealPrnSettleAmount = 0;

		//취소접수 존재여부
		if($this->order->getCancelingCount() > 0) {
			$canceling_price = $this->getCancelingAmount();
			$canceling_RealPrnSettlePrice = $this->getCancelingRealPrnSettleAmount();
		}

		$canceled_price = $this->getCanceled_price();

		return array($canceled_price, $canceling_price, $canceling_RealPrnSettlePrice);
	}
}
?>