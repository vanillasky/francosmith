<?php
require_once("plusCheeseEncrypter.class.php");

class plusCheese {
	var $godosno = 0;
	var $_url = "http://pluscheese.godo.co.kr/listen.shop.php";

	//생성.
	//$sno : godosno
	function plusCheese($sno=-1)
	{
		if($sno == -1){
			global $godo;
			$this->sno = $godo['sno'];
		}else{
			$this->sno = $sno;
		}
		$this->fpath = dirname(__FILE__);

		//설정파일 읽기
		if(file_exists($this->fpath."/../conf/config.plusCheeseCfg.php"))
			require $this->fpath."/../conf/config.plusCheeseCfg.php";
		$this->plusCheeseCfg = $plusCheeseCfg;
/*
		//고도서버에서 상태 읽기
		if(!empty($plusCheeseCfg) &&  (strtoupper($plusCheeseCfg['use']) == "Y" || strtoupper($plusCheeseCfg['test']) == "Y") ){
			$this->plusCheeseCfg = $plusCheeseCfg;
			$data = readurl($this->_url."?mode=status&godoid=".$this->getRelayKey());
			if(substr($data, 0, 4) == "DONE"){
				$data = substr($data, 4);
				$data = unserialize($data);
				$this->data = $data;
			}else{
				msg($data, -1);
			}
		}
*/
	}

	//중계키 구하기
	function getRelayKey(){
		return $this->plusCheeseCfg['key'];
	}
	
	//현재 상태 불러오기(전체)
	function getStatus(){
		//return $this->data;
		return ""; 
	}
	
	//현재 상태 불러오기(플러스치즈 업체 ID)
	function getStatusID(){
		//return $this->data['pc_entID'];
		return ""; 
	}
	
	//현재 상태 불러오기(수수료)
	function getStatusCom(){
		return $this->data['pc_commission'];
	}
	
	//현재 상태 불러오기(암호화키)
	function getStatusEncKey(){
		return $this->data['pc_key'];
	}
	
	//현재 상태 불러오기(샵 신청 상태)
	function getStatusCond(){
		//return $this->data['pc_condCode'];
		return ""; 
	}

	//현재 상태 불러오기(한글 메시지)
	function getStatusCondMsg($msg){
		if($msg == "Y"){
			return "승인 완료(사용가능)";
		}else if($msg == "A"){
			return "승인 대기";
		}else if($msg == "N"){
			return "승인 실패";
		}else if($msg == "C"){
			return "서비스 해지 (재가입 문의 : 070-7123-6015)";
		}else{
			return("알 수 없음(".$msg.")");
		}
	}
	
	//현재 상태 불러오기(신청날짜)
	function getStatusADate(){
		return $this->data['keyAssignDate'];
	}
	
	//현재 상태 불러오기(승인날짜)
	function getStatusRDate(){
		return $this->data['approveDate'];
	}
	
	//현재 상태 불러오기(반려날짜)
	function getStatusDDate(){
		return $this->data['denialDate'];
	}

	//암호화
	function encrypt($str){
		$encrypter = new plusCheeseEncrypter($this->getStatusEncKey());
		$encrypted = $encrypter->encrypt($str);
		return $encrypted;
	}

	//복호화
	function decrypt($str){
		$decrypter = new plusCheeseEncrypter($this->getStatusEncKey());
		$decrypter = $decrypter->decrypt($str);
		return $decrypter;
	}

	//날짜형식 변경(YYYY-MM-DD HH:II:SS -> YYYYMMDD_HHIISS)
	function dateFormatToXML($date){
		return str_replace("-", "", substr($date, 0, 10))."_".str_replace(":", "", substr($date, 11, 8));
	}
	
	//날짜형식 변경(YYYYMMDD -> YYYY-MM-DD)
	function dateFormatFromGET($date){
		return substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
	}
	
