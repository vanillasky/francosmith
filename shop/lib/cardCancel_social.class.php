<?
/**
	2011-02-08 by x-ta-c
	투데이샵 주문취소 클래스
	- 대부분 처리 방식이 같으나 일부 다른 값을 필요로 하므로 상속 클래스로 구현.
 */
class cardCancel_social extends cardCancel {

	function cardCancel_social() {

		// pg 정보
		$todayShop = Core::loader('todayshop');
		$tsCfg = $todayShop->cfg;

		$tsPG = ($tsCfg['pg'] != '') ? unserialize($tsCfg['pg']) : array();

		if (!empty($tsPG)) {
			foreach($tsPG as $k => $v) {
				if ($k == 'mode') continue;
				$this->$k = $GLOBALS[$k];
				$this->$k = array_merge((array)$this->$k,$v);
			}

			$this->cpg = $this->pg;

		}
		$this->cancel_code = 10;	// 거래 미성사

		$this->shopdir = substr(dirname(__FILE__),0,-4);
		$this->pg_dir = 'todayshop/card';

	}


	// 테스트 함수.
	function _cancel_pg($ordno){

		return rand(0,1);

	}

	// cardCancel 의 멤버 중 오버라이딩이 필요한건 여기서 부터..




	//
}
?>
