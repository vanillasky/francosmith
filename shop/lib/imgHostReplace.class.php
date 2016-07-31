<?

class imgHost
{
	var $ftp;				// FTP Resource
	var $ftpConf;			// FTP 정보
	var $putCnt = 0;
	var $putMax = 5;		// FTP 1회 접속당 Img 전송가능수
	var $errMsg;			// 에러 메시지

	function imgHost($conf)
	{
		ob_start();
		if (is_string($conf) === true)
		{
			$conf = unserialize($conf);
			$conf['pass'] = decode($conf['pass'],1);
		}

		$this->ftpConf['domain'] = $conf['domain'];
		$this->ftpConf['user'][0] = $conf['userid'];
		$this->ftpConf['user'][1] = $conf['pass'];
		ob_end_clean();
	}

	### String replace(): 이미지경로→호스팅경로 치환
	function replace($source)
	{
		$split = $this->_split($source);
		for ($i=1,$s=count($split); $i < $s; $i += 2)
		{
			$prev = &$split[($i-1)];
			$self = &$split[$i];
			$next = &$split[($i+1)];

			if (preg_match('@^http:\/\/@ix', $self));
			else {
				# godoOld 속성 제거
				$prev = $this->clearGodoOld('@(<(?:[^<])+$)@ix', $prev);
				$next = $this->clearGodoOld('@(^(?:[^>])+>)@ix', $next);

				# 이미지 전송
				$src = $this->_filePut($self);

				# godoOld 속성 추가
				$quot = substr($prev,-1,1);
				if (in_array($quot, array('"', "'")) === false) $quot = '';
				$self = $src . $quot . ' godoOld=' . $quot . $self;
			}
		}
		$source = implode('', $split);
		return $source;
	}

	### String clearGodoOld(): godoOld 속성 제거 후 리턴
	function clearGodoOld($pattern, $str)
	{
		$res = preg_split($pattern, $str, 3, PREG_SPLIT_DELIM_CAPTURE);
		$res[1] = preg_replace('@ ?godoOld\=(?:"|\')(?:[^"|\'])*[^"|\']+(?:"|\')@i', '', $res[1]);
		$str = implode('', $res);
		return $str;
	}

	### Array imgStatus(): 이미지경로 현황
	function imgStatus($source)
	{
		$cnt = array();
		if (is_string($source) === true) $split = $this->_split($source);
		else $split = $source;
		for ($i=1,$s=count($split); $i < $s; $i += 2)
		{
			$cnt['tot']++;
			if (preg_match('@^http:\/\/@ix', $split[$i]));
			else {
				if (substr($split[$i],0,1) == '/') $imgPath = $_SERVER['DOCUMENT_ROOT'] . $split[$i];
				if (file_exists($imgPath) === true){
					$chkimg = getimagesize($imgPath);
					if ($chkimg[2] != 0){
						$cnt['in']++;
					}
				}
			}
		}
		return $cnt;
	}

	### Array _split(): 이미지경로 기준으로 분할
	function _split($source)
	{
		$cnt = array();
		$Ext = '(?<=src\=")(?:[^"])*[^"](?=")'.
			"|(?<=src\=')(?:[^'])*[^'](?=')".
			'|(?<=src\=\\\\")(?:[^"])*[^"](?=\\\\")'.
			"|(?<=src\=\\\\')(?:[^'])*[^'](?=\\\\')";
		$pattern = '@('. $Ext .')@ix';
		$split = preg_split($pattern, $source, -1, PREG_SPLIT_DELIM_CAPTURE);
		return $split;
	}

	### String _filePut(): 이미지파일을 호스팅전송한 후 호스팅경로 리턴
	function _filePut($path)
	{
		$imgPath = $path;
		if (substr($imgPath,0,1) == '/') $imgPath = $_SERVER['DOCUMENT_ROOT'] . $imgPath;
		if (file_exists($imgPath) === false){
			return $path;
		} else {
			$chkimg = getimagesize($imgPath);
			if ($chkimg[2] == 0) return $path;
		}
		if (is_resource($this->ftp) === false) $this->_connector();

		$this->_setDir();
		$remotePath = ($this->ftpConf['dirPath'] == '/' ? '' : $this->ftpConf['dirPath']) . '/' . basename($imgPath);
		$res = $this->_call("put", array($remotePath, $imgPath, FTP_BINARY));
		if ($res){
			$this->_call("site", array(sprintf('CHMOD %u %s', 755, $remotePath)));
			$path = 'http://' . $this->ftpConf['domain'] . $remotePath;
		}
		else if (preg_match('@Warning.*ftp_put\(\).*Ok\ to\ send\ data@ix', $this->errMsg))
		{
			header("Status: 용량 초과로 파일을 전송할 수 없습니다.", true, 400);
			echo "";
			exit;
		}

		$this->putCnt++;
		if ($this->putCnt >= $this->putMax){
			$this->putCnt = 0;
			$this->_destruct();
		}
		return $path;
	}

