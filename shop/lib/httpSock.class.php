<?

/**
 * httpSock class
 * HTTP 소켓 클래스
 */

class httpSock
{
	var $method = 'GET';		// GET or POST
	var $url = array(
			'scheme' => 'http',	// HTTP or SSL
			'host' => '',		// XXX.XX.XX
			'path' => '',		// /~.htm
			'query' => '',		// url_query
			'port' => '80'		// 80 or 443
		);
	var $errno;
	var $errstr;
	var $timeout = 10;			// 10
	var $hostname;				// hostname of fsockopen
	var $postdata = array();	// data of post
	var $header = array();		// Headers
	var $resHeader = array();	// 결과 헤더
	var $resContent = '';		// 결과 내용

	function httpSock($str,$method='GET',$data=array())
	{
		if ($method) $this->method = $method;

		## Building url
		$this->url = array_merge($this->url, parse_url($str)); # parsing the given URL
		if ($this->url['scheme'] == 'https')
		{
			$this->hostname = 'ssl://';
			$this->url['scheme'] = 'ssl';
			$this->url['port'] = '443';
		}
		$this->url['query'] = ($this->url['query'] ? '?' . $this->url['query'] : '');

		##  Building referrer
		$referrer = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://" ) . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];

		## making string from $data
		$data_string = http_build_query($data);
		
		## Building Header-request:
		if ($this->method == 'GET')
		{
			if ($data_string) $this->url['query'] .= ($this->url['query'] ? '&' : '?') . $data_string;
			$this->header[] = sprintf("GET %s HTTP/1.0", $this->url['path'] . $this->url['query']);
			$this->header[] = sprintf("Host: %s:%s", $this->url['host'], $this->url['port']);
			$this->header[] = sprintf("Referer: %s", $referrer);
			$this->header[] = sprintf("Connection: close");
		}
		else {
			$this->header[] = sprintf("POST %s HTTP/1.0", $this->url['path'] . $this->url['query']);
			$this->header[] = sprintf("Host: %s:%s", $this->url['host'], $this->url['port']);
			$this->header[] = sprintf("Referer: %s", $referrer);
			$this->header[] = sprintf("Content-type: %s", "application/x-www-form-urlencoded");
			$this->header[] = sprintf("Content-length: %d", strlen($data_string));
			$this->header[] = sprintf("Accept: */*");
			$this->header[] = sprintf("Connection: close");
			$this->header[] = "";
			$this->header[] = sprintf("%s", $data_string);
		}
	}

	function send($echo=false)
	{
		ob_start();
		$fp = fsockopen($this->hostname . $this->url['host'], $this->url['port'], $this->errno, $this->errstr, $this->timeout);
		$get = ob_get_clean();
		if (strpos($get,'php_network_getaddresses') !== false){
			$this->errno = '000';
			$this->errstr = 'check dns(host)';
		}
		if ($fp)
		{
			if ($echo) echo "Connected\n";
			foreach ($this->header as $v) fwrite($fp, "{$v}\r\n");
			fwrite($fp, "\r\n");
			if ($echo) echo "Date Transfer\n";

			### Header Response
			$send = '';
			do {
				$send .= fgets ( $fp, 4096 );
			} while ( strpos ( $send, "\r\n\r\n" ) === false );
			$this->resHeader = $this->decode_header ( $send );
			if ($echo) echo "{$send}\n";

			### Content Response
			while ( ! feof ( $fp ) ) $this->resContent .= fread ( $fp, 8192 );

			fclose ( $fp );
		}
		else if ($echo) echo sprintf("Unconnected : [%s] %s\n", $this->errno, $this->errstr);
	}

