<?

class putLog
{
	var $logCode;
	var $logFile;
	function putLog($logFile)
	{
		$this->logFile = $logFile;
		$this->logCode = time();
		if ($this->logPath == '') $this->logPath = dirname(__FILE__) . "/../log/";
	}

	### ERROR
	function err($obOut='')
	{
		if (mysql_errno())
		{
			$this->log("DB_ERROR:[" . mysql_errno() . "] " . mysql_error());
			return true;
		}
		else if ($obOut)
		{
			if (preg_match("/<span style='font:8pt tahoma'><b>\[ERROR\]<\/b>/i", $obOut)) return false; # 메일관련 메시지는 통과
			$obOut = str_replace($_SERVER['DOCUMENT_ROOT'], "", $obOut);
			$obOut = strip_tags(str_replace("<br>", "\n", $obOut));
			$tmp_o = explode("\n", $obOut);
			$title = (strpos($obOut, 'Warning') !== false ? "CODE_ERROR" : "MESSAGE");
			if (count($tmp_o) == 1) $this->log($title . ":" . $obOut);
			else
			{
				$this->log($title . ":[");
				foreach ($tmp_o as $k => $str)
				{
					if (trim($str) == '') unset($tmp_o[$k]);
					else $tmp_o[$k] = "\t" . trim($str);
				}
				$this->log($tmp_o);
				$this->log("]");
			}
			return true;
		}
	}

	### Ending Header
	function endHeader($msg, $code=600)
	{
		if (empty($msg['log']) === false) $this->log($msg['log']);
		$this->log("END");
		header("Status: " . $msg['header'], true, $code);
		echo "";
		exit;
	}

	### 로그 남기기
	function log( $msg )
	{
		## 메시지 재정의
		$front = "DEBUG [__datetime__] <__logCode__> ";
		if (is_array($msg)) $msg = implode("\n{$front}", $msg);
		if ( $msg == 'START' ) $msg = "\n" . "INFO  [__datetime__] <__logCode__> START";
		else if ( $msg == 'END' ) $msg = "INFO  [__datetime__] <__logCode__> END";
		else $msg = $front . $msg;
		$msg = str_replace( array('__datetime__', '__logCode__'), array(date('Y-m-d_H:i:s:B'), $this->logCode), $msg ) . "\n";

		## 요일기준 : 동일한 요일이지만 날짜가 다르면 파일 삭제
		$filepath = $this->logPath . sprintf("%s_%s.log", date('D'), $this->logFile);
		if (file_exists($filepath)){
			if (date('Ymd', filemtime($filepath)) != date('Ymd')){
				@unlink($filepath);
			}
		}
		error_log($msg, 3, $filepath);
		@chmod( $filepath, 0777 );
	}
}

?>