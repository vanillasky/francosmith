<?php
/*
	환경설정값 클래스

	기존에 파일로 된 환경설정파일과 DB로 구성된 환경설정 부분을 통합해서 관리합니다
*/
class config {

	var $_loaded=array();
	var $_db_map=array(
		'godopost'=>array('compdivcd','webpost_id','webpost_pass'),
		'godofont'=>array('major_font'),
		'aboutcoupon'=>array('use_aboutcoupon','use_test','left_loc','top_loc','startdate','enddate'),
		'godotax'=>array('site_id','api_key'),
		'bgm'=>array('urlFix','use','file','volume','loof'),
		'checkoutapi'=>array('cryptkey','linkStock','integrateOrder'),
		'shoppingApp'=>array('useApp','orderby','e_exceptions','app_premium','app_premium2'),
		'ncash'=>array('useyn','api_id','api_key','save_mode','e_exceptions','e_category','baseAccumRate','addAccumRate','RateDate','status','mobileStatus','exceptionyn'),
		'mobilians' => array('merchantId', 'serviceId', 'serviceType', 'pg-centersetting'),
		'danal' => array('S_CPID', 'M_CPID', 'servicePwd', 'serviceItemCode', 'serviceType', 'pg-centersetting'),
		'hpauth' => array('serviceCode', 'serviceName'),
		'hpauthMcerti' => array('cpid', 'useyn', 'modyn', 'moduseyn', 'minoryn'),
		'hpauthDream' => array('cpid', 'useyn', 'modyn', 'moduseyn', 'minoryn'),
		'myicon'=>array('myicon'),
		'payco'=>array('crypt_key','paycoSellerKey','paycoCpId', 'testYn', 'useType'),
		'dormantConfig'=>array('use', 'agreeDate', 'checkDormantDate', 'checkDormantMailDate', 'checkDormantSmsDate'),
	);
	var $enable_keys = array();
	function load($name) {
		if(!array_key_exists($name,$this->_loaded)) {
			switch($name) {
				case 'godo': $this->_loaded['godo']=$this->_load_godo(); 	break;
				case 'config': $this->_loaded['config']=$this->_load_config(); 	break;
				case 'configpay': $this->_loaded['configpay']=$this->_load_configpay(); 	break;
				default:
					$this->_loaded[$name]=$this->_load($name);
					break;
			}
		}
		return $this->_loaded[$name];
	}

	function _load_godo() {
		$file = SHOPROOT."/conf/godomall.cfg.php";
		if (is_file($file)){
			$file = file($file);
			$godo = decode($file[1],1);
		}
		return $godo;
	}

	function _load_config() {
		include(SHOPROOT."/conf/config.php");
		return $cfg;
	}

	function _load_configpay() {
		include(SHOPROOT."/conf/config.pay.php");
		return $set;
	}

	function _load($name) {
		$db = Core::loader('db');
		$query = $db->_query_print('select name,value from gd_env where category=[s]',$name);
		$result = $db->_select($query);
		$ar_return=array();
		foreach($result as $v) {
			$ar_return[$v['name']]=$v['value'];
		}
		return $ar_return;
	}

	function save($name,$ar_data) {
		if($this->_db_map[$name]) {
			array_diff_unset($ar_data,$this->_db_map[$name]);
		}
		elseif(count($this->enable_keys)) {
			array_diff_unset($ar_data,$this->enable_keys);
		}

	    $ar_insert=array();
	    foreach($ar_data as $k=>$v) {
			$ar_insert[]=array($name,$k,$v);
	    }

	    $db = Core::loader('db');
	    $query = $db->_query_print('replace into gd_env values [vs]',$ar_insert);
	    $db->query($query);
	}

}

?>
