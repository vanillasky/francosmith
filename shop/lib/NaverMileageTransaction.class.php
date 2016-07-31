<?php

/**
 * 네이버 마일리지 처리 클래스
 *
 * @TODO : 차후 naverNcash.class.php에 있는 내용중 API관련 내용만 추려서 현재 파일로 옮겨야 함
 *
 * @author NaverMileageTransaction.class.php workingparksee <parksee@godo.co.kr>
 * @version 1.0
 * @date 2013-02-19, 2013-02-19
 */
class NaverMileageTransaction
{

	private $config;

	private $ordno, $transactionId, $saveMode, $orderAmount, $calcAmount, $orderItem = array(), $serializedOrderItem, $mileageUseAmount, $cashUseAmount, $discountAmount;

	/**
	 * NaverMileageTransaction 객체를 생성함
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $ordno 이나무 주문번호
	 * @date 2013-02-19, 2013-02-19
	 */
	public function __construct($ordno)
	{
		$this->config = Core::loader('config')->load('ncash');

		$this->setOrdno($ordno);

		// 전달된 주문번호로 된 트랜젝션이 있는경우 조회
		$db = Core::loader('db');
		$nmtxResultSet = $db->query('SELECT * FROM gd_naver_mileage_transaction WHERE nm_ordno='.$ordno.' LIMIT 1');
		if ($naverMileageTransaction = $db->fetch($nmtxResultSet, true)) {
			$this->setTransactionId($naverMileageTransaction['nm_transaction_id']);
			$this->setSaveMode($naverMileageTransaction['nm_save_mode']);
			$this->setOrderAmount($naverMileageTransaction['nm_order_amount']);
			$this->setCalcAmount($naverMileageTransaction['nm_calc_amount']);
			foreach (unserialize($naverMileageTransaction['nm_serialized_order_item']) as $orderItem) {
				$this->addOrderItem($orderItem['goodsno'], $orderItem['goodsnm'], $orderItem['ea'], $orderItem['price']);
			}
			$this->serializeOrderItem();
			$this->setMileageUseAmount($naverMileageTransaction['nm_mileage_use_amount']);
			$this->setCashUseAmount($naverMileageTransaction['nm_cash_use_amount']);
			$this->setDiscountAmount($naverMileageTransaction['nm_discount_amount']);
		}
	}

	/**
	 * 네이버 마일리지가 사용/적립된 주문건의 주문번호 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $ordno 이나무 주문번호
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setOrdno($ordno)
	{
		$this->ordno = $ordno;
	}

	/**
	 * 네이버 마일리지 트랜젝션의 주문번호 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int 이나무 주문번호
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getOrdno()
	{
		return $this->ordno;
	}

	/**
	 * 네이버 마일리지 사용/적립 트랜젝션 아이디 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $transactionId 네이버 마일리지 txId
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setTransactionId($transactionId)
	{
		$this->transactionId = $transactionId;
	}

	/**
	 * 네이버 마일리지 트랜젝션의 아이디 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string 네이버 마일리지 txId
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getTransactionId()
	{
		return $this->transactionId;
	}

	/**
	 * 적립금 적립위치 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $saveMode 적립위치 naver_mileage_only : 네이버 마일리지만, both : 쇼핑몰과 네이버 마일리지 둘다 적립
	 * @date 2013-02-19, 2013-02-20
	 */
	public function setSaveMode($saveMode)
	{
		$this->saveMode = $saveMode;
	}

	/**
	 * 적립금 적립위치 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string 적립위치 naver_mileage_only : 네이버 마일리지만, both : 쇼핑몰과 네이버 마일리지 둘다 적립
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getSaveMode()
	{
		return $this->saveMode;
	}

	/**
	 * 총 주문금액 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $orderAmount 총 주문금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setOrderAmount($orderAmount)
	{
		$this->orderAmount = $orderAmount;
	}

	/**
	 * 총 주문금액 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int 총 주문금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getOrderAmount()
	{
		return $this->orderAmount;
	}

	/**
	 * 네이버 마일리지 적립대상금액 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $calcAmount 네이버 마일리지 적립대상금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setCalcAmount($calcAmount)
	{
		$this->calcAmount = $calcAmount;
	}

	/**
	 * 네이버 마일리지 적립대상금액 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int 네이버 마일리지 적립대상금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getCalcAmount()
	{
		return $this->calcAmount;
	}

	/**
	 * 주문상품 추가
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $goodsno 상품번호
	 * @param string $goodsnm 상품명
	 * @param int $ea 구매수량
	 * @param int $price 상품단가
	 * @date 2013-02-19, 2013-02-19
	 */
	public function addOrderItem($goodsno, $goodsnm, $ea, $price)
	{
		$this->orderItem[] = array(
		    'goodsno' => $goodsno,
		    'goodsnm' => $goodsnm,
		    'ea' => $ea,
		    'price' => $price,
		);
	}

	/**
	 * 추가된 주문상품을 직렬화
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string
	 * @date 2013-02-19, 2013-02-19
	 */
	public function serializeOrderItem()
	{
		$this->serializedOrderItem = serialize($this->orderItem);
		return $this->serializedOrderItem;
	}

