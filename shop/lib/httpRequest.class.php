<?
	/**
	* @author crizin <crizin@gmail.com>
	* @version 2007.02.03.01
	* @copyright LGPL
	*/

	class HTTPRequest {
		var $debug = false;
		var $connectionTrial = 0;
		var $socket, $path = '/', $host, $port, $timeout, $method = 'GET', $content;
		var $socketErrorNumber, $socketErrorDescription;
		var $requestHeaders = array();
		var $responseHeaders = array();
		var $cookies = array();
		var $status, $responseText;
		var $proxyHost = false, $proxyPort = false;
		var $timer;
		var $attachedFiles = array();
		var $fileToSave = false;

		function HTTPRequest($host, $port=80, $timeout=5) {
			$this->timer = new Timer();
			$this->host = $host;
			$this->port = $port;
			$this->timeout = $timeout;
			$this->initializeHeaders();
		}

		function debug($switch=true) {
			$this->debug = $switch;
		}

		function initializeHeaders() {
			$this->requestHeaders = array();
			$this->setRequestHeader('Accept', '*/*');
			$this->setRequestHeader('User-Agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
			$this->setRequestHeader('Accept-Language', 'ko');
			$this->setRequestHeader('Host', $this->host.($this->port==80?'':":{$this->port}"));
			$this->setRequestHeader('Referer', "http://{$this->host}");
		}

		function authorization($id, $password) {
			$this->setRequestHeader('Authorization', 'Basic '.base64_encode("$id:$password"));
		}

		function setRequestHeader($name, $value) {
			$this->requestHeaders[$name] = $value;
		}

		function removeRequestHeader($name) {
			unset($this->requestHeaders[$name]);
		}

		function getResponseHeader($name) {
			if(isset($this->responseHeaders[$name]))
				return $this->responseHeaders[$name];
			foreach($this->responseHeaders as $key => $value)
				if(strtolower($key) == strtolower($name))
					return $value;
			return false;
		}

		function setPath($path) {
			$this->path = $path;
			$this->method = 'GET';
			$this->removeRequestHeader('Content-Length');
			$this->removeRequestHeader('Content-Type');
		}

		function saveToFile($path) {
			return ($this->fileToSave = fopen($path, 'w'));
		}

		function open($host=null, $port=null, $timeout=null) {
			$this->close();
			if(!is_null($host)) {
				$this->host = $host;
				$this->initializeHeaders();
			}
			if(strpos($this->host, 'http://') === 0) {
				if(!$info = @parse_url($this->host))
					return false;
				$this->host = $info['host'];
				$this->port = isset($info['port']) ? $info['port'] : $this->port;
				$path = (isset($info['path']) ? $info['path'] : '/') . (isset($info['query']) ? "?{$info['query']}" : '');
				$this->setPath($path);
				$this->setRequestHeader('Host', $this->host.($this->port==80?'':":{$this->port}"));
				$this->setRequestHeader('Referer', "http://{$this->host}");
			}
			if($port)
				$this->port = $port;
			if($timeout)
				$this->timeout = $timeout;
			if($this->proxyHost)
				$this->socket = @fsockopen($this->proxyHost, $this->proxyPort, $this->socketErrorNumber, $this->socketErrorDescription, $this->timeout);
			else
				$this->socket = @fsockopen($this->host, $this->port, $this->socketErrorNumber, $this->socketErrorDescription, $this->timeout);
			if($this->socket)
				socket_set_timeout($this->socket, $this->timeout);
			return $this->socket;
		}

		function attachFile($name, $filename, $customFilename=null) {
			return is_readable($filename) ? array_push($this->attachedFiles, array($name, $filename, is_null($customFilename) ? $filename : $customFilename)) : false;
		}

		function attachRemoteFile($name, $filename, $customFilename=null) {
			return (@file_get_contents($filename)) ? array_push($this->attachedFiles, array($name, $filename, is_null($customFilename) ? $filename : $customFilename)) : false;
		}

		function reconnect($silent=false) {
			if($this->debug && !$silent && $this->connectionTrial++ > 0) {
				echo '<fieldset style="font: 9pt/1.5 \'Crizin Code\', Gulim, Monospace; word-break: break-all; color: #383; display: block"><legend style="font: bold 8pt Verdana, Sans-serif; margin-bottom: 7px">&nbsp;Notice&nbsp;</legend>Connection lost. Now opening socket again.</fieldset>';
				flush();
			}
			if($this->connectionTrial > 3)
				return false;
			else {
				$this->close();
				if($this->open())
					return true;
				else if($this->connectionTrial == 1 && $this->debug) {
					echo '<fieldset style="font: 9pt/1.5 \'Crizin Code\', Gulim, Monospace; word-break: break-all; color: #383; display: block"><legend style="font: bold 8pt Verdana, Sans-serif; margin-bottom: 7px">&nbsp;Notice&nbsp;</legend>Connection failed.<br/>', "Error number : $this->socketErrorNumber<br/>Error message : $this->socketErrorDescription<br/></fieldset>";
					flush();
				}
				return false;
			}
		}

		function close() {
			@fclose($this->socket);
			unset($this->socket);
		}

		function getContentType($filename) {
			return 'application/octet-stream';
		}

		function send($content=null) {
			$this->responseText = '';
			$this->content = $content;
			if(!is_null($this->content)) {
				$this->method = 'POST';
				$this->setRequestHeader('Content-Length', strlen($this->content));
				$this->setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			}
			if(count($this->attachedFiles) > 0) {
				$this->method = 'POST';
				$boundary = '---------------------------'.substr(md5(time()), 0, 13);
				$this->setRequestHeader('Content-Type', 'multipart/form-data; boundary='.$boundary);
				$body = '';
				if(strlen($this->content) > 0) {
					foreach(explode('&', $this->content) as $data) {
						list($name, $value) = explode('=', $data, 2);
						$body .= "--$boundary\r\n";
						$body .= "Content-Disposition: form-data; name=\"$name\"\r\n\r\n";
						$body .= "$value\r\n";
					}
				}
				foreach($this->attachedFiles as $file) {
					list($name, $filename, $customFilename) = $file;
					if($chunk = file_get_contents($filename)) {
						$body .= "--$boundary\r\n";
						$body .= "Content-Disposition: form-data; name=\"$name\"; filename=\"$customFilename\"\r\n";
						$body .= 'Content-Type: '.$this->getContentType($filename)."\r\n\r\n";
						$body .= "$chunk\r\n";
					}
					unset($chunk);
				}
				$this->content = $body."--$boundary--\r\n";
				$this->setRequestHeader('Content-Length', strlen($this->content));
			}
			if(@$this->socket) {
				for($trial = 0; $trial < 5; $trial++) {
					if($this->proxyHost) {
						$request = "{$this->method} http://{$this->host}".($this->port==80?'':":{$this->port}")."{$this->path} HTTP/1.0\r\n";
						$this->setRequestHeader('Proxy-Connection', 'Keep-Alive');
					}
					else {
						$request = "{$this->method} {$this->path} HTTP/1.1\r\n";
						$this->setRequestHeader('Connection', 'Keep-Alive');
						$this->setRequestHeader('Cache-Control', 'no-cache');
					}
					foreach($this->requestHeaders as $name => $value)
						$request .= "$name: $value\r\n";
					if(count($this->cookies) > 0) {
						$request .= 'Cookie: ';
						foreach($this->cookies as $name => $value)
							$request .= "$name=$value; ";
						$request = substr($request, 0, strlen($request) - 2)."\r\n";
					}
					$request .= "\r\n";
					if(($this->method == 'POST' && $this->content) || count($this->attachedFiles) > 0)
						$request .= $this->content;
					if($this->debug) {
						echo '<fieldset style="font: 9pt/1.5 \'Crizin Code\', Gulim, Monospace; word-break: break-all; color: #e33; display: block"><legend style="font: bold 8pt Verdana, Sans-serif; margin-bottom: 7px">&nbsp;Request (';
						if($this->proxyHost)
							echo 'via proxy ', $this->proxyHost.($this->proxyPort==80?'':":{$this->proxyPort}");
						else
							echo $this->host.($this->port==80?'':":{$this->port}");
						echo ')&nbsp;</legend>'.nl2br(htmlspecialchars(rtrim($request))).'</fieldset>';
						flush();
					}
					$this->timer->start();
					if(!$this->socket)
						return $this->reconnect();
					fwrite($this->socket, $request);
					while(true) {
						$line = fgets($this->socket);
						$this->responseHeaders = array('__STATUS__' => $line);
						if(!preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $line, $matches))
							return $this->reconnect() ? $this->send($content) : false;
						$this->status = intval($matches[1]);
						while($line = fgets($this->socket)) {
							if($line == "\r\n")
								break;
							$header = explode(': ', $line, 2);
							if(count($header) != 2)
								continue;
							if($header[0] == 'Set-Cookie') {
								preg_match('/^([^=]+)=([^;]*)/', $header[1], $matches);
								if(count($matches) == 3)
									$this->setCookie($matches[1], rawurldecode($matches[2]));
							}
							else
								$this->responseHeaders[$header[0]] = rtrim($header[1]);
						}
						if($this->status == 100) {
							if($this->debug)
								$this->printReceivedData();
						}
						else
							break;
					}
					if($this->status >= 300 && $this->status <= 302) {
						if($this->getResponseHeader('Location')) {
							if($this->debug)
								$this->printReceivedData();
							$info = parse_url($this->getResponseHeader('Location'));
							$host = isset($info['host']) ? $info['host'] : $this->host;
							$port = isset($info['port']) ? $info['port'] : $this->port;
							$path = (isset($info['path']) ? $info['path'] : '/') . (isset($info['query']) ? "?{$info['query']}" : '');
							if($this->open("http://$host$path", $port, $this->timeout))
								continue;
						}
						else
							return false;
					}
					else
						break;
				}
				if($trial == 5)
					return false;
				if($this->getResponseHeader('Transfer-Encoding') == 'chunked') {
					while($line = fgets($this->socket)) {
						$size = hexdec(trim($line));
						if($size == 0)
							break;
						$buffer = '';
						while(strlen($buffer) < $size + 2)
							$buffer .= fread($this->socket, $size + 2 - strlen($buffer));
						if($this->fileToSave)
							fwrite($this->fileToSave, substr($buffer, 0, strlen($buffer) - 2));
						else
							$this->responseText .= substr($buffer, 0, strlen($buffer) - 2);
					}
				}
				else if($this->getResponseHeader('Content-Length') !== false) {
					$bufferdLength = 0;
					while($bufferdLength < $this->getResponseHeader('Content-Length')) {
						if($this->fileToSave)
							$bufferdLength += fwrite($this->fileToSave, fread($this->socket, $this->getResponseHeader('Content-Length') - strlen($this->responseText)));
						else {
							$this->responseText .= fread($this->socket, $this->getResponseHeader('Content-Length') - strlen($this->responseText));
							$bufferdLength = strlen($this->responseText);
						}
					}
				}
				else {
					while(!feof($this->socket)) {
						if($this->fileToSave)
							fwrite($this->fileToSave, fread($this->socket, 10240));
						else
							$this->responseText .= fread($this->socket, 10240);
					}
				}
				if($this->fileToSave) {
					$this->responseText = '(Content was saved to file - '.number_format(ftell($this->fileToSave)).' Bytes)';
					fclose($this->fileToSave);
					$this->fileToSave = false;
				}
				if($this->getResponseHeader('Connection') == 'close')
					$this->reconnect(true);
				if($this->debug)
					$this->printReceivedData();
				$this->connectionTrial = 1;
				return true;
			}
			else
				return $this->reconnect() ? $this->send($content) : false;
		}

		function setCookie($name, $value) {
			$this->cookies[$name] = $value;
		}

		function getCookie($name) {
			return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
		}

		function deleteCookie($name) {
			unset($this->cookies[$name]);
		}

		function importCookie($str) {
			$cookies = explode(';', $str);
			foreach($cookies as $cookie) {
				$cookie = trim($cookie);
				if(!empty($cookie)) {
					list($name, $value) = explode('=', $cookie, 2);
					$this->setCookie($name, $value);
				}
			}
		}

		function setProxy($host, $port) {
			$this->proxyHost = $host;
			$this->proxyPort = $port;
		}

		function printReceivedData() {
			echo '<div style="margin-bottom: 30px; font: 9pt/1.5 \'Crizin Code\', Gulim, Monospace; word-break: break-all">';
			if(count($this->responseHeaders) > 0) {
				echo '<fieldset style="display: block"><legend style="font: bold 8pt Verdana, Sans-serif; margin-bottom: 7px">&nbsp;Headers ('.$this->status.')&nbsp;</legend>';
				foreach($this->responseHeaders as $name => $value)
					echo (($name == '__STATUS__') ? '' : "$name: ").htmlspecialchars($value).'<br/>';
				echo '</fieldset>';
			}
			if(count($this->cookies) > 0) {
				echo '<fieldset style="display: block"><legend style="font: bold 8pt Verdana, Sans-serif; margin-bottom: 7px">&nbsp;Cookies&nbsp;</legend>';
				foreach($this->cookies as $name => $value)
					echo "<strong>$name</strong>: ".htmlspecialchars($value).'<br/>';
				echo '</fieldset>';
			}
			if(!empty($this->responseText)) {
				echo '<fieldset style="display: block"><legend style="font: bold 8pt Verdana, Sans-serif; margin-bottom: 7px">&nbsp;Content ('.$this->timer->fetch().' sec)&nbsp;</legend>';
				if(($size = strlen($this->responseText)) > 300)
					echo '<a href="#" onclick="this.nextSibling.style.display=\'block\'; this.parentNode.removeChild(this); return false" style="color: #999; text-decoration: none">'.nl2br(htmlspecialchars(substr($this->responseText, 0, 300))).'...<br/><span style="color: #555">(click to see full content)</span></a><div style="display: none">'.nl2br(str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', htmlspecialchars($this->responseText))).'</div>';
				else
					echo nl2br(htmlspecialchars($this->responseText));
				echo '</fieldset>';
			}
			echo '</div>';
			flush();
		}

		function existText($string) {
			return strpos($this->responseText, $string) !== false;
		}

		function getAllTextFragments($prefix, $postfix, $index=false) {
			return String::getAllTextFragments($this->responseText, $prefix, $postfix, $index);
		}

		function getTextFragment($prefix, $postfix, $index=1) {
			return $this->getAllTextFragments($prefix, $postfix, $index);
		}
	}

	class String {
		function getAllTextFragments($string, $prefix, $postfix, $index=false) {
			$prefix = preg_quote($prefix, '/');
			$postfix = preg_quote($postfix, '/');
			if(preg_match_all("/$prefix(.*)$postfix/Us", $string, $matches) === 0)
				return false;
			if($index)
				return ($index - 1 < count($matches[1])) ? $matches[1][$index-1] : false;
			return $matches[1];
		}

		function getTextFragment($string, $prefix, $postfix, $index=1) {
			return String::getAllTextFragments($string, $prefix, $postfix, $index);
		}
	}

	class Timer {
		var $start, $stop = 0;

		function Timer() {
			$this->start();
		}

		function start() {
			$this->start = $this->getMicroTime();
		}

		function pause() {
			$this->stop = $this->getMicroTime();
		}

		function resume() {
			$this->start += $this->getMicroTime() - $this->stop;
			$this->stop = 0;
		}

		function fetch($decimalPlaces = 3) {
			return sprintf('%.3f', round(($this->getMicrotime() - $this->start), $decimalPlaces));
		}

		function getMicroTime() {
			list($usec, $sec) = explode(' ', microtime());
			return (float)$usec + (float)$sec;
		}
	}

/*
require 'Crizin.HTTPRequest.php';

    // Create instance
    $h = new HTTPRequest('www.foo.com');
    // Turn on debug mode (request/response streams will be print)
    $h->debug(true);
    // Setting 'PATH'
    $h->setPath('/');
    // Request by GET method
    $h->send();
    // Get responsed informations
    $responseText = $h->responseText;
    $contentType = $h->getResponseHeader('Content-Type');

    // Let's try to another host into 8080 port
    $h->open('www.bar.com', 8080);
    // Path and GET parameter string
    $h->setPath('/accept.php?mode=login&and=more');
    // Send around proxy server
    $h->setProxy('www.proxy.com', 8888);
    // Modify 'User-Agent' header
    $h->setRequestHeader('User-Agent', 'Gozilla/1.0');
    // Import from cookie string
    $h->importCookie('COOK1=foo; COOK2=bar;');
    // Set cookie
    $h->setCookie('cook', 'something');
    // Request by POST method
    $h->send('param1=value1&param2=value2');
    // Get cookie
    $sessionId = $h->getCookie('PHPSESSID');

    // Download image
    $h->setPath('/images/logo.gif');
    // Login with apache authorization
    $h->authorization('myId', 'myPassword');
    // Hide 'User-Agent' header
    $h->removeRequestHeader('User-Agent');
    // Response content will be saved in './save'
    $h->saveToFile('./save');

    // Upload some files
    $h->setPath('/write.php');
    // <input type="file" name="image" value="/files/blah.gif"/> and rename to 'myImage.gif'
    $h->attachFile('image', './files/blah.gif', 'myImage.gif');
    // Request
    $h->send();

    // Test some text exists in $responseText or not
    $result = $h->existText('OK') ? 'Succeed' : 'Failed';
    // Get all text fragments was surrounded by '<td>' and '</td>' (array will be return)
    $result = $h->getAllTextFragments('<td>', '</td>');
    // Get 5th text only (string will be return)
    $result = $h->getTextFragment('<td>', '</td>', 5);
*/
?>