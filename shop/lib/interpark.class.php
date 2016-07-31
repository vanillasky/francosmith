<?

class interparkConf
{
	### 상점정보
	function getGodo()
	{
		ob_start();
		$file = dirname(__FILE__) . "/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$this->godo = decode($file[1],1);
		ob_end_clean();
	}

	### 인터파크정보
	function getInpk()
	{
		// Root Directory
		ob_start();
		@include_once dirname(__FILE__) . "/../conf/config.php";
		$this->rootDir = $cfg[rootDir];
		if ($this->rootDir == ''){
			$this->rootDir = str_replace(array($_SERVER['DOCUMENT_ROOT'], "/lib"), "", dirname(__FILE__));
		}

		// 인터파크
		if (isset($GLOBALS['inpkCfg'])){
			$this->inpkCfg = $GLOBALS['inpkCfg'];
		}
		else {
			@include_once dirname(__FILE__) . "/../conf/interpark.php";
			$this->inpkCfg = $inpkCfg;
		}
		ob_end_clean();
	}

	### 인터파크 오픈스타일 정보
	function getInpkOS()
	{
		// Root Directory
		ob_start();
		@include_once dirname(__FILE__) . "/../conf/config.php";
		$this->rootDir = $cfg[rootDir];
		if ($this->rootDir == ''){
			$this->rootDir = str_replace(array($_SERVER['DOCUMENT_ROOT'], "/lib"), "", dirname(__FILE__));
		}

		// 인터파크
		if (isset($GLOBALS['inpkOSCfg'])){
			$this->inpkOSCfg = $GLOBALS['inpkOSCfg'];
		}
		else {
			@include_once dirname(__FILE__) . "/../conf/interparkOpenStyle.php";
			$this->inpkOSCfg = $inpkOSCfg;
		}
		ob_end_clean();
	}

}


class interpark extends interparkConf
{
	function hashdata(&$data)
	{
		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - godosno 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$data[godosno]	= $this->godo[sno];					# 상점번호
		$data[hashdata]	= md5($data[godosno]);				# hashdata 생성
	}

	function isExists($args)
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도로 문의하세요.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - 상점아이디가 환경정보의 상점아이디와 일치하지 않습니다.^고도로 문의하세요.");

		$this->hashdata($data=array());
		return array('400', readurl("http://godointerpark.godo.co.kr/sock_isExists.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}"));
	}

	function putMerchant($args)
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도로 문의하세요.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - 상점아이디가 환경정보의 상점아이디와 일치하지 않습니다.^고도로 문의하세요.");

		$this->hashdata($data=array());
		$data = array_merge($data, $args);
		unset($data[mode]);
		unset($data[dummy]);
		return array('400', readpost("http://godointerpark.godo.co.kr/sock_putMerchant.php", $data));
	}

	function getShopCategory($data)
	{
		return readurl("http://godointerpark.godo.co.kr/sock_getShopCategory.php?callCate=".urlencode($data[callCate]));
	}

	function getDispSrch($data)
	{
		return readurl("http://godointerpark.godo.co.kr/sock_getDispSrch.php?srchName=".urlencode($data[srchName]));
	}

	function getDispNm($data)
	{
		return readurl("http://godointerpark.godo.co.kr/sock_getDispNm.php?dispno=".$data[dispno]);
	}
}

?>