	/**
	 * 직렬화된 주문상품을 병렬화
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return array
	 * @date 2013-02-19, 2013-02-19
	 */
	public function unserializeOrderItem()
	{
		$this->orderItem = unserialize($this->serializedOrderItem);
		return $this->orderItem;
	}

	/**
	 * 주어진 인덱스에 해당하는 주문상품 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $index
	 * @return array 'goodsno' => int, 'goodsnm' => string, 'ea' => int, 'price' => int
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getOrderItem($index)
	{
		return $this->orderItem[$index];
	}

	/**
	 * 네이버 마일리지 사용금액 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $mileageUseAmount 네이버 마일리지 사용금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setMileageUseAmount($mileageUseAmount)
	{
		$this->mileageUseAmount = $mileageUseAmount;
	}

	/**
	 * 네이버 마일리지 사용금액 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int 네이버 마일리지 사용금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getMileageUseAmount()
	{
		return $this->mileageUseAmount;
	}

	/**
	 * 네이버 캐쉬 사용금액 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $cashUseAmount 네이버 캐쉬 사용금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setCashUseAmount($cashUseAmount)
	{
		$this->cashUseAmount = $cashUseAmount;
	}

	/**
	 * 네이버 캐쉬 사용금액 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int 네이버 캐쉬 사용금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getCashUseAmount()
	{
		return $this->cashUseAmount;
	}

	/**
	 * 이나무에서 할인받은 금액 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $discountAmount 이나무에서 할인받은 금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setDiscountAmount($discountAmount)
	{
		$this->discountAmount = $discountAmount;
	}

	/**
	 * 이나무에서 할인받은 금액 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int 이나무에서 할인받은 금액
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getDiscountAmount()
	{
		return $this->discountAmount;
	}

	/**
	 * 설정된 정보를 통하여 트랜젝션을 생성
	 * @author workingparksee <parksee@godo.co.kr>
	 * @date 2013-02-19, 2013-02-19
	 */
	public function createTransaction()
	{
		$db = Core::loader('db');
		$db->query('
			INSERT gd_naver_mileage_transaction SET
			nm_ordno = '.$this->getOrdno().',
			nm_transaction_id = "'.$this->getTransactionId().'",
			nm_save_mode = "'.$this->getSaveMode().'",
			nm_order_amount = '.$this->getOrderAmount().',
			nm_calc_amount = '.$this->getCalcAmount().',
			nm_serialized_order_item = "'.mysql_real_escape_string($this->serializeOrderItem()).'",
			nm_mileage_use_amount = '.$this->getMileageUseAmount().',
			nm_cash_use_amount = '.$this->getCashUseAmount().',
			nm_discount_amount = '.$this->getDiscountAmount().'
		');
	}

	/**
	 * 기존 SESSION 방식의 처리방법을 호환하기 위한 적립금 적립위치 설정
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $saveMode 적립위치 ncash : 네이버 마일리지만, both : 쇼핑몰과 네이버 마일리지 둘다 적립
	 * @date 2013-02-20, 2013-02-20
	 */
	public function setLegacySaveMode($saveMode)
	{
		switch ($saveMode) {
			case 'ncash':
				$this->saveMode = 'naver_mileage_only';
				break;
			case 'both':
				$this->saveMode = 'both';
				break;
			default :
				//throw new Exception('잘못된 적립위치입니다.');
				return false;
				break;
		}
	}

	/**
	 * 기존 SESSION 방식의 처리방법을 호환하기 위한 적립금 적립위치 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string 적립위치 ncash : 네이버 마일리지만, both : 쇼핑몰과 네이버 마일리지 둘다 적립
	 * @date 2013-02-20, 2013-02-20
	 */
	public function getLegacySaveMode()
	{
		switch ($this->getSaveMode()) {
			case 'naver_mileage_only':
				$saveMode = 'ncash';
				break;
			case 'both':
				$saveMode = 'both';
				break;
			default :
				//throw new Exception('잘못된 적립위치입니다.');
				return false;
				break;
		}
		return $saveMode;
	}

	/**
	 * 기존 SESSION 방식의 처리방법을 호환하기 위해 생성된 트랜젝션 객체를 배열로 반환
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return array
	 * @date 2013-02-20, 2013-02-20
	 */
	public function toLegacyArray()
	{
		$naverMileageTransaction = array();
		$naverMileageTransaction['ordno'] = $this->getOrdno();
		$naverMileageTransaction['save_mode'] = $this->getLegacySaveMode();
		$naverMileageTransaction['goodsPrice'] = $this->getCalcAmount();
		$naverMileageTransaction['orderAmount'] = $this->getOrderAmount();
		$naverMileageTransaction['discountAmount'] = $this->getDiscountAmount();
		$naverMileageTransaction['item'] = $this->unserializeOrderItem();
		$naverMileageTransaction['mileageUseAmount'.$this->config['api_id']] = $this->getMileageUseAmount();
		$naverMileageTransaction['cashUseAmount'.$this->config['api_id']] = $this->getCashUseAmount();
		$naverMileageTransaction['reqTxId'.$this->config['api_id']] = $this->getTransactionId();
		return $naverMileageTransaction;
	}

}

?>