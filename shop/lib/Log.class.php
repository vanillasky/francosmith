<?php
/**
 * @todo
 *
 */
Class Log {
	var $logDir = '';		// 로그 저장되는 기본 디렉토리
	var $logFile = '';		// 로그 저장되는 파일명
	var $logMsg = '';		// 로그 저장되는 내용
	var $logProtectDir = '';	//로그 관리의 보호 디렉토리 - 해당 경로 외의 삭제나 생성은 안됨

	/**
	 * Constructor
	 */
	function Log() {
		$this->logProtectDir = dirname(__FILE__).'/../log';
	}

	function setLogDir($dir) {
		$this->logDir = $dir;
	}
	function setLogProtectDir($protectDir) {
		$this->logProtectDir = $protectDir;
	}
	function setLogFile($fileName) {
		$this->logFile = $fileName;
	}
	function setLogMsg($msg) {
		$this->logMsg = $msg;
	}
	function getLogDir() {
		return $this->logDir;
	}
	function getLogProtectDir() {
		return $this->logProtectDir;
	}
	function getLogFile() {
		return $this->logFile;
	}
	function getLogMsg() {
		return $this->logMsg;
	}

	/**
	 * 파일 삭제
	 * @param array $delArr 절대경로 포함한 파일명 또는 디렉토리
	 */
	function deletePathArray($delArr) {
		$return = array();
		foreach($delArr as $value) {
			if(@is_dir($value)) {
				$this->deleteDirectory($value);
			} else {
				$pos = strpos($dir, $this->getLogProtectDir());
				if($pos !== false){
					@unlink($value);
				} else {
					$return['code'] = "003";
					$return['msg'] = $value." 삭제 실패되었습니다.";
				}
			}
		}
	}

	/**
	 * 디렉토리 삭제
	 * 하위 디렉토리 및 파일 삭제 후 요청 디렉토리 삭제
	 * @param string $dir 삭제될 디렉토리 경로
	 * @example $delReturn = $this->deleteDirectory("/www/s4self/shop/log/admin");
	 * @return array 처리결과 (오류코드 오류메세지)
	 */
	function deleteDirectory($dir) {
		$return = array();
		$pos = strpos($dir, $this->getLogProtectDir());
		if($pos === false){
			$return['dir']['code'] = "003";
			$return['dir']['msg'] = $dir." 삭제 실패되었습니다.";
		} else {
			if(!@is_dir($dir)) {
				$return['dir']['code'] = "001";
				$return['dir']['msg'] = $dir." 디렉토리를 확인하십시요.";
			} else {
				if (substr($dir, strlen($dir) - 1, 1) != DIRECTORY_SEPARATOR) {
					$dir .= DIRECTORY_SEPARATOR;
				}
				$dirPath = glob($dir."*",GLOB_MARK);
				foreach($dirPath as $dirNow) {
					if(@is_dir($dirNow)) {
						$this->deleteDirectory($dirNow);
					} else {
						@unlink($dirNow);
					}
				}
				if(@rmdir($dir)) {
					$return['dir']['code'] = "000";
					$return['dir']['msg'] = "삭제되었습니다.";
				} else {
					$return['dir']['code'] = "002";
					$return['dir']['msg'] = $dir." 삭제 실패되었습니다.";
				}
			}
		}
		return $return;
	}

	/**
	 * 디렉토리 생성
	 * 요청 디렉토리까지 없으면 생성
	 * @param string $dir 생성될 디렉토리 경로
	 * @example $makeReturn = $this->makeDirectory("/www/s4self/shop/log/admin");
	 * @return array 처리결과 (오류코드 오류메세지)
	 */
	function makeDirectory($dir) {
		$return = array();
		$pos = strpos($dir, $this->getLogProtectDir());
		if($pos === false){
			$return['code'] = "003";
			$return['msg'] = $dir." 생성 실패되었습니다.";
		} else {
			$dirFull = "";
			$dirPath = explode(DIRECTORY_SEPARATOR, $dir);
			foreach($dirPath as $dirNow) {
				if($dirNow) {
					$dirNow = DIRECTORY_SEPARATOR.$dirNow;
					$dirFull .= $dirNow;
					if (!@is_dir($dirFull)) {
						@umask(0);
						if(@mkdir($dirFull, 0707)) {
							$return['code'] = "000";
							$return['msg'] = "생성되었습니다.";
						} else {
							$return['code'] = "001";
							$return['msg'] = $dirFull." 생성 실패되었습니다.";
						}
					} else {
						$return['code'] = "002";
						$return['msg'] = "이미 존재하는 디렉토리입니다.";
					}
				} else {
					$return['code'] = "004";
					$return['msg'] = "생성 실패되었습니다.";
				}
			}
		}
		return $return;
	}

	/**
	 * path 생성 (argument list를 절대경로로 만듬)
	 * @param argument list
	 * @example $logDir = $this->buildPath('shop', 'log', date('Ym'));
	 * @return string 처리결과 (/shop/log/201506)
	 */
	function buildPath() {
		$segments = func_get_args();
		$path = join(DIRECTORY_SEPARATOR, $segments);
		return $this->getAbsolutePath($path);
	}

	/**
	 * 절대경로 상에 (../, ./)이 포함된 경로를 제거하여 경로 반환
	 * @param string $path 절대경로
	 * @return string $path 절대경로
	 */
	function getAbsolutePath($path) {
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		return DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $absolutes);
	}

	/**
	 * 압축
	 * @param string $zipTargetDirectory 압축할 폴더
	 * @param string $zipTargetFile 압축할 파일
	 * @return 처리결과(오류코드 or array 압축결과)
	 */
	function makeZipLogFile($zipTargetDir, $zipTargetFile) {
		include_once dirname(__FILE__).'/pclzip/pclzip.lib.php';

		$zipTargetDir = $this->getAbsolutePath($zipTargetDir);

		$zipFile = $zipTargetDir.DIRECTORY_SEPARATOR.$zipTargetFile.'.zip';
		$targetFile = $zipTargetDir.DIRECTORY_SEPARATOR.$zipTargetFile;

		$zipLog = new PclZip($zipFile);
		$returnZip = $zipLog->create($targetFile);
		for($i=0; $i<sizeof($returnZip); $i++) {
			if($returnZip[$i]['status'] == 'ok') {
				@chmod($zipFile, 0707);
				@unlink($targetFile);
			}
		}

		return $returnZip;
	}

	/**
	 * 로그 작성
	 * @param string $meg 로그 내용
	 * @param string $logDir 로그 절대경로
	 * @param string $logFile 로그 파일명
	 */
	function writeLog() {
		if(!$this->getLogDir()) {
			$error['code'] = '001';
			$error['msg'] = '저장 경로가 없습니다.';
			return $error;
		}
		if(!$this->getLogFile()) {
			$error['code'] = '002';
			$error['msg'] = '저장 파일명이 없습니다.';
			return $error;
		}
		if(!$this->getLogMsg()) {
			$error['code'] = '003';
			$error['msg'] = '저장 메세지가 없습니다.';
			return $error;
		}
		if($this->makeDirectory($this->getLogDir())) {
			$logFile = $this->buildPath($this->getLogDir(), $this->getLogFile());
			@error_log($this->getLogMsg(), 3, $logFile);
			@chmod($logFile, 0707);
		}
	}
}