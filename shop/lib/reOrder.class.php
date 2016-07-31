<?php

/**
 * 재주문 : 주문내역에 있는 상품들을 장바구니에 담는 기능
 * 구입 당시 상품의 정보와 현재 판매하고 있는 상품의 정보를 비교한 결과를 리턴
 */
class reOrder
{
	private $_db = null;
	private $_cart = null;
	
	/**
	 * 생성자
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = Core::loader('db');
		$this->_cart = Core::loader('Cart');
	}
	
	/**
	 * 주문서에 있는 상품 체크
	 * @param int $ordno 주문번호
	 * @return array
	 */
	public function chk_order($ordno)
	{
		$added = 0;				// 장바구니에 추가 된 상품 개수 체크
		$price_result = 0;		// 가격변경 된 상품 개수 체크
		$itemCount = 0;			// 상품 개수
		
		$orderItem = $this->_db->query("select goodsno, opt1, opt2, addopt, ea, price from ".GD_ORDER_ITEM." where ordno='".$ordno."'");
		$itemCount = mysql_affected_rows();

		while ($item = $this->_db->fetch($orderItem,1)) {
			
			// 상품 진열 여부 체크
			$chkOpen = 0;
			$chkOpen = chkOpenYn($item['goodsno'],'D',0,'true');
			if ($chkOpen == 1) {
				continue;
			}

			$price_chk = 0;		// 가격 변경을 체크하기 위한 변수
			$price_opt = 0;		// 가격옵션 가격
			$opt = array();		// 가격옵션 초기화

			// 가격 옵션
			$option = $this->_db->query("select price,sno from ".GD_GOODS_OPTION." where opt1='".mysql_real_escape_string($item['opt1'])."' and opt2='".mysql_real_escape_string($item['opt2'])."' and goodsno='".$item['goodsno']."' and go_is_display = '1' and go_is_deleted <> '1'");
			$optItem = $this->_db->fetch($option);

			// 가격옵션이 없어졌을 경우
			if ($item['opt1'] && !$optItem['sno']) {
				continue;
			}
			$price_opt = $optItem['price'];
			$opt[] = $item['opt1'];
			$opt[] = $item['opt2'];
			
			// 추가옵션 체크 함수 호출
			$addOptionRes = $this->chk_addOption($item['addopt'],$item['goodsno']);
		
			if ($addOptionRes['pass'] == '') {
				$added += $this->_cart->addCart($item['goodsno'], $opt, $addOptionRes['addopt'], $addOptionRes['addopt_inputable'], $item['ea'], 'true');
				$price_chk = $price_opt+$addOptionRes['price_addopt']+$addOptionRes['price_addopt_inputable'];

				// 가격이 바뀐 상품이 있는지 체크
				if($item['price'] != $price_chk){
					$price_result++;
				}
			}
		}
		$reOrderRes = array(
					'added'			=>	$added,
					'price_result'	=>	$price_result,
					'itemCount'		=>	$itemCount,
		);
		return $reOrderRes;
	}
	/**
	 * 추가옵션 체크
	 * @param int $itemGoodsno 상품 번호
	 * @param string $itemOpt 상품 추가옵션
	 * @return array
	 */	
	private function chk_addOption($itemOpt,$itemGoodsno)
	{
		
		$count = 0;						// 추가옵션과 입력옵션이 없어졌을 경우 담지 않기 위해 체크하는 변수
		$pass = '';						// 추가옵션을 장바구니에 담지 않고 넘기기 위한 변수
		$addopt = array();				// 추가옵션 초기화
		$addopt_inputable = array();	// 입력옵션 초기화
		$price_addopt = 0;				// 추가옵션 가격
		$price_addopt_inputable = 0;	// 입력옵션 가격
		
		// 주문내역에 있는 상품의 추가옵션과 입력옵션의 내용을 분리
		$addoption = explode('^',$itemOpt);

		$add = $this->_db->query("select a.type, a.sno, a.opt, a.addprice, a.step, g.addoptnm from ".GD_GOODS_ADD." a left join ".GD_GOODS." g on a.goodsno=g.goodsno where a.goodsno='".$itemGoodsno."'");
		while ($addItem = $this->_db->fetch($add,1)) {

			// 등록된 상품 정보에서 입력옵션과 추가옵션의 이름을 분리
			$addoptnms = explode('|',$addItem['addoptnm']);

			$addoptNm = array();
			$addoptReq = array();
			$addoptType = array();
			$chk_name = '';
			
			// 옵션 정보를 이름,구분자,타입별로 분류
			for ($a=0,$b=count($addoptnms); $a<$b; $a++) {
				list($addoptNm[], $addoptReq[], $addoptType[]) = explode('^',$addoptnms[$a]);
			}

			// 추가옵션 또는 입력옵션이 일치하는지 비교
			for ($i=0; $i<count($addoption); $i++) {
				$addoptInCart[$i] = str_replace(':','^',$addoption[$i]);			// addCart에 들어갈때의 형태
				list($chkAddNm[$i], $chkAddVal[$i]) = explode(':',$addoption[$i]);	// 비교하기 위한 형태

				// 추가옵션이 없이 상품을 구입했지만 필수체크 된 추가옵션 또는 입력옵션이 생겼을 경우
				if (strpos($addItem['addoptnm'],'^o^') > -1 && $addoption[$i] == null) {
					$pass = 'pass';
				}

				// 등록된 상품과 주문서내의 상품과 비교
				else if (in_array($chkAddNm[$i],$addoptNm) === true) {
					// 추가 옵션인지 체크
					if ($addItem['type'] === 'S' && $addItem['opt'] === $chkAddVal[$i]) {
						$addopt[] = $addItem['sno'].'^'.$addoptInCart[$i].'^'.$addItem['addprice'];
						$price_addopt += $addItem['addprice'];
					}
					// 입력 옵션인지, 글자수 제한에 걸리는지 체크
					else if ($addItem['type'] === 'I' && $addItem['opt'] >= mb_strlen($chkAddVal[$i],'euc-kr')) {
						$name_offset = $addItem['step'] + ($addItem['type'] === 'I' ? (int) array_search('I', $addoptType) : 0);
						$chk_name = $addoptNm[$name_offset];

						if($chk_name === $chkAddNm[$i]){
							$addopt_inputable[] = $addItem['sno'].'^'.$addoptInCart[$i].'^'.$addItem['addprice'];
							$price_addopt_inputable += $addItem['addprice'];
						}
					}
					else {}
				}
				// 상품의 입력옵션 또는 추가옵션의 이름이 변경 된것을 체크 
				else if ($addItem['addoptnm'] != null && $chkAddNm[$i] != null) {
					$pass = 'pass';
				}
				else {}
			}
			// 구입하지 않았던 추가옵션이 필수체크 되었을 경우
			if(array_search('o',$addoptReq) != false && in_array($addoptNm[array_search('o',$addoptReq)],$chkAddNm) === false){
				$pass = 'pass';
			}
		}
		// 주문내역에 있는 추가옵션들이 빠진게 있는지 확인
		if ($addoption[0] == null) {
			$count = 0;
		}
		else {
			$count = count($addoption);
		}
		
		if ($count != count($addopt)+count($addopt_inputable)) {
			$pass = 'pass';
		}

		$addOptionRes = array (
						'pass'					=>	$pass,
						'addopt'				=>	$addopt,
						'addopt_inputable'		=>	$addopt_inputable,
						'price_addopt'			=>	$price_addopt,
						'price_addopt_inputable'=>	$price_addopt_inputable,
		);
		return $addOptionRes;
	}
}
?>