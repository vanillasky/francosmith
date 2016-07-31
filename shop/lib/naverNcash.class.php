<?
class naverNcash{

	var $requestResult = '';	// 결과내용

	// 샌드박스 환경
	//var $service_url = 'sandbox-service.mileage.naver.com';
	//var $api_url = 'sandbox-api.mileage.naver.com';

	// 서비스 환경
	var $service_url = "service.mileage.naver.com";
	var $api_url = "api.mileage.naver.com";

	function naverNcash($isAdmin = false){

		@include_once "./config.class.php";
		$config = Core::loader('config');
		$load_config_ncash = $config->load('ncash');

		$this->useyn = $load_config_ncash['useyn'];
		$this->api_id = $load_config_ncash['api_id'];
		$this->api_key = $load_config_ncash['api_key'];
		$this->e_exceptions = unserialize($load_config_ncash['e_exceptions']);
		$this->e_category = unserialize($load_config_ncash['e_category']);
		$this->baseAccumRate = $load_config_ncash['baseAccumRate'];
		$this->status = $load_config_ncash['status'];
		$this->exceptionyn = $load_config_ncash['exceptionyn'];
		$this->save_mode = $load_config_ncash['save_mode'];
		$this->mobileStatus = $load_config_ncash['mobileStatus'];
		$this->testerIPList = array('111.91.139.55','58.72.239.56','61.36.175.','103.243.200.','211.233.50.','211.233.51.','115.88.179.','111.91.140.58','182.162.206.167');
		if($_COOKIE['NA_MI'])
		{
			$this->inflowParam = array();
			foreach(explode('|', urldecode(base64_decode($_COOKIE['NA_MI']))) as $set)
			{
				$set = explode('=', $set);
				$this->inflowParam[$set[0]] = $set[1];
			}
		}
		else if($_COOKIE['Ncisy'])
		{
			$this->inflowParam = array();
			$this->inflowParam['ba'] = $_COOKIE['N_ba'];
			$this->inflowParam['aa'] = $_COOKIE['N_aa'];
		}

		if($this->api_id == '' || $this->api_key == '') $this->useyn = "N";

		include_once(SHOPROOT.'/lib/httpSock.class.php');
		include_once(SHOPROOT.'/lib/parsexml.class.php');

		if ($this->useyn === 'Y' && $isAdmin === false && $this->is_inflow() === false) {
			$this->useyn = 'N';
		}

		$this->getAccumRate();
	}

