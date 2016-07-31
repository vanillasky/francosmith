<?php
/** 
* GODO License .. . PG ... ..... ..... 
* 
*/ 
include dirname(__FILE__)."/../lib/library.php";

define('CRYPT_XXTEA_DELTA', 0x9E3779B9);

class clsDataSecurity {
	var $_key;

	function setKey($key) {
        if (is_string($key)) {
            $k = $this->_str2long($key, false);
        } elseif (is_array($key)) {
            $k = $key;
        } else {
            exit('The secret key must be a string or long integer array.');
        }
        if (count($k) > 4) {
            exit('The secret key cannot be more than 16 characters or 4 long values.');
        } elseif (count($k) == 0) {
            exit('The secret key cannot be empty.');
        } elseif (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) {
                $k[$i] = 0;
            }
        }
        $this->_key = $k;
        return true;
    }

    function encrypt($plaintext) {
        if ($this->_key == null) {
            exit('Secret key is undefined.');
        }
        if (is_string($plaintext)) {
			$encrypt_data = $this->_encryptString($plaintext);
			$encrypt_data = rawurlencode($encrypt_data);
			$encrypt_data = base64_encode($encrypt_data);
            return $encrypt_data;
        } elseif (is_array($plaintext)) {
			$encrypt_data = $this->_encryptArray($plaintext);
			$encrypt_data = rawurlencode($encrypt_data);
			$encrypt_data = base64_encode($encrypt_data);
			return $encrypt_data;
        } else {
            exit('The plain text must be a string or long integer array.');
        }
    }

    function decrypt($chipertext) {
        if ($this->_key == null) {
            exit('Secret key is undefined.');
        }
		$chipertext = base64_decode($chipertext);
		$chipertext = rawurldecode($chipertext);
        if (is_string($chipertext)) {
            return $this->_decryptString($chipertext);
        } elseif (is_array($chipertext)) {
            return $this->_decryptArray($chipertext);
        } else {
            exit('The chiper text must be a string or long integer array.');
        }
    }

    function _encryptString($str) {
        if ($str == '') {
            return '';
        }
        $v = $this->_str2long($str, true);
        $v = $this->_encryptArray($v);
        return $this->_long2str($v, false);
    }

    function _encryptArray($v) {
        $n = count($v) - 1;
        $z = $v[$n];
        $y = $v[0];
        $q = floor(6 + 52 / ($n + 1));
        $sum = 0;
        while (0 < $q--) {
            $sum = $this->_int32($sum + CRYPT_XXTEA_DELTA);
            $e = $sum >> 2 & 3;
            for ($p = 0; $p < $n; $p++) {
                $y = $v[$p + 1];
                $mx = $this->_int32((($z >> 5 & 0x07FFFFFF) ^ $y << 2) + (($y >> 3 & 0x1FFFFFFF) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($this->_key[$p & 3 ^ $e] ^ $z));
                $z = $v[$p] = $this->_int32($v[$p] + $mx);
            }
            $y = $v[0];
            $mx = $this->_int32((($z >> 5 & 0x07FFFFFF) ^ $y << 2) + (($y >> 3 & 0x1FFFFFFF) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($this->_key[$p & 3 ^ $e] ^ $z));
            $z = $v[$n] = $this->_int32($v[$n] + $mx);
        }
        return $v;
    }

    function _decryptString($str) {
        if ($str == '') {
            return '';
        }
        $v = $this->_str2long($str, false);
        $v = $this->_decryptArray($v);
        return $this->_long2str($v, true);
    }

	function _decryptArray($v) {
        $n = count($v) - 1;
        $z = $v[$n];
        $y = $v[0];
        $q = floor(6 + 52 / ($n + 1));
        $sum = $this->_int32($q * CRYPT_XXTEA_DELTA);
        while ($sum != 0) {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--) {
                $z = $v[$p - 1];
                $mx = $this->_int32((($z >> 5 & 0x07FFFFFF) ^ $y << 2) + (($y >> 3 & 0x1FFFFFFF) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($this->_key[$p & 3 ^ $e] ^ $z));
                $y = $v[$p] = $this->_int32($v[$p] - $mx);
            }
            $z = $v[$n];
            $mx = $this->_int32((($z >> 5 & 0x07FFFFFF) ^ $y << 2) + (($y >> 3 & 0x1FFFFFFF) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($this->_key[$p & 3 ^ $e] ^ $z));
            $y = $v[0] = $this->_int32($v[0] - $mx);
            $sum = $this->_int32($sum - CRYPT_XXTEA_DELTA);
        }
        return $v;
    }

    function _long2str($v, $w) {
        $len = count($v);
        $s = '';
        for ($i = 0; $i < $len; $i++) {
            $s .= pack('V', $v[$i]);
        }
        if ($w) {
            return substr($s, 0, $v[$len - 1]);
        } else {
            return $s;
        }
    }

    function _str2long($s, $w) {
        $v = array_values(unpack('V*', $s.str_repeat("\0", (4-strlen($s)%4)&3)));
        if ($w) {
            $v[] = strlen($s);
        }
        return $v;
    }

    function _int32($n) {
        while ($n >= 2147483648) $n -= 4294967296;
        while ($n <= -2147483649) $n += 4294967296;
        return (int)$n;
    }
}



class collect{
	var $ip;
	var $target;
	var $result_filename;

	var $serial_result_code ; 
	var $godocfg_result_code ; 
	var $_is_log; 
	var $_information;	// .. ... .... .. 
	var $_godo; 
	var $_dbconn; 
	var $_db_ready ; 

	function collect() {
		if ($_GET['debug'] == 'T') {
			$this->_is_log = true; 
		} else {
			$this->_is_log = false; 
		}
	}

	function check_serial() {
		### .. ... ..
		$file	= dirname(__FILE__)."/../conf/serial.cfg.php";
		if (!is_file($file)) {
			$this->serial_result_code = "SERIAL_ERROR_NOTEXIST"; 
			$this->godocfg_result_code = "GODOCFG_ERROR";
		}
		else {
			$file	= file($file);
			if (serial('godo!@#')!=trim($file[1])) {
				$this->serial_result_code = "SERIAL_ERROR_BAD"; 
			} else {
				$this->serial_result_code = "SERIAL_SUCCESS"; 
			}
			### .. .. .. ..
			$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
			if (!is_file($file)) {
				$this->godocfg_result_code = "GODOCFG_ERROR_NOTEXIST"; 
			} else {
				$this->godocfg_result_code = "GODOCFG_SUCCESS";
			}
			$file	= file($file);
			$godo	= decode($file[1],1);
		}
		// 
		$this->setInformation("serial_result", $this->serial_result_code); 
		$this->setInformation("godocfg_result", $this->godocfg_result_code); 
		$this->_godo = $godo; 
	}

	/**
		establish db connection and set value 
		: $this->_dbconn 
		: $this->_db_ready 
	*/ 
	function db_connection($db_host, $db_user, $db_pass, $db_name) 
	{
		$this->_dbconn = FALSE; 
		$this->_db_ready = FALSE; 

		$this->_dbconn = mysql_connect($db_host, $db_user, $db_pass);
		if ($this->_dbconn == FALSE) {
			$this->setLog($dir.". db .. .. $db_host $db_user : DB CONF FAILED");
			return; 
		}
		$bRc = mysql_select_db($db_name, $this->_dbconn);
		if ($bRc == FALSE) {
			$this->setLog($dir.". db .. .. $db_name : DB CONF FAILED");
		} else {
			$this->_db_ready = TRUE; 
		}
	}

	function db_close_connection() 
	{
		if ($this->_dbconn != FALSE) {
			mysql_close($this->_dbconn);
		}
	}

	function query_and_result($dbconn, $query, $dir)
	{
		$result_set = mysql_query($query, $dbconn);
		if ($result_set === false) {
			$this->setlog( $dir." query error : " . mysql_error($dbconn) );
			return array();
		}
		else {
			$this->setlog( $dir." query complete ");

			// 3. .. ..
			if (mysql_num_rows($result_set) == 0) {
				$this->setlog( $dir." no rows found");
				return array();
			} else {
				$array_result = array();
				while ($row_data = mysql_fetch_assoc($result_set)) {
					$array_result[] = $row_data;
				}
				return $array_result;
			}
		}
	}

	/** 
	*/
	function setInformation($name, $value) 
	{
		$this->_information[$name] = $value; 
	}

	/**
	*/
	function setlog($msg) 
	{
		if ($this->_is_log == true) {
			$fp = fopen('_godo_check'.date('Ymd').'.log', 'a');
			fwrite($fp, date('Y-m-d H:i:s')." ".$msg. chr(10) );
			fclose($fp);
		}
	}


	/*
	 *
	 */
	function _get_information() {
		$this->_information = array(); 
		$this->check_serial(); 

		// Work Start
		$log = '==> start'.chr(10); 
		$this->setlog($log);

		// 0. ..... ..
		$conf_filename = dirname(__FILE__)."/../conf/config.php";
		if (!is_file($conf_filename)) {
			$this->setlog('config.php ... .....');
			$this->setInformation("MESSAGE", "not exist config.php");
			$this->set_output(); 
			return; 
		}
		// config .. ....
		include $conf_filename;

		// PG.. ....
		$shopUrl = $cfg['shopUrl'];
		$pg_info = $cfg['settlePg'];
		$pg_mid  = 'nopg';
		if (!$pg_info) $pg_info = 'nopg';
		if ($pg_info != 'nopg' && is_file(dirname(__FILE__)."/../conf/pg.".$pg_info.".php") ) {
			include dirname(__FILE__)."/../conf/pg.".$pg_info.".php";
			$pg_mid = $pg['id'];
			unset($pg);
		} else {
			$pg_mid = 'nopg'; 
			$this->setlog("no pg");
		}

		//########################
		$this->setInformation("SERVER_NAME", $_SERVER['SERVER_NAME']); 
		$this->setInformation("SERVER_ADDR", $_SERVER['SERVER_ADDR']); 
		$this->setInformation("SHOP_URL", $shopUrl); 
		$this->setInformation("PG_COMPANY", $pg_info); 
		$this->setInformation("PG_MID", $pg_mid); 
		$this->setInformation("SHOP_SKIN", $cfg['tplSkin']); 
		//########################

		unset($cfg);
		unset($db_host);
		unset($db_user);
		unset($db_pass);
		unset($db_name);

		$db_conf_file = dirname(__FILE__)."/../conf/db.conf.php";
		if (!is_file($db_conf_file)) {
			$log = "no db.conf.php";
			$this->setlog($log);
		}
		else {
			include $db_conf_file;
			// DB....
			if (!$db_host || !$db_user || !$db_pass  || !$db_name) {
				$this->setLog("db ..... ...... : DB CONF FAILED");
			} else {
				$this->db_connection($db_host, $db_user, $db_pass, $db_name); 
			}
		}
		//########################
		$this->setInformation("DB_HOST", $db_host); 
		//########################

		// 1..... .... .. 
		// config.mobileShop.php 
		$conf_mobileshop = dirname(__FILE__)."/../conf/config.mobileShop.php"; 
		if (!is_file($conf_mobileshop)) {
			$mb_useMobileShop = '0'; 
			$mb_mobileShopRootDir = ''; 
			$mb_mobileSkin = ''; 
		}
		else {
			include $conf_mobileshop ; 
			$mb_useMobileShop = $cfgMobileShop['useMobileShop']; 
			$mb_mobileShopRootDir = $cfgMobileShop['mobileShopRootDir']; 
			$mb_mobileSkin = $cfgMobileShop['tplSkinMobile']; 
		}
		//########################
		$this->setInformation("USE_MOBILE", $mb_useMobileShop); 
		$this->setInformation("MOBILE_ROOT", $mb_mobileShopRootDir); 
		$this->setInformation("MOBILE_SKIN", $mb_mobileSkin); 
		//########################

		//... PG.. 
		$mobile_pg_mid = "nopg"; 
		$mobile_pg_confname = dirname(__FILE__)."/../conf/pg_mobile.".$pg_info.".php"; 
		if ($pg_info != 'nopg' && is_file($mobile_pg_confname) ) {
			@include $mobile_pg_confname;
			$mobile_pg_mid = $pg_mobile['id'];
			unset($pg_mobile);
		} else {
			$this->setlog($dir." no pg");
		}
		//########################
		$this->setInformation("MOBILE_PG_MID", $mobile_pg_mid); 
		//########################

		// ..... .... .. 
		// /conf/pg_cell.mobilians.cfg.php
		$conf_mobilians = dirname(__FILE__)."/../conf/pg_cell.mobilians.cfg.php"; 
		if (!is_file($conf_mobilians)) {
			//... ... 
			$mb_pgUse = 'no'; 
			$mb_pgID = ''; 
		} 
		else {
			include $conf_mobilians ; 
			$mb_pgUse = $mobiliansCfg['serviceType']; 
			$mb_pgID = $mobiliansCfg['serviceId']; 
		}
		//########################
		$this->setInformation("MOBILIANS_PG_USE", $mb_pgUse); 
		$this->setInformation("MOBILIANS_PG_MID", $mb_pgID); 
		//########################
		$this->set_output(); 
		$this->db_close_connection(); 
	}

	function set_output() {
		$this->setInformation("GODOSNO", $this->_godo['sno']); 
		$this->setInformation("ECCODE", $this->_godo['ecCode']);
		$this->setInformation("SDATE", $this->_godo['sdate']); 
		$this->setInformation("EDATE", $this->_godo['edate']); 

		$output_string = ''; 
		foreach($this->_information as $k=>$v) {
			$output_string.= $k."::".$v.";;";
		}
		//암호화 
		$ins_secure = new clsDataSecurity(); 
		$ins_secure->setKey("20201000");
		$encrypted_output = $ins_secure->encrypt($output_string);
		//
		echo $encrypted_output; 
	}
}


//---------------------------------------------------------------
// Request Validation 

/* 
	인증을 위한 해시값을 만든다. 
*/ 
function make_auth_hash() 
{
	// 해시값 만들기 
	$auth_hash_val = sha1(date("Ymd")."polling_godosoft"); 
	return $auth_hash_val; 
}

$error_message = ""; 
if ($_GET['auth_hash']) {
	$auth_hash = make_auth_hash(); 
	if ($_GET['auth_hash'] != $auth_hash) {
		$ins_secure = new clsDataSecurity(); 
		$ins_secure->setKey("20201000");
		$encrypted_output = $ins_secure->encrypt("not proper hash");
		echo $encrypted_output; 
		exit; 
	} 
}
else {
	$ins_secure = new clsDataSecurity(); 
	$ins_secure->setKey("20201000");
	$encrypted_output = $ins_secure->encrypt("not accepted");
	echo $encrypted_output; 
	exit; 
}

//---------------------------------------------------------------

$collect = new collect();
$collect->_get_information();
?>