	//UTF-8로 변환
	function toUTF8($str){
		return iconv("CP949", "UTF-8", $str);
	}
	
	//EUC-KR로 변환
	function toEUCKR($str){
		return iconv("UTF-8", "CP949", $str);
	}

	//XML파싱 데이터에서 값을 구한다.
	//$xml : XML값
	//$key : 구할 값의 키
	//$c : 같은값 구해도 계속 구할지 여부(true이면 계속)
	function findKey($xml, $key, $c = false){
		for($i=0;$i<count($xml);$i++){
			if(strtoupper($xml[$i]['tag']) == strtoupper($key)){
				$return[] = $xml[$i]['val'];
				if(!$c) return $return;
			}
		}
		return $return;
	}
	
	//지정된 상품이 플러스치즈 제외 상품인지 체크
	function except_goods($goodsno){
		return true; 
		/*
		//상품검색
		if($this->plusCheeseCfg['e_refer']){
			foreach($this->plusCheeseCfg['e_refer'] as $v){
				if($v == $goodsno){
					return true;
				}
			}
		}

		//상품이 등록된 모든 카테고리 검색
		global $db;
		$query = "SELECT category FROM ".GD_GOODS_LINK." WHERE goodsno=".$goodsno;
		$res = $db->query($query);
		while($data=$db->fetch($res)){
			$category[] = $data['category'];
		}
		if($this->plusCheeseCfg['category']){
			foreach($this->plusCheeseCfg['category'] as $v){
				foreach($category as $v2){
					$cate_len = strlen($v);
					if($v == substr($v2, 0, $cate_len)){
						return true;
					}
				}
			}
		}
		return false;
		*/
	}

	//주문상태 플러스치즈로 전송
	function sendStatus($ordno, $step, $cancel=0){
		return; 
		/*
		global $db;
		$url = "http://admin.pluscheese.com/api/receiveOrderCond.do";
		$query = "SELECT o.pCheeseOrdNo ordno, oi.pCheeseOrdNo iordno FROM ".GD_ORDER." o, ".GD_ORDER_ITEM." oi WHERE o.ordno= oi.ordno AND o.ordno='".$ordno."'";
		$res = $db->query($query);
		//
		//$step
		//0:주문접수 => 001
		//1:입금확인 => 002
		//2:배송준비 => 003
		//3:배송중   => 004
		//4:배송완료 => 006
		
		
		switch($step){
			case 0:
				$stepMsg = "001";
				break;
			case 1:
				$stepMsg = "002";
				break;
			case 2:
				$stepMsg = "003";
				break;
			case 3:
				$stepMsg = "004";
				break;
			case 4:
				$stepMsg = "006";
				break;
		}
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		while($data=$db->fetch($res)){
			$xml .= "<orderCondInfo>\n";
			$xml .= "	<tempOrderNo>".$data['ordno']."</tempOrderNo>\n";
			$xml .= "	<tempOrderSeq>".($data['iordno'])."</tempOrderSeq>\n";
			$xml .= "	<orderCancelCount>".$cancel."</orderCancelCount>\n";
			$xml .= "	<orderCondition>".$stepMsg."</orderCondition>\n";
			$xml .= "	<changeDate>".$this->dateFormatToXML(date("Y-m-d H:i:s"))."</changeDate>\n";
			$xml .= "</orderCondInfo>";
		}
		$xml = $this->encrypt($xml);
		$param['XMLData'] = $xml;
		$param['entID'] = $this->getStatusID();
		$result = $this->postRequest($url, $param);
		return $result;
		*/
	}
	
