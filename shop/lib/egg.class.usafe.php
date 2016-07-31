<?

/**
 * Egg class
 * 전자보증보험 클래스
 *    - PHP 설정중 allow_url_fopen is enabled 허용되어야 합니다.
 *    - TEST URL : http://gateway2.usafe.co.kr/esafe/guartrn.asp
 *    - SERVICE URL : https://gateway.usafe.co.kr/esafe/guartrn.asp
 */

class Egg
{
	var $eggData, $set, $isErr, $errMsg;

	function Egg($act, $eggData)
	{
		$this->eggData = $eggData;

		### 환경정보
		ob_start();
		include dirname(__FILE__)."/../conf/egg.usafe.php";
		$this->set = $egg;
		ob_end_clean();

		### 함수실행
		$this->$act();
		if ( $this->errMsg ) $this->isErr = true;
	}

	### 문자열 자르기 함수
	function strcut($str,$len)
	{
		$str = str_replace(" ", "", $str);
		if (strlen($str) > $len){
			$len = $len-2;
			for ($pos=$len;$pos>0 && ord($str[$pos-1])>=127;$pos--);
			if (($len-$pos)%2 == 0) $str = substr($str, 0, $len);
			else $str = substr($str, 0, $len+1);
		}
		return $str;
	}

	### 보증서 발급
	function create()
	{
		if ( $this->eggData['payInfo1'] != '' && $this->eggData['payInfo2'] != '' ){
			ob_start();
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET eggpginfo='{$this->eggData['payInfo1']}|{$this->eggData['payInfo2']}' WHERE ordno='{$this->eggData['ordno']}'");
			ob_end_clean();
		}

		$this->paymethod	= array(
				"a"	=> "MON", # 무통장
				"c"	=> "CAD", # 신용카드
				"o"	=> "BMC", # 계좌이체
				"v"	=> "CAS", # 가상계좌
				);

		if ( $this->set['use'] != 'Y' ){ $this->errMsg = '사용설정 필요'; return; }
		if ( $this->set['usafeid'] == '' ){ $this->errMsg = 'U-Safe ID 필요'; return; }
		if ( $this->set['scope'] == 'P' && $this->eggData['issue'] != 'Y' ){ $this->errMsg = '발급동의 필요'; return; }
		if ( $this->eggData['agree'] != 'Y' ){ $this->errMsg = '개인정보동의 필요'; return; }

		### 주문건 체크
		ob_start();
		$data = $GLOBALS[db]->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno='{$this->eggData['ordno']}'", "ASSOC");
		if ( ob_get_clean() ) return;
		if ( $data[ordno] == '' ){ $this->errMsg = '주문번호 필요'; return; }

		/***************************************************************************************************
		*  증권발급 수신 전문
		*    - 0|증권번호		, 성공시
		*    - 1|Error Message	, 실패시
		***************************************************************************************************/
		$gubun 			= "A0";										# (*) 전문구분 : 발급
		$mallId			= $this->set[usafeid];						# (*) 쇼핑몰 아이디(U-Safe ID)
		$oId			= $this->strcut($data[ordno], 30);			# (*) 주문번호
		$totalMoney		= $this->strcut($data[settleprice] - $data[eggFee], 9);		# (*) 결제금액
		$pId			= $this->strcut(decode($this->eggData['resno1'],1) . decode($this->eggData['resno2'],1), 13);	# (*) 주민등록번호
		$paymethod		= $this->paymethod[ $data[settlekind] ];	# (*) 결제방법

		if ( in_array($data[settlekind],array("c","o","v")) ){
			if ( $data[eggpginfo] != '' && $this->eggData['payInfo1'] == '' && $this->eggData['payInfo2'] == '' ){
				$tmp = explode("|", $data[eggpginfo]);
				$this->eggData['payInfo1'] = $tmp[0];
				$this->eggData['payInfo2'] = $tmp[1];
			}
			$payInfo1 = $this->strcut($this->eggData['payInfo1'], 5);	# (*) 결제정보(카드사 / 은행명 / 은행명)
			$payInfo2 = $this->strcut($this->eggData['payInfo2'], 20);	# (*) 결제정보(승인번호 / 승인번호 / 계좌번호)
		}
		else if ( $data[settlekind] == "a" ){
			ob_start();
			list($payInfo1, $payInfo2) = $GLOBALS[db]->fetch("SELECT bank, account FROM ".GD_LIST_BANK." WHERE sno='{$data[bankAccount]}'");
			$payInfo1 = $this->strcut($payInfo1, 5);	# (*) 결제정보(은행명)
			$payInfo2 = $this->strcut($payInfo2, 20);	# (*) 결제정보(계좌번호)
			ob_end_clean();
		}
		else {
			$this->errMsg = '미지원 결제수단';
			return;
		}

		$orderNm		= $this->strcut($data[nameOrder], 20);			# (*) 주문자명
		$orderHomeTel	= $this->strcut($data[phoneOrder], 20);			# (*) 주문자전화1
		$orderHpTel		= $this->strcut($data[mobileOrder], 20);		# 주문자전화2
		$orderZip		= $this->strcut(str_replace("-", "", $data[zipcode]), 6);	# (*) 주문자우편번호
		$orderAddress	= $this->strcut(urlencode($data[address]), 80);	# (*) 주문자주소
		$orderEmail		= $this->strcut($data[email], 40);				# 주문자email

		$acceptor		= $this->strcut($data[nameReceiver], 20);		# 수령인명
		$deliveryTel1	= $this->strcut($data[phoneReceiver], 20);		# 수령인전화1
		$deliveryTel2	= $this->strcut($data[mobileReceiver], 20);		# 수령인전화2
		$sign			= "YNN";										# (*) 개인정보동의(개인정보동의/Email수신동의/SMS수신동의)

		ob_start();
		$tmp = array();
		$ires = $GLOBALS[db]->query("select goodsnm, price, ea from ".GD_ORDER_ITEM." where ordno='{$data[ordno]}'");
		while ($idata=$GLOBALS[db]->fetch($ires)){
			$tmp[goodsName][]		= urlencode($this->strcut(strip_tags($idata[goodsnm]), 100));
			$tmp[goodsPrice][]		= $this->strcut($idata[price], 9);
			$tmp[goodsQuantity][]	= $this->strcut($idata[ea], 4);
		}
		$goodsName		= implode("|", $tmp[goodsName]);				# (*) 상품명
		$goodsPrice		= implode("|", $tmp[goodsPrice]);				# (*) 상품단가
		$goodsQuantity	= implode("|", $tmp[goodsQuantity]);			# (*) 상품수량
		$goodsCount		= count($tmp[goodsName]);						# (*) 상품종류수
		ob_end_clean();

		ob_start();
		$url = "https://gateway.usafe.co.kr/esafe/guartrn.asp?gubun=$gubun&mallId=$mallId&oId=$oId&totalMoney=$totalMoney&pId=$pId&paymethod=$paymethod&pay_Info1=$payInfo1&pay_Info2=$payInfo2&order_Nm=$orderNm&order_HomeTel=$orderHomeTel&order_HpTel=$orderHpTel&order_Zip=$orderZip&order_Address=$orderAddress&order_Email=$orderEmail&goodsCount=$goodsCount&acceptor=$acceptor&deliveryTel1=$deliveryTel1&deliveryTel2=$deliveryTel2&goodsName=$goodsName&goodsPrice=$goodsPrice&goodsQuantity=$goodsQuantity&sign=$sign";
		$out = readurl($url);
		$err = ob_get_clean();

		list($result_code, $result_msg) = explode("|", $out);
		if ( $result_code != '' && $result_msg != '' ){
			$settlelog .= "\n전자보증보험 발급 (".date('Y:m:d H:i:s').")\n";
			$settlelog .= "----------------------------------------\n";
			$settlelog .= "결과코드 : {$result_code} (0(성공) 그외 실패)\n";
			$settlelog .= "결과메세지 : {$result_msg}\n";
			$settlelog .= "----------------------------------------\n";
		}

		if ( $result_code == 0 && $result_msg != '' && $err == '' ){
			ob_start();
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET eggyn='y', eggno='{$result_msg}', settlelog=concat(ifnull(settlelog,''),'{$settlelog}') WHERE ordno='{$data[ordno]}'");
			ob_end_clean();
		}
		else {
			if ( $result_msg != '' ){
				$this->errMsg = $result_msg;
			}
			else if ( $err != '' ){
				$settlelog .= "\n전자보증보험 발급 (".date('Y:m:d H:i:s').")\n";
				$settlelog .= "----------------------------------------\n";
				$settlelog .= "결과메세지 : 통신장애\n";
				$settlelog .= "----------------------------------------\n";
				$this->errMsg = '통신장애';
			}
			ob_start();
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET eggyn='f', settlelog=concat(ifnull(settlelog,''),'{$settlelog}') WHERE ordno='{$data[ordno]}'");
			ob_end_clean();
		}
	}

}

?>