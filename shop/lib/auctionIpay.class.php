<?php
class auctionSession
{
	var $serverUrl;
	var $soapAuction;

	function auctionSession($serverUrl, $soapAuction)
	{
		$this->serverUrl = $serverUrl;
		$this->soapAuction = $soapAuction;
	}

	/**	sendHttpRequest
		Sends a HTTP request to the server for this session
		Input:	$requestBody
		Output:	The HTTP Response as a String
	*/
	function sendHttpRequest($requestBody)
	{
		//build auction headers using variables passed via constructor
		$headers = $this->buildAuctionHeaders(strlen($requestBody));

		//initialise a CURL session
		$connection = curl_init();

		//set the server we are using (could be Sandbox or Production server)
		curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

		//stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

		//set the headers using the array of headers
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

		//set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);

		//set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);

		//set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

		//Send the Request
		$response = curl_exec($connection);

		//close the connection
		curl_close($connection);

		//return the response
		return $response;
	}

	function buildAuctionHeaders($requestBodyLength)
	{
		$headers = array (
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: $requestBodyLength",
			"SOAPAction: $this->soapAuction"
		);

		return $headers;
	}
}
?>
<?php

 /*
 * requestCartNo
 * 상품정보를 전달하고 발급된 카트번호를 요청합니다.
 * http://www.auction.co.kr/IpayService/Ipay/InsertIpayOrder
 * 서비스 문의시에 Request SOAP과 Response SOAP을 보내주시면 됩니다.
 * 옥션 API 개발자 커뮤니티 : http://api.auction.co.kr/developer
 */
class requestCartNo
{
	var $serverUrl = "https://api.auction.co.kr/ArcheSystem/IpayService.asmx";	//실제 운영 서버 주소
	var $action = "http://www.auction.co.kr/IpayService/Ipay/InsertIpayOrder";
	var $ticket;

	function requestCartNo($ticket){
		$this->ticket = $ticket;
	}

	/*** 서비스를 실행(호출)한다.*/
	function doService($orderQuery){

		// Set Request SOAP Message
		$requestXmlBody =  '<?xml version="1.0" encoding="utf-8"?>	';
		$requestXmlBody .=  '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> ';
		$requestXmlBody .=  '  <soap:Header>		';
		$requestXmlBody .=  '   <EncryptedTicket xmlns="http://www.auction.co.kr/Security">		';
		$requestXmlBody .=	'   <Value>' . $this->ticket .  '</Value> ';
		$requestXmlBody .=  '		</EncryptedTicket> ';
		$requestXmlBody .=  '	</soap:Header> ';
		$requestXmlBody .=  '  <soap:Body>		';
		$requestXmlBody .=  '    <InsertIpayOrder xmlns="http://www.auction.co.kr/IpayService/Ipay">';
		$requestXmlBody .=  iconv('EUC-KR', 'UTF-8', $orderQuery);
		$requestXmlBody .=  '</InsertIpayOrder> ';
		$requestXmlBody .=  '</soap:Body> ';
		$requestXmlBody .=  '</soap:Envelope> ';

		$requestXmlBody = str_replace("&", "&amp;", $requestXmlBody);

		// Load the XML Document to Print Request SOAP
		//$requestDoc = new DomDocument();
		//$requestDoc->loadXML($requestXmlBody);
/*
		// Print Request SOAP
		echo "<PRE>";
		echo "<STRONG>* REQUEST SOAP</STRONG><BR>";
		//echo htmlentities ($requestDoc->saveXML());
		echo htmlentities ($requestXmlBody);
		echo "</PRE>";
*/

		//Create a new auction session with all details pulled in from included auctionSession.php
		$session = new auctionSession($this->serverUrl, $this->action);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);