	### Void _setDir(): 파일업로드할 디렉토리 정의
	function _setDir()
	{
		ob_start();
		if ($this->ftpConf['dirPath'] == "")
		{
			# goods_XXXX 디렉토리 이동
			list($docRoot) = explode(".", basename($_SERVER['DOCUMENT_ROOT']));
			$docRoot = "goods_{$docRoot}";
			if (!$this->_call("chdir", array($docRoot)))
			{
				if ($this->_call("mkdir", array($docRoot)))
				{
					$this->_call("site", array(sprintf('CHMOD %u %s', 755, $docRoot)));
					$this->_call("chdir", array($docRoot));
				}
			}

			# infra 디렉토리 이동
			$ls = $this->_call("rawlist", array("/{$docRoot}"));
			if (count($ls) > 0)
			{
				foreach ($ls as $v)
				{
					$vinfo = preg_split("/[\s]+/", $v, 9);
					if ($vinfo[0] !== "total" && $vinfo[0]{0} == "d") $dirlist[] = strip_tags($vinfo[8]);
				}
				sort($dirlist, SORT_NUMERIC);
				$lastDir = $dirlist[count($dirlist) - 1];
				$last_ls = $this->_call("rawlist", array("/{$docRoot}/{$lastDir}"));
				if (count($last_ls) > 100){
					$lastDir = sprintf("%d", $lastDir + 1);
				}
			}
			else {
				$lastDir = sprintf("%d", 1);
			}
			if (!$this->_call("chdir", array($lastDir)))
			{
				if ($this->_call("mkdir", array($lastDir)))
				{
					$this->_call("site", array(sprintf('CHMOD %u %s', 755, $lastDir)));
					$this->_call("chdir", array($lastDir));
				}
			}
			$this->ftpConf['dirPath'] = $this->_call("pwd");
		}
		ob_end_clean();
	}

	### Void _connector(): 접속
	function _connector()
	{
		if ($this->ftpConf['domain'] == '')
		{
			header("Status: FTP 도메인을 입력하셔야 합니다.", true, 400);
			echo "";
			exit;
		}
		if ($this->ftpConf['user'][0] == '')
		{
			header("Status: FTP ID를 입력하셔야 합니다.", true, 400);
			echo "";
			exit;
		}
		if ($this->ftpConf['user'][1] == '')
		{
			header("Status: FTP Password를 입력하셔야 합니다.", true, 400);
			echo "";
			exit;
		}

		$ftpDomin = 'ftp.' . $this->ftpConf['domain'];
		$this->_construct($ftpDomin);
		if (is_resource($this->ftp) === false)
		{
			header("Status: FTP 도메인은 접속되지 않습니다.", true, 400);
			echo "";
			exit;
		}

		$login_result = $this->_call("login", $this->ftpConf['user']);
		if ($login_result === false)
		{
			header("Status: FTP 아이디 또는 비밀번호가 정확하지 않습니다.", true, 400);
			echo "";
			exit;
		}
	}

	### Void _construct(): Constructor
	function _construct($host, $port = 21, $timeout = 90)
	{
		$this->ftp = @ftp_connect($host, $port, $timeout);
	}

	### Void _destruct(): Destructor
	function _destruct()
	{
		@ftp_close($this->ftp);
	}

	### Mixed _call(): Re-route all function calls to the PHP-functions
	function _call($function, $arguments=array())
	{
		array_unshift($arguments, $this->ftp); // Prepend the ftp resource to the arguments array
		ob_start();
		$result = call_user_func_array('ftp_' . $function, $arguments); // Call the PHP function
		$this->errMsg = ob_get_clean();
		return $result;
	}
}

?>