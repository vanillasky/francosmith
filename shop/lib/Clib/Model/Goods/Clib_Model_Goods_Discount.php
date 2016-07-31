<?php
/**
 * Clib_Model_Goods_Discount
 * @author extacy @ godosoft development team.
 */
class Clib_Model_Goods_Discount extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'gd_goodsno';

	protected $objectRelationMapping = array('goods' => array('modelName' => 'goods', ),
		/*'options' => array(
		 'modelName' => 'goods_option',
		 'isCollection' => true,
		 'foreignColumn'=>'goodsno'
		 ),*/
	);
	/**
	 * 할인 적용 대상을 리턴
	 * @param integer $idx [optional]
	 * @return array|string
	 */
	public function getLevel($idx = null)
	{
		$tmp = explode(',', $this->getGdLevel());
		return is_null($idx) ? $tmp : $tmp[$idx];
	}

	/**
	 * 할인 금액을 리턴
	 * @param integer $idx [optional]
	 * @return array|string
	 */
	public function getAmount($idx = null)
	{
		$tmp = explode(',', $this->getGdAmount());
		return is_null($idx) ? $tmp : $tmp[$idx];
	}

	/**
	 * 할인액 연산 방법을 리턴
	 * @param integer $idx [optional]
	 * @return array|string
	 */
	public function getUnit($idx = null)
	{
		$tmp = explode(',', $this->getGdUnit());
		return is_null($idx) ? $tmp : $tmp[$idx];
	}

	/**
	 * 회원 등급명을 리턴
	 * @param integer $idx [optional] 회원 등릅
	 * @return string
	 */
	public function getLevelLabel($idx = null)
	{
		static $labels = array();

		$level = $this->getLevel($idx);

		if ($level == '*') {
			$labels[$level] = '회원전체';
		}
		else if ($level == '0') {
			$labels[$level] = '회원 및 비회원 전체';
		}
		else {
		
			if ( ! isset($labels[$level])) {
				$memberGroup = Clib_Application::getModelClass('member_group');
				$memberGroup->loadByLevel($this->getLevel($idx));

				$labels[$level] = $memberGroup->getData('grpnm');
			}
		}

		return $labels[$level];

	}

	/**
	 *
	 * @param Clib_Model_Goods_Abstract|array $goods
	 * @param integer $memberLevel [optional]
	 * @return integer|false
	 */
	public function getDiscountAmount($goods, $memberLevel = null)
	{
		if ($this->hasLoaded() && $this->_canDiscount()) {

			if (is_null($memberLevel)) {
				$memberLevel = Clib_Application::session()->getMemberLevel();
			}

			settype($memberLevel,'string');

			foreach ($this->getLevel() as $idx => $level) {
				if (($level === '*' && $memberLevel > 0) || $level == $memberLevel || $level === '0') {
					$amount = $this->getAmount($idx);
					$unit = $this->getUnit($idx);
					break;
				}
			}

			if ($amount && $unit) {

				if ($goods instanceof Clib_Model_Goods_Abstract) {
					$price = $goods->getPrice();
				}
				else if (is_array($goods)) {
					$price = $goods['price'];
				}
				else {
					$price = 0;
				}

				switch ($unit) {
					case '%' :
						$special = $price * $amount / 100;
						break;

					case '=' :
						$special = $amount;
						break;
				}

				return Clib_Application::iapi('number')->getCuttedNumberFromConfigString($special, $this->getGdCutting());

			}

		}

		return 0;

	}

	private function _canDiscount()
	{
		$can = true;

		// 시작일, 종료일
		$_start = (int)$this->getData('gd_start_date');
		$_end   = (int)$this->getData('gd_end_date');

		// 미시작
		if ($_start > 0 && $_start > G_CONST_NOW) {
			$can = false;
		}

		// 종료
		if ($_end > 0 && $_end < G_CONST_NOW) {
			$can = false;
		}

		return $can;

	}

	// 상품검색 - 상품할인정보
	public function getDiscountAmountSearch($goods, $memberLevel = null)
	{
		$DiscountResource = Clib_Application::getResourceClass('goods_goods');
		$this->setData($DiscountResource->getDiscountInformation($goods['goodsno']));
		$this->setLoaded(true);

		if ($this->hasLoaded() && $this->_canDiscount()) {

			if (is_null($memberLevel)) {
				$memberLevel = Clib_Application::session()->getMemberLevel();
			}

			settype($memberLevel,'string');

			foreach ($this->getLevel() as $idx => $level) {
				if (($level === '*' && $memberLevel > 0) || $level == $memberLevel || $level === '0') {
					$amount = $this->getAmount($idx);
					$unit = $this->getUnit($idx);
					break;
				}
			}


			if ($amount && $unit) {

				if ($goods instanceof Clib_Model_Goods_Abstract) {
					$price = $goods->getPrice();
				}
				else if (is_array($goods)) {
					$price = $goods['price'];
				}
				else {
					$price = 0;
				}

				switch ($unit) {
					case '%' :
						$special = $price * $amount / 100;
						break;

					case '=' :
						$special = $amount;
						break;
				}

				return Clib_Application::iapi('number')->getCuttedNumberFromConfigString($special, $this->getGdCutting());

			}

		}

		return 0;

	}

	// 모바일 - 상품할인정보
	public function getDiscountUnit($goods, $memberLevel = null)
	{
		$this->setData(Clib_Application::getResourceClass('goods_goods')->getDiscountInformation($goods['goodsno']));

		if (is_null($memberLevel)) {
			$memberLevel = Clib_Application::session()->getMemberLevel();
		}

		settype($memberLevel,'string');

		foreach ($this->getLevel() as $idx => $level) {
			if (($level === '*' && $memberLevel > 0) || $level == $memberLevel || $level === '0') {
				$amount = $this->getAmount($idx);
				$unit = $this->getUnit($idx);
				break;
			}
		}
		
		
		if ($amount && $unit) {
			switch ($unit) {
				case '%' :
					return $amount . "%" ;
				break;

				case '=' :
					return $amount . "원" ;
				break;
			}
		}

		return 0;

	}

}