		// Process Response
		return $this->processResponse($responseXml);
	}

	/**
	 * Request SOAP Message를 서버에 요청하고 받아온 Response SOAP Message를 가지고 처리한다.
	 * $responseXml	: Response SOAP Message
	 */
	function processResponse($responseXml){
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') {
			die('<P>Error sending request');
		} else {
/*
			//Xml string is parsed and creates a DOM Document object
			$responseDoc = new DomDocument();
			$responseDoc->loadXML($responseXml);
*/
			//if (!$responseDoc = domxml_open_mem($responseXml)) {
			//	echo "Error while parsing the document\n";
			//	exit;
			//}
//			echo iconv("UTF-8", "EUC-KR", urldecode (htmlentities ($responseXml, ENT_NOQUOTES, "UTF-8")));

/*
			// Print Response SOAP
			echo "<PRE>";
			echo "<STRONG>* RESPONSE SOAP</STRONG><BR>";
			echo "<BR>".iconv("UTF-8", "EUC-KR", urldecode (htmlentities ($responseDoc->saveXML(), ENT_NOQUOTES, "UTF-8")) );
			echo "</PRE>";
*/
			// Error
/*
			$eleFaultcode = $responseDoc->getElementsByTagName('faultcode')->item(0);
			$eleFaultstring = $responseDoc->getElementsByTagName('faultstring')->item(0);
			$eleResult =  $responseDoc->getElementsByTagName('InsertIpayOrderResult')->item(0);
*/
			//$tmp_eleFaultcode = $dom->get_elements_by_tagname("faultcode");
			//$tmp_eleFaultstring = $dom->get_elements_by_tagname('faultstring');
			//$tmp_eleResult = $dom->get_elements_by_tagname('InsertIpayOrderResult');
			//$eleFaultcode = $tmp_eleFaultcode[0];
			//$eleFaultstring = $tmp_eleFaultstring[0];
			//$eleResult = $tmp_eleResult[0];

			$parser = xml_parser_create();
			//xml_parse($parser, $responseXml);
			xml_parse_into_struct($parser, $responseXml, $vals, $index);
			xml_parser_free($parser);

			$eleFaultcode = $this->getXmlValueByName($index, $vals, 'faultcode');
			$eleFaultstring = $this->getXmlValueByName($index, $vals, 'faultstring');
			$eleResult = $this->getXmlValueByName($index, $vals, 'InsertIpayOrderResult');

			if ((empty($eleFaultcode)) && (!empty($eleResult)) && $eleResult > 0)
			{
				return array("result"=>$eleResult);
			}
			else {
				return array("result"=>$eleResult, "msg"=>$this->processError($eleFaultcode, $eleFaultstring));
			}
		}
		return null;
	}


	/**
	 * 에러 처리를 한다.
	 * $eleFaultcode	: 오류 코드 메시지
	 * $eleFaultstring	: 오류 메시지
	 */
	function processError($eleFaultcode, $eleFaultstring){
		$errMsg = '';
		if ($eleFaultcode) $errMsg .= "faultcode : ".iconv("UTF-8", "EUC-KR", urldecode (htmlentities ($eleFaultcode, ENT_NOQUOTES, "UTF-8")))."<BR>";
		if ($eleFaultstring) $errMsg .= "faultstring : ".iconv("UTF-8", "EUC-KR", urldecode (htmlentities ($eleFaultstring, ENT_NOQUOTES, "UTF-8")))."<BR>";
		return $errMsg;
	}

	/**
	 * xml node value 이름으로 가져오기.
	 *
	 */
	function getXmlValueByName($index, $vals, $nodeName) {
		$nodeName = strtoupper($nodeName);
		if (empty($index[$nodeName]) === false) {
			foreach($index[$nodeName] as $idx) {
				if ($vals[$idx]['tag'] ==$nodeName && isset($vals[$idx]['value'])) {
					return $vals[$idx]['value'];
				}
			}
		}
		return null;
	}
}

class AuctionIpay {
	var $auctionIpayCfg;
	var $imgTags;
	var $fpath;

	function AuctionIpay()
	{
		global $auctionIpayCfg;
		$this->fpath = dirname(__FILE__);
		if(!$auctionIpayCfg && file_exists($fpath."../conf/auctionIpay.cfg.php"))
			require $fpath."../conf/auctionIpay.cfg.php";

		if(!$checkoutBan && file_exists($fpath."../conf/auctionIpay.banWords.php"))
			require $fpath."../conf/auctionIpay.banWords.php";

		$this->auctionIpayCfg = $auctionIpayCfg;
		$this->banWords = $checkoutBan;
	}

