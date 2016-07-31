<?php
/*
 * payco CLASS
 *
 * @author paycoConfig.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-06-09
 */
class paycoConfig {

	var $paycoCfgFile, $saveEnvData, $paycoCfg;

	function paycoConfig()
	{
		global $paycoCfg;

		if(!$paycoCfg && is_file(dirname(__FILE__) . '/../conf/payco.cfg.php')){
			@include dirname(__FILE__) . '/../conf/payco.cfg.php';
		}

		$this->paycoCfgFile = dirname(__FILE__) . '/../conf/payco.cfg.php';
		$this->paycoCfg = $paycoCfg;
	}

	/*
	* 페이코 설정파일 저장
	* @param array  저장데이터
	* @return
	* @date 2015-06-09
	*/
	function savePaycoConfigFile($data)
	{
		global $qfile;

		if(!$qfile && is_file(dirname(__FILE__) . '/../lib/qfile.class.php')){
			@include_once(dirname(__FILE__) . '/../lib/qfile.class.php');
		}
		
		$qfile = new qfile();
		$qfile->open($this->paycoCfgFile);
		$qfile->write("<?php \n");
		$qfile->write("\$paycoCfg = array( \n");
		foreach ($data as $k=>$v)
		{
			if(is_array($v)):
				$qfile->write("'$k' => array(");
				foreach ($v as $k1=>$v1) $qfile->write("'".trim($v1)."',");
				$qfile->write("), \n");
			else:
				$qfile->write("'$k' => '".trim($v)."', \n");
			endif;
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod($this->paycoCfgFile, 0707);
	}

	/*
	* gd_env data -> config 파일로 생성
	* @param
	* @return
	* @date 2015-06-09
	*/
	function restorePaycoBackupData()
	{
		//payco.cfg.php 파일존재여부 확인
		if(!is_file($this->paycoCfgFile)){
			$paycoEnvData	= array();
			$paycoEnvData	= Core::loader('config')->load('payco');

			if($paycoEnvData['crypt_key'] && $paycoEnvData['paycoSellerKey'] && $paycoEnvData['paycoCpId'] && $paycoEnvData['testYn'] && $paycoEnvData['useType']){
				//create payco config file
				$this->savePaycoConfigFile($paycoEnvData);
			}
		}
	}

	/*
	* gd_env data insert
	* @param array - 백업될 post data
	* @return
	* @date 2015-06-09
	*/
	function saveEnvData($postData)
	{
		global $config;

		if(!$config){
			$config = Core::loader('config');
		}

		//저장될 데이터 셋팅
		$this->setEnvData($postData);

		//gd_env save
		if($this->saveEnvData['crypt_key'] && $this->saveEnvData['paycoSellerKey'] && $this->saveEnvData['paycoCpId'] && $this->saveEnvData['testYn'] && $this->saveEnvData['useType']){
			$config->save('payco', $this->saveEnvData);
		}
	}

	/*
	* gd_env 에 저장될 데이터 셋팅
	* @param array - post data
	* @return
	* @date 2015-06-09
	*/
	function setEnvData($postData)
	{
		if($postData['crypt_key'] || $this->paycoCfg['crypt_key'])	{
			$this->saveEnvData['crypt_key'] = ($postData['crypt_key']) ? $postData['crypt_key'] : $this->paycoCfg['crypt_key'];
		}
		if($postData['paycoSellerKey'] || $this->paycoCfg['paycoSellerKey']) {
			$this->saveEnvData['paycoSellerKey'] = ($postData['paycoSellerKey']) ? $postData['paycoSellerKey'] : $this->paycoCfg['paycoSellerKey'];
		}
		if($postData['paycoCpId'] || $this->paycoCfg['paycoCpId'])	{
			$this->saveEnvData['paycoCpId'] = ($postData['paycoCpId']) ? $postData['paycoCpId'] : $this->paycoCfg['paycoCpId'];
		}
		if($postData['testYn'] || $this->paycoCfg['testYn'])	{
			$this->saveEnvData['testYn'] = ($postData['testYn']) ? $postData['testYn'] : $this->paycoCfg['testYn'];
		}
		if($postData['useType'] || $this->paycoCfg['useType'])	{
			$this->saveEnvData['useType'] = ($postData['useType']) ? $postData['useType'] : $this->paycoCfg['useType'];
		}
	}
}
?>