	function decode_header ($str)
	{
	   $part = preg_split("/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY);
	   $out = array ();
	   for ($h = 0; $h < sizeof($part); $h++)
	   {
	       if ($h != 0)
	       {
	           $pos = strpos($part[$h], ':');
	           $k = strtolower(str_replace(' ', '', substr ( $part[$h], 0, $pos)));
	           $v = trim(substr($part[$h], ($pos + 1)));
	       }
	       else {
	           $k = 'status-code';
	           $v = explode(' ', $part[$h]);
	           $v = $v[1];
	       }

	       if ($k == 'set-cookie') $out['cookies'][] = $v;
	       else if ($k == 'content-type')
	       {
	           if (($cs = strpos($v, ';')) !== false) $out[$k] = substr($v, 0, $cs);
	           else $out[$k] = $v;
	       }
	       else $out[$k] = $v;
	   }
	   return $out;
	}

	function getStatusCode($code)
	{
		switch ($code)
		{
			case 100 : $name = "Continue";break;
			case 101 : $name = "Switching protocols";break;
			case 200 : $name = "Complete, 전송 성공";break;
			case 201 : $name = "Created, POST 명령 실행 및 성공";break;
			case 202 : $name = "Accepted, 서버가 클라이언트 명령을 받음";break;
			case 203 : $name = "Non-authoritative information, 서버가 클라이언트 요구 중 일부 만 전송";break;
			case 204 : $name = "No content, 클라언트 요구를 처리했으나 전송할 데이터가 없음";break;
			case 205 : $name = "Reset content";break;
			case 206 : $name = "Partial content";break;
			case 300 : $name = "Multiple choices, 최근에 옮겨진 데이터를 요청";break;
			case 301 : $name = "Moved permanently, 요구한 데이터를 변경된 임시 URL에서 찾았음";break;
			case 302 : $name = "Moved temporarily, 요구한 데이터가 변경된 URL에 있음을 명시";break;
			case 303 : $name = "See other, 요구한 데이터를 변경하지 않았기 때문에 문제가 있음";break;
			case 304 : $name = "Not modified";break;
			case 305 : $name = "Use proxy";break;
			case 400 : $name = "Bad request, 클라이언트의 잘못된 요청으로 처리할 수 없음";break;
			case 401 : $name = "Unauthorized, 클라이언트의 인증 실패";break;
			case 402 : $name = "Payment required, 예약됨";break;
			case 403 : $name = "Forbidden, 접근이 거부된 문서를 요청함";break;
			case 404 : $name = "Not found, 문서를 찾을 수 없음";break;
			case 405 : $name = "Method not allowed, 리소스를 허용안함";break;
			case 406 : $name = "Not acceptable, 허용할 수 없음";break;
			case 407 : $name = "Proxy authentication required, 프록시 인증 필요";break;
			case 408 : $name = "Request timeout, 요청시간이 지남";break;
			case 409 : $name = "Conflict";break;
			case 410 : $name = "Gone, 영구적으로 사용할 수 없음";break;
			case 411 : $name = "Length required";break;
			case 412 : $name = "Precondition failed, 전체조건 실패";break;
			case 413 : $name = "Request entity too large";break;
			case 414 : $name = "Request-URI too long, URL이 너무 김";break;
			case 415 : $name = "Unsupported media type";break;
			case 500 : $name = "Internal server error, 내부서버 오류";break;
			case 501 : $name = "Not implemented, 클라이언트에서 서버가 수행할 수 없는 행동을 요구함";break;
			case 502 : $name = "Bad gateway, 서버의 과부하 상태";break;
			case 503 : $name = "Service unavailable, 외부 서비스가 죽었거나 현재 멈춤 상태";break;
			case 504 : $name = "Gateway timeout";break;
			case 505 : $name = "HTTP version not supported";break;
		}
		$message = "[$code] $name";
		return $message;
	}

}

//$post = array("test1"=>"1","test2"=>"2","test3"=>"3");
//
/////* -----------------------------------------
//$httpSock = new httpSock('https://www.godo.co.kr/test_psh.php?code=ddd&id=afdsaf', 'POST', $post);
//$httpSock->send();
//debug($httpSock->resHeader);
//debug($httpSock->resContent);
//debug($httpSock->errno);
//debug($httpSock->errstr);
//
//
////------------------------------------------*/
//echo '<hr />';
//$httpSock = new httpSock('https://www.godo.co.kr/test_psh.php?code=ddd&id=afdsaf', 'GET', $post);
//$httpSock->send();
//debug($httpSock->resHeader);
//debug($httpSock->resContent);
//debug($httpSock->errno);
//debug($httpSock->errstr);
//
//
////------------------------------------------*/
//echo '<hr />';
//$httpSock = new httpSock('http://www.interpark.com/order/OrderClmExInterface.do?_method=orderListForComm&entrId=GODO&strDate=20080301000000&endDate=20080310000000');
//$httpSock->send();
//debug($httpSock->resHeader);
//debug($httpSock->resContent);
//debug($httpSock->errno);
//debug($httpSock->errstr);
//
//
////------------------------------------------*/

?>