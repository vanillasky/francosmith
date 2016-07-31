<?php
/**
* GODO License 체크 및 PG 정보등 기본정보를 조회한다.
*
*/
include dirname(__FILE__)."/../lib/library.php";

define("DEFINE_ITEMS", ";;");
define("DEFINE_ITEMVAL", "::");
define("DEFINE_QUERYRESULT", "[[[[");
define("DEFINE_RECORD", "]]]]"); 
define("DEFINE_FIELDS", "|||");
define("DEFINE_NAMEVAL", "$$$");

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
	var $_information;	// 모든 정보를 담아두는 곳.
	var $_godo;
	var $_dbconn;
	var $_db_ready ;
	// 주문조회용 페이징 처리를 위한 변수 
	var $_query_type ; 
	var $_max_order_count ; 
	var $_current_order_page ; 
	// 쿼리의 종류가 카운트 냐 리스트 냐 
	var $_get_order_count; 

	var $_search_date; 
	var $_query_result;		// 기본 
	var $_query_result_add;		// 추가: 주문아이템 정보

	function collect() {
		if ($_GET['debug'] == 'T') {
			$this->_is_log = true;
		} else {
			$this->_is_log = false;
		}
		
		$this->_dbconn = FALSE;
		$this->_db_ready = FALSE;

		$this->_max_order_count = 1000; 
		$this->_current_order_page = $_GET['status_page']; 
		if (intval($_GET['max_page_count'])) {
			$this->_max_order_count = intval($_GET['max_page_count']); 
			if ($this->_max_order_count > 1000) $this->_max_order_count = 1000; 
		}

		if (!$this->_current_order_page)  $this->_current_order_page = 1; 
		$this->_get_order_count = $_GET['status_cnt']; 
		// 주문조회 : o 
		// 취소조회 : c
		$this->_query_type = $_GET['query_type']; 
		if (!$this->_query_type)		$this->_query_type = 'o'; 

		$this->_search_date = $_GET['search_date'];
	}

	function check_serial() {
		### 고도 시리얼 체크
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
			### 고도 설정 화일 체크
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
			$this->setLog("db 연결 실패 $db_host $db_user : DB CONF FAILED");
			$this->setInformation("QUERYFAIL", "DB CONN NOT READY. CONN FAILED"); 
			return;
		}
		$bRc = mysql_select_db($db_name, $this->_dbconn);
		if ($bRc == FALSE) {
			$this->setLog("db name 변경 실패 $db_name : DB CONF FAILED");
			$this->setInformation("QUERYFAIL", "DB CONN NOT READY. DBNAME FAILED"); 
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

	function query_and_result($query)
	{
		if ($this->_db_ready == TRUE) {
			$result_set = mysql_query($query, $this->_dbconn);
			if ($result_set === FALSE) {
				$this->setlog("query error : " . mysql_error($this->_dbconn) );
				$this->setInformation("QUERYFAIL", mysql_error($this->_dbconn)); 
				return array();
			}
			else {
				$this->setInformation("QUERYFAIL", "SUCCESS");
				// 3. 결과 저장
				if (mysql_num_rows($result_set) == 0) {
					$this->setlog(" no rows found");
					return array();
				} else {
					$array_result = array();
					while ($row_data = mysql_fetch_assoc($result_set)) {
						$array_result[] = $row_data;
					}
					return $array_result;
				}
			}
		} else {
			$this->setInformation("QUERYFAIL", "DB CONN NOT READY"); 
			return array();
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
//		debug($msg);
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

		// 0. 홈디렉토리 체크
		$conf_filename = dirname(__FILE__)."/../conf/config.php";
		if (!is_file($conf_filename)) {
			$this->setlog('config.php 파일이 없습니다.');
			$this->setInformation("QUERYFAIL", "not exist config.php"); 
			return;
		}
		// config 파일 인그루드
		include $conf_filename;

		// PG정보 가져오기
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
			$this->setInformation("QUERYFAIL", "DB NO CONF"); 
		}
		else {
			include $db_conf_file;
			// DB연결하기 
			if (!$db_host || !$db_user || !$db_pass  || !$db_name) {
				$this->setInformation("QUERYFAIL", "DB CONF FAILED"); 
			} else {
				$this->db_connection($db_host, $db_user, $db_pass, $db_name);
			}
		}
		//########################
		if ($this->_search_date) {
			$dt_search_date = strtotime($this->_search_date);
			$search_date = date("Y-m-d", $dt_search_date); 
			if (strlen($search_date) != 10 || count(explode("-", $search_date)) != 3) {
				$search_date = date("Y-m-d", strtotime('-1 days')); 
				$next_date = date("Y-m-d"); 
			} else {
				$next_date = date("Y-m-d", strtotime('+1 days', $dt_search_date));
			}
		}
		else {
			$search_date = date("Y-m-d", strtotime('-1 days')); 
			$next_date = date("Y-m-d"); 
		}
//		debug("search_date = ".$search_date); 
//		debug("next_date = ".$next_date);

		// 1 주문리스트 조회 
		//
		// 주문건수가 일정수 이상이면 ? --> 천개 단위 페이징 처리 
		//
		if ($this->_query_type == 'o') {
			// 주문조회이면 

			if ($this->_get_order_count == 'T') {
				// 주문건수 조회를 한다. 
				$all_settle_query = " select count(*)  as cnt"; 
				$all_settle_query.= " from gd_order o ";
				$all_settle_query.= " where orddt >= '$search_date' ";
				$all_settle_query.= " and orddt < '$next_date' ";
			} else {
				$limit = $this->_max_order_count; 
				$offset = $this->_max_order_count * ($this->_current_order_page-1); 

				$all_settle_query = " select o.* ";
				$all_settle_query.= " from gd_order o ";
				$all_settle_query.= " where orddt >= '$search_date' ";
				$all_settle_query.= " and orddt < '$next_date' ";
				$all_settle_query.= " limit ".$limit." offset ".$offset; 

				$all_order_item_query = " select oi.*, orddate "; 
				$all_order_item_query.= " from gd_order_item oi join ";
				$all_order_item_query.= " (select ordno, substr(orddt,1,10) as orddate from gd_order where orddt >= '$search_date' and orddt < '$next_date' limit ".$limit." offset ".$offset." ) X on oi.ordno = X.ordno "; 

			}


		}
		else if ($this->_query_type == 'c') { 
			// 2. 취소리스트 조회
			if ($this->_get_order_count == 'T') {
				$all_settle_query = " select count(*) as cnt "; 
				$all_settle_query.= " from gd_order_cancel "; 
				$all_settle_query.= " where regdt >= '$search_date' "; 
				$all_settle_query.= " and regdt < '$next_date' "; 
			} else {
				$limit = $this->_max_order_count; 
				$offset = $this->_max_order_count * ($this->_current_order_page-1); 

				$all_settle_query = " select ordno, code, memo, name, regdt, rprice, rfee, remoney, rncash_emoney, rncash_cash"; 
				$all_settle_query.= " from gd_order_cancel "; 
				$all_settle_query.= " where regdt >= '$search_date' "; 
				$all_settle_query.= " and regdt < '$next_date' "; 
				$all_settle_query.= " limit ".$limit." offset ".$offset; 
			}
			$limit = $this->_max_order_count; 
			$offset = $this->_max_order_count * ($this->_current_order_page-1); 
		}
//		debug($all_settle_query);
		$this->_query_result = $this->query_and_result($all_settle_query); 
		if ($this->_query_type == 'o'  && $this->_get_order_count != 'T') {
			$this->_query_result_add = $this->query_and_result($all_order_item_query); 
		}

//		debug($query_result); 
		//########################
		$this->set_output();
		$this->db_close_connection(); 
	}

	function set_error_output($msg) 
	{
		$this->setInformation("MESSAGE", "not exist config.php");
		$this->echo_information(); 
	}

	function set_output() 
	{
		$this->setInformation("GODOSNO", $this->_godo['sno']);
		$this->setInformation("ECCODE", $this->_godo['ecCode']);
		$this->setInformation("SDATE", $this->_godo['sdate']);
		$this->setInformation("EDATE", $this->_godo['edate']);
		$output_string = 'SUCCESS::;;';

//		debug($this->_information);
		$this->echo_information(); 
	}

	function echo_information() 
	{
		// 
		foreach($this->_information as $k=>$v) {
			$output_string.= $k.DEFINE_ITEMVAL.$v.DEFINE_ITEMS;
		}
		//
		if (count($this->_query_result) >0) {
			//
			$suboutput_string = DEFINE_QUERYRESULT; 
			foreach($this->_query_result as $k => $v) {
				foreach($v as $sub_k => $sub_v) {
					$suboutput_string.=$sub_k; 
					$suboutput_string.=DEFINE_NAMEVAL;	//네임/밸류구분
					$suboutput_string.=$sub_v; 
					$suboutput_string.=DEFINE_FIELDS;	//필드구분
				}
				$suboutput_string.=DEFINE_RECORD;		//레코드구분
			}
			$suboutput_string.=DEFINE_QUERYRESULT;
			//
			$output_string.="QUERYRESULT::";
			$output_string.=$suboutput_string; 
			$output_string.=";;";
		}

		if (count($this->_query_result_add) >0) {
			//
			$suboutput_string = DEFINE_QUERYRESULT; 
			foreach($this->_query_result_add as $k => $v) {
				foreach($v as $sub_k => $sub_v) {
					$suboutput_string.=$sub_k; 
					$suboutput_string.=DEFINE_NAMEVAL;	//네임/밸류구분
					$suboutput_string.=$sub_v; 
					$suboutput_string.=DEFINE_FIELDS;	//필드구분
				}
				$suboutput_string.=DEFINE_RECORD;		//레코드구분
			}
			$suboutput_string.=DEFINE_QUERYRESULT;
			//
			$output_string.="QUERYRESULT_ADD::";
			$output_string.=$suboutput_string; 
			$output_string.=";;";
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