	function get_imgTags($mode,$active,$msg=''){
		$imgTags = '';
		if($mode == 2){
			if($active == 'Y'){
				$imgTags .= '
				<script type="text/javascript">
				var ipayPop = null;
				function auctionIpay(){
					var f = document.frmView;
					f.action = "../goods/auctionIpay.php";
					f.mode.value="buy";
					f.target = "ifrmHidden";

					var _chk = false;

					// 멀티옵션 사용中
					if (typeof nsGodo_MultiOption == "object") {

						var opt_cnt = 0, data;

						nsGodo_MultiOption.clearField();

						for (var k in nsGodo_MultiOption.data) {
							data = nsGodo_MultiOption.data[k];
							if (data && typeof data == "object") {
								nsGodo_MultiOption.addField(data, opt_cnt);
								opt_cnt++;
							}
						}

						if (opt_cnt > 0) _chk = true;

					}

					if(_chk || chkForm(f)) {
						try {
							ipayPop = window.open("about:blank","","scrollbars=yes,toolbar=no,width=500,height=600");
						}
						catch(e) {
							alert("팝업이 차단되었습니다.");
							return;
						}
						f.submit();
					}
					f.mode.value="addItem";
					f.target = "";
					f.action = "";
				}
				</script>';
			}else{
				$imgTags .= '
				<script type="text/javascript">
				function auctionIpay(){
					alert("'.$msg.'");
				}
				</script>
				';
			}
		}else{
			$imgTags .= '
			<script type="text/javascript">
			var ipayPop = null;
			function auctionIpay(){
				try {
					ipayPop = window.open("about:blank","","scrollbars=yes,toolbar=no,width=500,height=600");
				}
				catch(e) {
					alert("팝업이 차단되었습니다.");
					return;
				}
				var idxs = document.getElementsByName("idxs[]");
				var param = "";
				for (var i=0,m=idxs.length;i<m;i++) {
					if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
				}
				ifrmHidden.location.href="../goods/auctionIpay.php?mode=cart"+param;
			}
			</script>';
		}
		$imgTags .= '<a onclick="auctionIpay()" style="cursor:pointer;"><img src="'.$this->auctionIpayCfg['btnType'].'" /></a>';
		return $imgTags;
	}

	function check_exceptions($goodsno)
	{
		$db = & $GLOBALS['db'];
		if($this->auctionIpayCfg['e_exceptions'] && in_array($goodsno,$this->auctionIpayCfg['e_exceptions']) ) return false;
		$res = $db->query("select category from ".GD_GOODS_LINK." where goodsno='$goodsno' and category");
		while($data = $db->fetch($res)){
			for($i=3;$i<=strlen($data['category']);$i=$i+3)
			{
				$category = substr($data['category'],0,$i);
				if($this->auctionIpayCfg['e_category'] && in_array($category,$this->auctionIpayCfg['e_category']) ) return false;
			}
		}
		return true;
	}

	function check_use(){
		global $sess;
		if($this->auctionIpayCfg['useYn'] != 'y') return false;
		if($this->auctionIpayCfg['testYn'] == 'y' && $sess['level'] <= 79) return false;
		return true;
	}

	function get_GoodsViewTag($goodsno,$goodsnm,$on=true,$msg='')
	{
		if(!$this->check_use())	return false;
		if(!$this->check_exceptions($goodsno)){
			$on = false;
			$msg = "죄송합니다. 옥션 iPay로 구매가 불가한 상품입니다.";
		}
		if(!$this->check_banWords($goodsnm)){
			$on = false;
			$msg = "죄송합니다. 옥션 iPay로 구매가 불가한 상품입니다.";
		}

		if($on) return $this->get_imgTags(2,'Y');
		else return $this->get_imgTags(2,'N',$msg);
	}

	function get_GoodsCartTag($item)
	{
		if(!$this->check_use()) return false;
		if(!$item) return false;
		$on = true;
		if($item)foreach($item as $goods)
		{
			if(!$this->check_exceptions($goods['goodsno'])) $on = false;
			if(!$this->check_banWords($goods['goodsnm'])) $on = false;
		}
		if($on) return $this->get_imgTags(1,'Y');
		else return $this->get_imgTags(1,'N');
	}

	function check_banWords($goodsnm)
	{
		return true;
		if ($this->banWords && $goodsnm) {
			foreach($this->banWords as $word)
			{
				if(preg_match('/'.$word.'/',$goodsnm) && $word) return false;
			}
		}
		return true;
	}
}
?>