	//CPS주문 플러스치즈로 전송
	function sendCPSOrder($ordno, $orderItem, $delivery, $settleprice, $settlekind, $sellerID){
		return; 
		/*
		if($settlekind == "a"){ //무통장입금
			$settlekind = "C_TRANSFER";
		}else if($settlekind == "c"){ //신용카드
			$settlekind = "C_CARD";
		}else if($settlekind == "o"){ //실시간 계좌이체
			$settlekind = "R_TRANSFER";
		}else if($settlekind == "v"){ //가상계좌 결제
			$settlekind = "C_TRANSFER";
		}else if($settlekind == "d"){ //전액할인
			$settlekind = "C_TRANSFER";
		}else if($settlekind == "a"){ //휴대폰결제
			$settlekind = "PHONE";
		}else if($settlekind == "p"){ //포인트 결제
			$settlekind = "POINT";
		}else if($settlekind == "u"){ //중국 신용카드 결제
			$settlekind = "C_CARD";
		}
		
		$url = "http://admin.pluscheese.com/api/receiveCPSOrder.do";

		$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		$xml .= "<orderRequest>\n";
		$xml .= "	<contentsClass>COCL000001</contentsClass>\n";
		$xml .= "	<orderNo>".$ordno."</orderNo>\n";
		$xml .= "	<sellerID>".$sellerID."</sellerID>\n";
		$xml .= "	<entID>".$this->getStatusID()."</entID>\n";
		for($i=0;$i<count($orderItem['orderSeq']);$i++){
			$xml .= "	<orderInfo>\n";
			$xml .= "		<orderSeq>".$orderItem['orderSeq'][$i]."</orderSeq>\n";
			$xml .= "		<prdCode>".$orderItem['prdCode'][$i]."</prdCode>\n";
			$xml .= "		<prdName><![CDATA[".$this->toUTF8($orderItem['prdName'][$i])."]]></prdName>\n";
			$xml .= "		<optionTitle>".$this->toUTF8($orderItem['optionTitle'][$i])."</optionTitle>\n";
			$xml .= "		<prdPrice>".$orderItem['prdPrice'][$i]."</prdPrice>\n";
			$xml .= "		<orderCount>".$orderItem['orderCount'][$i]."</orderCount>\n";
			$xml .= "		<orderSum>".$orderItem['orderSum'][$i]."</orderSum>\n";
			$xml .= "	</orderInfo>\n";
		}
		$xml .= "	<deliveryCharge>".$delivery."</deliveryCharge>\n";
		$xml .= "	<paymentAmount>".$settleprice."</paymentAmount>\n";
		$xml .= "	<paymentMethod>".$settlekind."</paymentMethod>\n";
		$xml .= "	<partnerCommission>".$this->getStatusCom()."</partnerCommission>\n";
		$xml .= "	<orderDate>".date("Ymd_His")."</orderDate>\n";
		$xml .= "</orderRequest>";
		$xml = $this->encrypt($xml);
		$param['XMLData'] = $xml;
		$param['entID'] = $this->getStatusID();
		$result = $this->postRequest($url, $param);
		$result = $this->decrypt($result['content']);
		return $result;
		*/
	}

	function postRequest($url, $data, $referer='') {
		return; 
		/*
		// Convert the data array into URL Parameters like a=b&foo=bar etc.
		$data = http_build_query($data);

		// parse the given URL
		$url = parse_url($url);

		if ($url['scheme'] != 'http') { 
			die('Error: Only HTTP request are supported !');
		}

		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];

		// open a socket connection on port 80 - timeout: 30 sec
		$fp = fsockopen($host, 80, $errno, $errstr, 30);

		if ($fp){
			// send the request headers:
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");

			if ($referer != '') fputs($fp, "Referer: $referer\r\n");

			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);

			$result = ''; 
			while(!feof($fp)) {
				// receive the results of the request
				$result .= fgets($fp, 128);
			}
		}else { 
			return array(
				'status' => 'err', 
				'error' => "$errstr ($errno)"
			);
		}

		// close the socket connection:
		fclose($fp);

		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);

		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';

		// return as structured array:
		return array(
			'status' => 'ok',
			'header' => $header,
			'content' => $content
		);
		*/
	}
}
?>