	function hash_hmac_php4($algo,$data,$passwd){
		/* php4 용 md5 and sha1 only */
		$algo=strtolower($algo);
		$p=array('md5'=>'H32','sha1'=>'H40');
		if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
		if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));
		$ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
		$opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);
		return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
	}

	function init($url,$field,$host){
		$this->ch = curl_init();

		curl_setopt($this->ch,CURLOPT_URL,$url);
		curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST,$host);
		curl_setopt($this->ch,CURLOPT_POST, 0);
		curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, 1);

		//set the XML body of the request
		if($host){
			curl_setopt($this->ch,CURLOPT_POSTFIELDS, $field);
		}

		//Send the Request
		$response = curl_exec($this->ch);

		//close the connection
		curl_close($this->ch);

		//return the response
		return $response;
	}

	function realyn(){
		$ret = 0;

		if($this->status == 'real') $ret = 1;
		foreach($this->testerIPList as $v){
			$v = trim($v);
			if($v&&preg_match('/'.$v.'/',$_SERVER['REMOTE_ADDR']))$ret = 1;
		}

		return $ret;
	}

	### 주문서 - 사용/적립버튼
	/**
	*	네이버 팝업창 속성
	*
	*	URL
	*	- 테스트 : https://sandbox-service.mileage.naver.com/service/accumulation/{API_ID}
	*	- 서비스 : https://service.mileage.naver.com/service/accumulation/{API_ID}
	*
	*	windowName : 가맹점 고유의 이름을 정해 사용해야 한다. 이는 네이버마일리지를 적용한 가맹점 사이의 혼란을 막고, 여러 거래에서 동시에 네이버마일리지를 사용하는 것을 막기 위한 것이다.
	*
	*	option: width=400,height=679,status=no,resizeable=no
	**/

	function cash_save_use($reqTxId='', $maxUseAmount){

		$https = ($_SERVER['HTTPS'] == 'on') ? "https" : "http";

		$doneUrl = urlencode($https."://".$_SERVER['HTTP_HOST']."/shop/proc/naverNcash_bridge.php");
		$timestamp = time();

		$data = $timestamp.$doneUrl;

		$signature = $this->hash_hmac_php4('sha1',$data,$this->api_key);

		$cash_url = "https://".$this->service_url."/service/v2/accumulation/".$this->api_id."?doneUrl=".$doneUrl."&maxUseAmount=".$maxUseAmount."&Ncisy=\"+mileageInfo+\"&reqTxId=".$reqTxId."&sig=".$signature."&timestamp=".$timestamp;
		return $cash_url;
	}

	### 네이버 마일리지 예외상품, 예외카테고리 체크
	function exception_goods($item){
		global $db;

		$exceptionYN = "Y";
		$a_exception = array();

		foreach( $item as $v ){

			$exception_goods = $exception_category = "N";

			// 예외상품 체크
			if(@in_array($v['goodsno'],$this->e_exceptions))	$exception_goods = "Y";

			// 예외카테고리 체크
			$res = $db->query("select category from `gd_goods_link` where `goodsno` = ".$v['goodsno']);
			while ($data=$db->fetch($res)){
				if(@in_array($data['category'],$this->e_category))	$exception_category = "Y";
			}

			if(strcmp($exception_goods,$exception_category) == '0'){
				$a_exception[] = $exception_goods;
			}else{
				$a_exception[] = "Y";
			}
		}

		// 예외상품이 아닌 상품이 하나라도 있다면 네이버 마일리지 적립
		if(in_array("N",$a_exception)){
			$exceptionYN = "N";
		}

		return $exceptionYN;
	}

	### API 호출 공통 체크 함수 ( 결제승인 API 제외 )
	function common_check($ordno){

		global $db;

		if(!$ordno) return;

		$use_yn = "";

		$use_yn = $db->fetch("select ncash_tx_id, oldordno, ncash_save_yn from " .GD_ORDER. " where ordno ='".$ordno."'");
		// 트랜잭션 아이디가 있으며 , 재주문이 아니고 ncash 적립 및 사용을 선택했을 때만.
		if($use_yn['ncash_tx_id'] != "" && $use_yn['oldordno'] == "" && in_array($use_yn['ncash_save_yn'],array('y','b'))){
			return true;
		}
		return false;
	}

	// 유입확인
	function is_inflow()
	{
		return $this->inflowParam ? true : false;
	}

	// 기본적립률 반환
	function get_base_accum_rate()
	{
		return $this->is_inflow() ? $this->inflowParam['ba'] : $this->baseAccumRate;
	}

	// 추가적립률 반환
	function get_add_accum_rate()
	{
		return $this->is_inflow() ? $this->inflowParam['aa'] : 0;
	}

	### 결제 승인 API
	function payment_approval($ordno, $isPaid){

		if(!$ordno) exit;

		global $sess, $db;

		$use_yn = "";

		$paymentInfo = Core::loader('NaverMileageTransaction', $ordno)->toLegacyArray();

		$orderData = $db->fetch("select oldordno, m_no from " .GD_ORDER. " where ordno ='".$ordno."'");

		// 재주문이거나 , ncash 사용 여부를 안함으로 선택했을때는 중지
		if( $orderData['oldordno'] || !in_array($paymentInfo['save_mode'],array('ncash','both'))) return;

		$i = $orderAmount = 0;
		$item = $result = array();
		$ncash_post['items'] = $ncash_post['orderProductName'] = $logMsg = "";
		$ncash_save_yn = "y";
		if($paymentInfo['save_mode'] == 'both') $ncash_save_yn = "b";
		$settleprice = $paymentInfo['goodsPrice'];

		foreach($paymentInfo['item'] as $v){

			$goods_qty += $v['ea'];

			$category = $this->category_name($v['goodsno']);

			$exception_goods = $exception_category = "N";

			// 예외상품 가격 제외
			if(@in_array($v['goodsno'],$this->e_exceptions))	$exception_goods = "Y";

			// 예외카테고리 가격 제외
			$res = $db->query("select category from `gd_goods_link` where `goodsno` = ".$v['goodsno']);
			while ($data=$db->fetch($res)){
				if(@in_array($data['category'],$this->e_category))	$exception_category = "Y";
			}

			// 둘 중에 하나만 Y 이여도 가격 제외
			if(!($exception_goods == 'N' && $exception_category == 'N')){
				$settleprice -= $v['price'] * $v['ea'];
			}else{
				$i++;
				if($i == 1) $ncash_post['orderProductName'] = addslashes($v['goodsnm']);
			}

			$item[]= $v['goodsno'].",".base64_encode(iconv('EUC-KR', 'UTF-8', strip_tags($v['goodsnm']))).",".$v['price'].",".$v['ea'].",".($v['price']*$v['ea']).",".base64_encode(iconv('EUC-KR', 'UTF-8', strip_tags($category)));
		}

		// 예외상품, 예외카테고리 가격을 제외한 나머지 금액에서 기타 할인 금액도 함께 제외
		// 여기서의 settleprice는 적립대상금액으로 네이버 마일리지 사용금액은 제외하나 네이버 캐쉬 사용금액은 제외하지 않는다
		$settleprice -= $paymentInfo['discountAmount'] + $paymentInfo['mileageUseAmount'.$this->api_id];
		// 가맹점 적립시 적립대상금액 0원으로 변경
		if(!in_array($paymentInfo['save_mode'],array('ncash','both'))){$settleprice = 0; $ncash_save_yn = "n";}

		$this->order_tx_id = $paymentInfo['reqTxId'.$this->api_id];

		$ncash_post['orderProductName'] = mb_substr(strip_tags($ncash_post['orderProductName']),0,66,'EUC-KR');

		$ncash_post['orderProductName'] = iconv_recursive('euc-kr','utf-8',$ncash_post['orderProductName']);

		$ncash_post['orderProductName'] = base64_encode($ncash_post['orderProductName']);

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['reqTxId'] = $paymentInfo['reqTxId'.$this->api_id];		// 트랜젝션 아이디
		$ncash_post['orderNo'] = $paymentInfo['ordno'];		// 주문번호
		$ncash_post['orderAmount'] = $paymentInfo['orderAmount'];	// 거래의 총 주문금액
		$ncash_post['qty'] = $goods_qty;			// 주문 상품 수량
		$ncash_post['amount'] = ($settleprice < 0 ) ? "0" : $settleprice;		// 네이버마일리지 적립 대상 금액
		$ncash_post['mileageUseAmount'] = $paymentInfo['mileageUseAmount'.$this->api_id];	// 네이버 마일리지 사용 액수
		$ncash_post['cashUseAmount'] = $paymentInfo['cashUseAmount'.$this->api_id];	// 네이버 캐쉬 사용 액수
		$ncash_post['isMember'] = ($orderData['m_no'] > 0) ? "Y" : "N";	// 가맹점 회원여부 ( 회원 Y , 비회원 N )
		$ncash_post['isConfirmed'] = $isPaid?"Y":"N";	// 거래 확정 여부 ( 무통장(가상계좌) N , 그 외 Y )
		$ncash_post['items'] = @implode("|",$item);	// 거래 상품 정보 ex) 상품코드,상품이름,상품단가,구매수량,상품총가격,카테고리|상품코드,상품이름,상품단가,구매수량,상품총가격,카테고리|...
		$ncash_post['items'] = iconv_recursive('euc-kr','utf-8',$ncash_post['items']);
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		## 로그데이터
		foreach($ncash_post as $k => $v){
			$logMsg .= $k . " = " . $v . chr(10);
		}

		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/payment
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/payment
		**/

		$this->ncash_log('send_payment_approval',$logMsg);

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/payment",http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);

		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('result_payment_approval',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 적립 및 사용 결과 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		if( in_array($result['RESULTCODE'],array('1000','1001','1002')) ){ // DB처리
			$db->query("update ".GD_ORDER." set `ncash_emoney` = ". $ncash_post['mileageUseAmount'] ." , `ncash_cash` = ". $ncash_post['cashUseAmount'] ." , `ncash_save_yn` = '" . $ncash_save_yn . "' , `ncash_tx_id` = '" . $result['TXID'] . "' , `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
			return true;
		}
		else if ($result['RESULTCODE'] === '3092') {
			$db->query('UPDATE '.GD_ORDER.' SET `settlelog` = CONCAT(IFNULL(settlelog, ""), "'.$settlelog.'") WHERE `ordno`='.$ordno);
			return true;
		}
		else {
			$order = $db->fetch('SELECT `step` FROM '.GD_ORDER.' WHERE `ordno`='.$ordno);
			if ($order['step'] == '0') {
				$db->query('UPDATE '.GD_ORDER.' SET `settlelog` = CONCAT(IFNULL(`settlelog`, ""), "'.$settlelog.'"), `step2`=54 WHERE `ordno`='.$ordno);
				$db->query('UPDATE '.GD_ORDER_ITEM.' SET `istep`=54 WHERE `ordno`='.$ordno);
			}
			else {
				$db->query('UPDATE '.GD_ORDER.' SET `settlelog` = CONCAT(IFNULL(`settlelog`, ""), "'.$settlelog.'") WHERE `ordno`='.$ordno);
			}
			return false;
		}
	}

	### 결제 승인 취소 API
	function payment_approval_cancel($ordno,$TxId=null){
		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/abort
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/abort
		**/

		global $db;

		if(!$this->common_check($ordno)) return;

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		$paymentInfo = Core::loader('NaverMileageTransaction', $ordno)->toLegacyArray();

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/payment/".$paymentInfo['reqTxId'.$this->api_id]."/abort",http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('payment_approval_cancel',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 결제 승인 취소 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		$db->query("update ".GD_ORDER." set `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
	}

	### 거래 취소 API
	function deal_cancel($ordno, $cancel_sno){
		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/cancel
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/cancel
		**/

		global $db;

		if(!$this->common_check($ordno)) return;

		$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno = '".$ordno."' and istep < '44'");

		//부분취소일때는 재승인 API 호출
		if(mysql_num_rows($res) > 0 ){
			$this->deal_reapproval($ordno, $cancel_sno);
			return;
		}

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		//결제시 트랜잭션 아이디
		$reqTxId = $db->fetch("select ncash_tx_id from ".GD_ORDER." where ordno ='".$ordno."'");

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/payment/".$reqTxId['ncash_tx_id']."/cancel",http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('deal_cancel',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 거래 취소 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		$db->query("update ".GD_ORDER." set `ncash_save_yn` = 'n', `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");

	}

	/*
	 * 거래 재승인 API
	 * @param       : Int ordno, Int cancel_sno
	 * @return      : void
	 * @description : 기 사용된 마일리지, 캐쉬 및 적립대상금액을 수정
	 *  cancel_sno가 있으면 해당 취소건의 취소된 상품금액만큼 마일리지, 캐쉬를 차감하고 적립대상금액을 갱신.
	 *  cancel_sno가 없으면 적립대상금액만 갱신
	 */
	function deal_reapproval($ordno, $cancel_sno=null){

		global $db;

		if(!$this->common_check($ordno)) return;

		$i = $settleprice = $order_price = $goods_qty = $dcprice = 0;
		$category = "";
		$data = array();

		$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno = '".$ordno."' and istep < '44'");

		//전부다 취소일때는 거래취소 API 호출
		if(mysql_num_rows($res) == '0'){
			$this->deal_cancel($ordno, $cancel_sno);
			return;
		}

		while($data=$db->fetch($res)){

			$order_price += $data['price'] * $data['ea'];	//주문 총 금액

			$goods_qty += $data['ea'];

			$dcprice += $data['memberdc'];

			$category = $this->category_name($data['goodsno']);

			$exception_goods = $exception_category = "N";

			// 예외상품 가격 제외
			if(@in_array($data['goodsno'],$this->e_exceptions))	$exception_goods = "Y";

			// 예외카테고리 가격 제외
			$category_res = $db->query("select category from `gd_goods_link` where `goodsno` = ".$data['goodsno']);
			while ($category_data=$db->fetch($category_res)){
				if(@in_array($category_data['category'],$this->e_category))	$exception_category = "Y";
			}

			// 적립예외대상이 아닌경우 적립대상금액에 상품금액 추가
			if($exception_goods == 'N' && $exception_category == 'N'){
				$settleprice += $data['price'] * $data['ea'];
			}
			$i++;
			if($i == 1) $ncash_post['orderProductName'] = addslashes($data['goodsnm']);

			$item[]= $data['goodsno'].",".base64_encode(iconv('EUC-KR', 'UTF-8', strip_tags($data['goodsnm']))).",".$data['price'].",".$data['ea'].",".($data['price']*$data['ea']).",".base64_encode(iconv('EUC-KR', 'UTF-8', strip_tags($category)));
		}

		$order_data = $db->fetch("select coupon, memberdc, emoney, ncash_emoney, ncash_cash, ncash_save_yn, ncash_tx_id, m_no, settlekind, delivery, cyn from ".GD_ORDER." where ordno ='".$ordno."'");

		// 취소로 인하여 재승인하는경우가 아닐때
		if($cancel_sno===null)
		{
			$mileage_use_amount = $order_data['ncash_emoney'];
			$cash_use_amount = $order_data['ncash_cash'];
		}
		// 취소로 인하여 재승인할때
		else
		{
			// 취소처리가 완료되지 않은 건들의 마일리지, 캐쉬의 합계와 현재 취소건의 마일리지, 캐쉬 조회
			list($total_rncash_emoney, $total_rncash_cash, $rncash_emoney, $rncash_cash) = $db->fetch("
			SELECT SUM(`oc`.`rncash_emoney`), SUM(`oc`.`rncash_cash`), SUM(IF(`oc`.`sno`=".$cancel_sno.", `oc`.`rncash_emoney`, 0)), SUM(IF(`oc`.`sno`=".$cancel_sno.", `oc`.`rncash_cash`, 0))
			FROM `gd_order_item` AS `oi`
			INNER JOIN `gd_order_cancel` AS `oc`
			ON `oi`.`cancel`=`oc`.`sno`
			WHERE `oi`.`ordno`=".$ordno."
			AND `oi`.`cyn`='y'
			AND `oi`.`istep`<44
			");

			// 총 사용 마일리지 계산
			$total_mileage_use_amount = $order_data['ncash_emoney'] + $total_rncash_emoney;
			$total_cash_use_amount = $order_data['ncash_cash'] + $total_rncash_cash;

			// 재승인 요청할 마일리지, 캐쉬 금액 산출
			$mileage_use_amount = $total_mileage_use_amount - $rncash_emoney;
			$cash_use_amount = $total_cash_use_amount - $rncash_cash;
		}

		// 예외상품, 예외카테고리 가격을 제외한 나머지 금액에서 기타 할인 금액도 함께 제외
		// 여기서의 settleprice는 적립대상금액으로 네이버 마일리지 사용금액은 제외하나 네이버 캐쉬 사용금액은 제외하지 않는다
		if($order_data['ncash_emoney'] > $order_price ) $order_data['ncash_emoney'] = 0;
		$settleprice -= $order_data['coupon'] + $order_data['emoney'] + $mileage_use_amount + $dcprice;

		$ncash_post['orderProductName'] = mb_substr(strip_tags($ncash_post['orderProductName']),0,66,'EUC-KR');

		$ncash_post['orderProductName'] = iconv_recursive('euc-kr','utf-8',$ncash_post['orderProductName']);

		$ncash_post['orderProductName'] = base64_encode($ncash_post['orderProductName']);

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['orderNo'] = $ordno;		// 주문번호
		$ncash_post['orderAmount'] = $order_price+$order_data['delivery'];	// 거래의 총 주문금액
		$ncash_post['qty'] = $goods_qty;			// 주문 상품 수량
		$ncash_post['amount'] = ($settleprice < 0 ) ? "0" : $settleprice;		// 네이버 마일리지 적립 대상 금액
		$ncash_post['mileageUseAmount'] = $mileage_use_amount;	// 네이버 마일리지 사용 액수
		$ncash_post['cashUseAmount'] = $cash_use_amount;	// 네이버 캐쉬 사용 액수
		$ncash_post['isMember'] = ($order_data['m_no'] != '0') ? "Y" : "N";	// 가맹점 회원여부 ( 회원 Y , 비회원 N )
		$ncash_post['isConfirmed'] = (($order_data['settlekind'] == 'a'||$order_data['settlekind'] == 'v') && $order_data['cyn'] == 'n') ? "N" : "Y" ;	// 거래 확정 여부 ( 무통장 N , 그 외 Y )
		$ncash_post['items'] = @implode("|",$item);	// 거래 상품 정보 ex) 상품코드,상품이름,상품단가,구매수량,상품총가격,카테고리|상품코드,상품이름,상품단가,구매수량,상품총가격,카테고리|...
		$ncash_post['items'] = iconv_recursive('euc-kr','utf-8',$ncash_post['items']);
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		## 로그데이터
		foreach($ncash_post as $k => $v){
			$logMsg .= $k . " = " . $v . chr(10);
		}

		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/repayment/{reqTxId}
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/repayment/{reqTxId}
		**/

		$this->ncash_log('send_deal_reapproval',$logMsg);

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/repayment/".$order_data['ncash_tx_id'],http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('result_deal_reapproval',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 재승인 결과 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		if( in_array($result['RESULTCODE'],array('1000','1001','1002')) ){ // DB처리
			$db->query("update ".GD_ORDER." set `ncash_save_yn` = '".$order_data['ncash_save_yn']."' , `ncash_tx_id` = '" . $result['TXID'] . "' , `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
		}else{	// 성공이 아닌 이외의 결과에 log처리
			$db->query("update ".GD_ORDER." set `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
		}
	}

	### 거래 확정 API
	function deal_done($ordno){
		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/stated
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/stated
		**/

		global $db;

		if(!$this->common_check($ordno)) return;

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		//결제시 트랜잭션 아이디
		$reqTxId = $db->fetch("select ncash_tx_id from ".GD_ORDER." where ordno ='".$ordno."'");

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/payment/".$reqTxId['ncash_tx_id']."/stated",http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('deal_done',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 거래 확정 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		$db->query("update ".GD_ORDER." set `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
	}

	### 거래 확정 취소 API
	function deal_done_cancel($ordno){
		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/cancel/stated
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/cancel/stated
		**/


		global $db;

		if(!$this->common_check($ordno)) return;

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		//결제시 트랜잭션 아이디
		$reqTxId = $db->fetch("select ncash_tx_id from ".GD_ORDER." where ordno ='".$ordno."'");

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/payment/".$reqTxId['ncash_tx_id']."/cancel/stated",http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('deal_done_cancel',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 거래 확정 취소 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		$db->query("update ".GD_ORDER." set `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");

	}

	### 구매 확정 API
	function buy_done($ordno){
		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/confirmed
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/payment/{reqTxId}/confirmed
		**/

		global $db;

		if(!$this->common_check($ordno)) return;

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['secret'] = (string)$this->api_key;		// API 인증키

		//결제시 트랜잭션 아이디
		$reqTxId = $db->fetch("select ncash_tx_id from ".GD_ORDER." where ordno ='".$ordno."'");

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/payment/".$reqTxId['ncash_tx_id']."/confirmed",http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('buy_done',$logMsg);

		$settlelog = chr(10) . "네이버 마일리지 구매 확정 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		$db->query("update ".GD_ORDER." set `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
	}

	// 송장번호 등록
	function delivery_invoice($ordno, $deliveryNo=null, $invoiceNo=null)
	{
		/**
		*	[API URL]
		*	테스트 : https://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/delivery/{reqTxId}/invoice
		*	서비스 : https://api.mileage.naver.com/v2/partner/{API_ID}/delivery/{reqTxId}/invoice
		**/

		global $db;

		if(!$this->common_check($ordno)) return;

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태
		$ncash_post['secret'] = (string)$this->api_key;	// API 인증키

		//결제 및 배송정보
		$preOrder = $db->fetch("
		SELECT `o`.`ncash_tx_id`, `o`.`ddt` AS `order_delivery_date`, `o`.`nameReceiver`, `o`.`phoneReceiver`, `o`.`mobileReceiver`, `o`.`address`,
		`o`.`dyn` AS `order_delivery_yn`, `o`.`deliverycode` AS `order_delivery_code`, `deliveryno` AS `order_delivery_no`,
		`oi`.`dyn` AS `item_delivery_yn`, `oi`.`dvcode` AS `item_delivery_code`, `oi`.`dvno` AS `item_delivery_no`
		FROM `".GD_ORDER."` AS `o`
		INNER JOIN `".GD_ORDER_ITEM."` AS `oi`
		ON `o`.`ordno`=`oi`.`ordno`
		WHERE `o`.`ordno`=".$ordno."
		/* 네이버쪽에서는 주문건별로 송장번호를 받기때문에 이나무쪽에서 상품별로 송장을 입력해도 하나만 전송되도록 LIMIT처리(네이버와 협의) */
		LIMIT 1
		");

		// 송장정보가 별도로 지정되지 않은경우 DB정보를 사용
		if(!$deliveryNo || !$invoiceNo)
		{
			// 주문별송장입력
			if((int)$set['delivery']['basis']===0)
			{
				$deliveryNo = trim($preOrder['order_delivery_no']);
				$invoiceNo = trim($preOrder['order_delivery_code']);
			}
			// 상품별송장입력
			else
			{
				$deliveryNo = trim($preOrder['item_delivery_no']);
				$invoiceNo = trim($preOrder['item_delivery_code']);
			}
		}

		list($serviceCompany) = $db->fetch("SELECT `deliverycomp` FROM `".GD_LIST_DELIVERY."` WHERE `deliveryno`=".$deliveryNo);

		if((int)$invoiceNo<1) return;

		$ncash_post['shippedDate'] = preg_replace('/^(\d{4})-(\d{2})-(\d{2}).*$/', '${1}${2}${3}', $preOrder['order_delivery_date']);	// 발송완료 일시

		// 수취자 명
		$preOrder['nameReceiver'] = trim($preOrder['nameReceiver']);
		if(mb_strlen($preOrder['nameReceiver'], 'EUC-KR')<2) $ncash_post['recipientName'] = '*';
		else $ncash_post['recipientName'] = mb_substr($preOrder['nameReceiver'], 0, 1, 'EUC-KR').'*'.mb_substr($preOrder['nameReceiver'], 2, mb_strlen($preOrder['nameReceiver'], 'EUC-KR'), 'EUC-KR');

		// 수취자 유선전화
		$recipientTelNo = array();
		foreach(explode('-', $preOrder['phoneReceiver']) as $value)
		{
			$value = trim($value);
			if(strlen($value)>0 && preg_match('/^\d+$/', $value)) $recipientTelNo[] = $value;
		}
		if(count($recipientTelNo)>1)
		{
			$recipientTelNo[1] = str_repeat('*', strlen($recipientTelNo[1]));
			$ncash_post['recipientTelNo'] = implode('-', $recipientTelNo);
		}
		unset($recipientTelNo);

		// 수취자 휴대폰 연락처
		$recipientCphNo = array();
		foreach(explode('-', $preOrder['mobileReceiver']) as $value)
		{
			$value = trim($value);
			if(strlen($value)>0 && preg_match('/^\d+$/', $value)) $recipientCphNo[] = $value;
		}
		if(count($recipientCphNo)>1)
		{
			$recipientCphNo[1] = str_repeat('*', strlen($recipientCphNo[1]));
			$ncash_post['recipientCphNo'] = implode('-', $recipientCphNo);
		}
		unset($recipientCphNo);

		$address = array();
		foreach(preg_split('/\s+/', $preOrder['address']) as $part)
		{
			$address[] = $part;
			if(preg_match('/(동|읍|면|리)$/', $part)) break;
		}
		$ncash_post['address'] = strip_tags(implode(' ', $address));	// 배송지 주소

		$ncash_post['serviceCompany'] = strip_tags($serviceCompany);	// 택배사명
		$ncash_post['invoiceNo'] = $invoiceNo;	// 송장번호

		$ncash_post = iconv_recursive('EUC-KR', 'UTF-8', $ncash_post);

		$ncash_post['recipientName'] = base64_encode($ncash_post['recipientName']);
		$ncash_post['address'] = base64_encode($ncash_post['address']);
		$ncash_post['serviceCompany'] = base64_encode($ncash_post['serviceCompany']);

		$res = $this->init("https://".$this->api_url."/v2/partner/".$this->api_id."/delivery/".$preOrder['ncash_tx_id']."/invoice", http_build_query($ncash_post),1);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);

		$result = array();
		$result['택배사'] = $serviceCompany;
		$result['송장번호'] = $invoiceNo;
		$result['수취인'] = iconv('UTF-8', 'EUC-KR', base64_decode($ncash_post['recipientName']));
		$result['배송지'] = iconv('UTF-8', 'EUC-KR', base64_decode($ncash_post['address']));
		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$this->ncash_log('buy_done',$logMsg);

		$settlelog = chr(10) . "네이버 송장번호 등록 (".date('Y:m:d H:i:s').")". chr(10). "----------------------------------------". chr(10) . $logMsg . "----------------------------------------" . chr(10);

		$db->query("update ".GD_ORDER." set `settlelog` = concat(ifnull(settlelog,''),'$settlelog') where ordno = '". $ordno ."';");
	}

	### 기본 적립률 조회 API
	function getAccumRate(){
		/**
		*	[API URL]
		*	테스트 : http://sandbox-api.mileage.naver.com/v2/partner/{API_ID}/getAccumRate
		*	서비스 : http://api.mileage.naver.com/v2/partner/{API_ID}/getAccumRate
		**/
		global $config;
		if (is_object($config) == false) {
			$config = Core::loader('config');
		}

		$ncash_post = array();

		$ncash_post['format'] = "xml";	// 응답형태

		$request = iconv_recursive('euc-kr','utf-8',$ncash_post);

		if(!$this->api_id) return;	// ID가 있을때만 호출.


		$res = $this->init("http://".$this->api_url."/v2/partner/".$this->api_id."/getAccumRate?format=xml",$request,0);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $res, $vals, $index);
		xml_parser_free($parser);

		$vals = iconv_recursive('utf-8','euc-kr',$vals);


		foreach($vals as $result_k => $result_v ){
			$result[$result_v['tag']] = $result_v['value'];
		}

		if($result['RESULTCODE']=='1000'){
			$config_ncash = array(
				'baseAccumRate'=>(string)$result['BASEACCUMRATE'],
				'RateDate'=>date('Y-m-d H:i:s'),
			);
			$config->save('ncash',$config_ncash);
		}else{
			$config_ncash = array(
				'baseAccumRate'=>(string)'0',
				'RateDate'=>date('Y-m-d H:i:s'),
			);
			$config->save('ncash',$config_ncash);
		}

		$logMsg = "";
		foreach($result as $log_k => $log_v){
			$logMsg .= $log_k . " = " . $log_v . chr(10);
		}
		$logInfo  = 'INFO ['.date('Y-m-d H:i:s').'] START getAccumRate log'.chr(10);
		$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Connect IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
		$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Request URL : '.$_SERVER['REQUEST_URI'].chr(10);
		$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] User Agent : '.$_SERVER['HTTP_USER_AGENT'].chr(10);
		$logInfo .= $logMsg;
		$logInfo .= 'INFO ['.date('Y-m-d H:i:s').'] END getAccumRate log'.chr(10);
		$logInfo .= '------------------------------------------------------------------------------'.chr(10).chr(10);

		//error_log($logInfo, 3, dirname(__FILE__).'/../log/naverNcash/ncash_getAccumRate_log.log');

	}

	### 주문 상품 카테고리
	function category_name($goodsno){
		global $db;

		$goods_category = $db->fetch("select ".getCategoryLinkQuery('category', null, 'max')." from `gd_goods_link` where `goodsno` = $goodsno order by sno limit 1");

		$query = "
		select * from
			".GD_CATEGORY."
		where
			category in (left('$goods_category[category]',3),left('$goods_category[category]',6),left('$goods_category[category]',9),'$goods_category[category]')
		order by category
		";
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $pos[] = "$data[catnm]";
		$category = @implode(">",$pos);
		return $category;
	}

	### Log
	function ncash_log($api_name,$logMsg){	// api명 , 기록데이터

		$logInfo  = 'INFO ['.date('Y-m-d H:i:s').'] START '.$api_name.' log'.chr(10);
		$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Connect IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
		$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Request URL : '.$_SERVER['REQUEST_URI'].chr(10);
		$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] User Agent : '.$_SERVER['HTTP_USER_AGENT'].chr(10);
		$logInfo .= $logMsg;
		$logInfo .= 'INFO ['.date('Y-m-d H:i:s').'] END '.$api_name.' log'.chr(10);
		$logInfo .= '------------------------------------------------------------------------------'.chr(10).chr(10);

		error_log($logInfo, 3, dirname(__FILE__).'/../log/naverNcash/ncash_log_'.date('Ymd').'.log');
	}

	function _httpRequest($url,$request) {
		$this->error='';

		$request = iconv_recursive('euc-kr','utf-8',$request);

		$httpSock = new httpSock($url,'POST',$request);
		$httpSock->send();

		$this->requestResult = iconv_recursive('utf-8','euc-kr',$httpSock->resContent);
		return $this->requestResult;
	}

	### 적립 및 사용 제외 금액 계산
	function exception_price($item){
		global $db;

		$exception_price = "";
		foreach($item as $v){
			$exception_goods = $exception_category = "N";

			// 예외상품 가격 제외
			if(@in_array($v['goodsno'],$this->e_exceptions))	$exception_goods = "Y";

			// 예외카테고리 가격 제외
			$res = $db->query("select category from `gd_goods_link` where `goodsno` = ".$v['goodsno']);
			while ($data=$db->fetch($res)){
				if(@in_array($data['category'],$this->e_category))	$exception_category = "Y";
			}

			// 둘 중에 하나만 Y 이여도 가격 제외
			if(!($exception_goods == 'N' && $exception_category == 'N')){
				$exception_price += $v['price'];
			}
		}
		return $exception_price;
	}

	function canUseMobile()
	{
		if ($this->mobileStatus == 'real') $canUse = true;
		else $canUse = false;

		if ($canUse === false) {
			foreach ($this->testerIPList as $ip) {
				$ip = trim($ip);
				if ($ip && preg_match('/'.$ip.'/',$_SERVER['REMOTE_ADDR'])) {
					$canUse = true;
					break;
				}
			}
		}

		return $canUse;
	}

	function getMobileBaseScript($isSandbox = false)
	{
		if ($this->useyn == 'Y') {
			if ($isSandbox) {
				if (isset($_SERVER['HTTPS'])) {
					$javascriptSource = 'https://sandbox-service.mileage.naver.com/ext/m/v1/mileage.min.euckr.js';
					$styleSheetSource = 'https://sandbox-service.mileage.naver.com/ext/m/v1/css/w.css';
				}
				else {
					$javascriptSource = 'http://sandbox-static.mileage.naver.net/static/ext/m/v1/mileage.min.euckr.js';
					$styleSheetSource = 'http://sandbox-static.mileage.naver.net/static/ext/m/v1/css/w.css';
				}
			}
			else {
				if (isset($_SERVER['HTTPS'])) {
					$javascriptSource = 'https://ssl.pstatic.net/static.mileage/static/ext/m/v1/mileage.min.euckr.js';
					$styleSheetSource = 'https://ssl.pstatic.net/static.mileage/static/ext/m/v1/css/w.css';
				}
				else {
					$javascriptSource = 'http://static.mileage.naver.net/static/ext/m/v1/mileage.min.euckr.js';
					$styleSheetSource = 'http://static.mileage.naver.net/static/ext/m/v1/css/w.css';
				}
			}
			$mobileBaseScript = '<script type="text/javascript" src="'.$javascriptSource.'"></script>';
			$mobileBaseScript.= '<link rel="stylesheet" type="text/css" href="'.$styleSheetSource.'"/>';
			return $mobileBaseScript;
		}
		else {
			return false;
		}
	}

	function getMobileScript($isSandbox = false)
	{
		if ($this->useyn == 'Y') {
			include dirname(__FILE__).'/../conf/config.php';
			include dirname(__FILE__).'/../conf/config.mobileShop.php';

			$https = ($_SERVER['HTTPS'] == 'on') ? "https" : "http";
			$doneUrl = $https.'://'.$_SERVER['HTTP_HOST'].$cfgMobileShop['mobileShopRootDir'].'/proc/naver_mileage_bridge.php';
			$timestamp = time();
			$signature = $this->hash_hmac_php4('sha1', $timestamp.urlencode($doneUrl), $this->api_key);
			$requestStruct = explode('/', preg_replace('/^'.str_replace('/', '\/', $cfgMobileShop['mobileShopRootDir']).'\//', '', $_SERVER['SCRIPT_NAME']));

			$param = array();
			$param[] = 'Controller='.$requestStruct[0];
			$param[] = 'Action='.$requestStruct[1];
			$param[] = 'ElId=_mileage_acc';
			$param[] = 'ApiId='.$this->api_id;
			$param[] = 'DoneUrl='.$doneUrl;
			$param[] = 'Signature='.$signature;
			$param[] = 'Timestamp='.$timestamp;
			$param[] = 'BaseAccumRate='.$this->get_base_accum_rate();
			$param[] = 'SaveMode='.$this->save_mode;

			$mobileScript  = $this->getMobileBaseScript($isSandbox);
			$mobileScript .= '<script type="text/javascript" src="'.$cfg['rootDir'].'/lib/js/naverMileage.js?'.implode('&amp;', $param).'" id="naver-mileage-script"></script>';
			return $mobileScript;
		}
		else
		{
			return false;
		}
	}
}
?>
