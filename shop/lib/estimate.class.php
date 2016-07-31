<?php
/**
 * 견적서 : 장바구니에 담은 상품들의 견적서를 만듬
 */

class estimate
{
	private $_db = null;

	/**
	 * 생성자
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = Core::loader('db');
	}

	/**
	 * 숫자를 한글로 변환하는 함수
	 * @return string
	 */
	public function transNum($totalPrice)
	{
		$number_arr = array('','일','이','삼','사','오','육','칠','팔','구');

		// 천자리 이하 자리 수의 한글 배열
		$unit_arr1 = array('','십','백','천');

		// 만자리 이상 자리 수의 한글 배열
		$unit_arr2 = array('','만','억','조','경','해');

		// 인자값을 역순으로 배열한 후, 4자리 기준으로 나눔
		$reverse_arr = str_split(strrev($totalPrice), 4);

		$result = array();	// 결과값 배열 변수
		$result_idx = 0;	// 만자리 단위 생성 변수

		foreach ($reverse_arr as $reverse_idx=>$reverse_number) {
			// 1자리씩 나눔
			$convert_arr = str_split($reverse_number);
			$convert_idx = 0;

			foreach ($convert_arr as $split_idx=>$split_number) {
				// 해당 숫자가 0일 경우 처리되지 않음
				if (!empty($number_arr[$split_number])) {
					// 0부터 9까지 숫자 한글로 변경 그리고 천자리 이하 단위 생성
					$result[$result_idx] = $number_arr[$split_number].$unit_arr1[$split_idx];

					// 만자리 이상 단위 생성
					if (empty($convert_idx)) $result[$result_idx] .= $unit_arr2[$reverse_idx];
					$convert_idx++;
				}
				$result_idx++;
			}
		}

		// 배열 역순으로 재정렬 후 합침
		$result = implode('', array_reverse($result));
		return $result;
	}
	
	/**
	 * 사용자 Name 가져오는 함수
	 * @return string
	 */
	public function getName($m_no)
	{
		$query = " select * from ".GD_MEMBER." a left join ".GD_MEMBER_GRP." b on a.level=b.level where m_no='$m_no'";
		$member = $this->_db->fetch($query,1);
		return $member['name'];
	}

	/**
	 * 체크 된 상품 재정렬
	 * @return array
	 */
	public function getGoods($item,$idxs)
	{
		$count = 1;
		$goods = array();	// 상품이 재정렬될 배열
		foreach ($idxs as $value) {
			$item[$value]['idxs'] = $count;
			$goods[$count] = $item[$value];
			$count++;
		}

		return $goods;
	}

	/**
	 * 과세 여부 가져오는 함수
	 * @return array
	 */
	public function getTax($item)
	{
		$tax = '';
		$goodsno = '';

		for ($i=1; $i<=count($item); $i++) {
			$goodsno = $item[$i]['goodsno'];
			$query = "select tax from ".GD_GOODS." where goodsno='$goodsno'";
			list($item[$i]['tax']) = $this->_db->fetch($query);	// 과세여부를 item 배열에 추가
		}

		return $item;
	}

	/**
	 * 합계 금액 계산
	 * @return int
	 */
	public function totalPrice($item)
	{
		$totalPrice = 0;	// 합계금액
		foreach ($item as $goods) {
			$totalPrice += $goods['price']*$goods['ea'];	// 상품 가격 합산
			if ($goods['addprice']) $totalPrice += $goods['addprice']*$goods['ea'];	// 옵션 추가 가격 합산
		}

		return $totalPrice;
	}

	/**
	 * 공급가액 계산
	 * @return array
	 */
	public function supplyPrice($item)
	{
		for ($i=1; $i<=count($item); $i++) {
			$supplyPrice = 0;	// 공급가액
			$supplyPrice += $item[$i]['price']*$item[$i]['ea'];	// 상품 가격 합산
			if ($item[$i]['addprice']) $supplyPrice += $item[$i]['addprice']*$item[$i]['ea'];	// 옵션 추가 가격 합산
			if ($item[$i]['tax'] === '1') $supplyPrice = ($supplyPrice/1.1);	// 과세 상품 부가세 적용
			$item[$i]['supply'] = ceil($supplyPrice);
		}

		return $item;
	}

	/**
	 * 공급가액 합산
	 * @return string
	 */
	public function totalSupplyPrice($item)
	{
		$totalSupplyPrice = 0;
		foreach ($item as $goods) {
			$totalSupplyPrice += $goods['supply'];
		}

		return $totalSupplyPrice;
	}

	/**
	 * HTML 태그제거
	 * @return array
	 */
	public function tagStrip($item)
	{
		for ($i=1; $i<=count($item); $i++) {
			$item[$i]['goodsnm'] = strip_tags($item[$i]['goodsnm']);
		}

		return $item;
	}


}
?>
