<?
/*********************************************************
* 파일명     :  lib/sAPI.class.php
* 프로그램명 :  sAPI 전용 클래스 파일
* 작성자     :  dn
* 생성일     :  2012.05.09
**********************************************************/
if(!defined('API_PROTOCOL')) define('API_PROTOCOL', 'http://');
if(!defined('API_PATH')) define('API_PATH', '/enamooAPI/');
if(!defined('GET_CODE')) define('GET_CODE', 'STGetCode.gm');
if(!defined('GET_SET_LIST')) define('GET_SET_LIST', 'STGetSetList.gm');
if(!defined('MALL_CATEGORY')) define('MALL_CATEGORY', 'STGetMallCategory.gm');
if(!defined('GET_MALL_LOGIN_ID')) define('GET_MALL_LOGIN_ID', 'STGetMallLoginId.gm');
if(!defined('SCRAP_ORDER')) define('SCRAP_ORDER', 'STScrapOrder.gm');
if(!defined('LINK_GOODS')) define('LINK_GOODS', 'STLinkGoods.gm');
if(!defined('GET_LOGINID')) define('GET_LOGINID', 'STGetLoginId.gm');
if(!defined('DEL_SET_INFO')) define('DEL_SET_INFO', 'STDelSetInfo.gm');
if(!defined('LINK_MODIFY_GOODS')) define('LINK_MODIFY_GOODS', 'STLinkModifyGoods.gm');
if(!defined('GET_MALL_GOODS_URL')) define('GET_MALL_GOODS_URL', 'STGetMallGoodsUrl.gm');
if(!defined('LINK_GOODS_STATUS')) define('LINK_GOODS_STATUS', 'STLinkGoodsStatus.gm');
if(!defined('SEND_ORDER')) define('SEND_ORDER', 'STSendOrder.gm');
if(!defined('LINK_GOODS')) define('LINK_GOODS', 'STLinkGoods.gm');
if(!defined('SET_DELIVERY_INFO')) define('SET_DELIVERY_INFO', 'STSetDeliveryInfo.gm');
if(!defined('SET_EXCHANGE_DELIVERY_INFO')) define('SET_EXCHANGE_DELIVERY_INFO', 'STSetExchangeDeliveryInfo.gm');
if(!defined('GET_MALL_GOODS_EXTEND')) define('GET_MALL_GOODS_EXTEND', 'STGetMallGoodsExtend.gm');
if(!defined('LINK_GOODS_EXTEND')) define('LINK_GOODS_EXTEND', 'STLinkGoodsExtend.gm');
if(!defined('CHECK_MALL_LOGIN')) define('CHECK_MALL_LOGIN', 'STCheckMallLogin.gm');
if(!defined('INS_MALL')) define('INS_MALL', 'STInsMall.gm');
if(!defined('GET_MALL_LIST')) define('GET_MALL_LIST', 'STGetMallList.gm');
if(!defined('GET_MALL_INFO')) define('GET_MALL_INFO', 'STGetMallInfo.gm');
if(!defined('DELETE_MALL')) define('DELETE_MALL', 'STDeleteMall.gm');

class sAPI {

	var $db;
	var $cust_cd;
	var $cust_seq;
	var $xxtea;

	function sAPI()	{

		$this->xxtea = Core::loader('xxtea');

		include_once ('xmlWriter.class.php');
		include_once ('parsexmlstruc.class.php');

		$db = &$GLOBALS['db'];
		
		$domain_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
		$domain_res = $db->_select($domain_query);
		$selly_domain = (string)$domain_res[0]['value'];

		if(!defined('API_DOMAIN')) define('API_DOMAIN', $selly_domain);

		$cust_cd_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');

		$cust_cd_res = $db->_select($cust_cd_query);
		$this->cust_cd = $cust_cd_res[0]['value'];

		$cust_seq_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_seq');
		$cust_seq_res = $db->_select($cust_seq_query);
		$this->cust_seq = $cust_seq_res[0]['value'];

	}

	function xcryptare($text, $cryptKey, $crypt) { 
		
		if(!$crypt) {
			$text = $text;
		}

		$this->xxtea->setKey($cryptKey);

		if($crypt) {
			$ret_text = $this->xxtea->encrypt($text);
		}
		else {
			$ret_text = $this->xxtea->decrypt($text);
		}

		return $ret_text;
	}

	function createXmlData(&$arr) {
		$xml = new XmlWriter_py();
		$this->arrayToXml($xml, 'data', $arr);
		$rtn_value = $xml->getXml();
		unset($xml);
		return $rtn_value;
	}

	function arrayToXml(&$xml, $key, $val) {
		if (is_array($val)) {
			$xml->push($key);
			foreach($val as $k => $v) {
				$this->arrayToXml($xml, $k, $v);
			}
			$xml->pop();
		}
		else $xml->element($key, $val);		
	}

	function curlFunc($url, $postdata='') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 90);
		$ret_val = curl_exec($ch);

		curl_close($ch);
		return str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version="1.0" encoding="utf-8"?>', $ret_val);
	}
	
	function getXmlData($mode, $arr_data, $arr_sort='') {

		if (!defined('API_DOMAIN') || API_DOMAIN == '') return;

		$mode = strtoupper($mode);
		$arr['header'] = array('mode' => $mode, 'cust_cd' => $this->cust_cd, 'cust_seq' => $this->cust_seq);
		
		if (!$arr_data) $arr_data = array();
		$arr[strtolower($mode).'_data'] = $arr_data;

		if(empty($arr_sort) === false) {
			$arr['sort']=$arr_sort;
		}

		$data['xml_data'] = $this->createXmlData($arr);	
		switch($mode) {
			case 'GETSETLIST' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_SET_LIST;
				break;
			case 'GETCODE' : 
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_CODE;
				break;
			case 'MALLCATEGORY' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.MALL_CATEGORY;
				break;
			case 'GETMALLLOGINID' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_MALL_LOGIN_ID;
				break;
			case 'SCRAPORDER' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.SCRAP_ORDER;
				break;
			case 'LINKGOODS' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.LINK_GOODS;
				break;
			case 'GETLOGINID' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_LOGINID;
				break;
			case 'DELSETINFO' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.DEL_SET_INFO;
				break;
			case 'LINKMODIFYGOODS' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.LINK_MODIFY_GOODS;
				break;
			case 'GETMALLGOODSURL' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_MALL_GOODS_URL;
				break;
			case 'LINKGOODSSTATUS' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.LINK_GOODS_STATUS;
				break;
			case 'SENDORDER' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.SEND_ORDER;
				break;
			case 'SETDELIVERYINFO' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.SET_DELIVERY_INFO;
				break;
			case 'SETEXCHANGEDELIVERYINFO' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.SET_EXCHANGE_DELIVERY_INFO;
				break;
			case 'GETMALLGOODSEXTEND' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_MALL_GOODS_EXTEND;
				break;
			case 'LINKGOODSEXTEND' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.LINK_GOODS_EXTEND;
				break;
			case 'CHECKMALLLOGIN' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.CHECK_MALL_LOGIN;
				break;
			case 'INSMALL' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.INS_MALL;
				break;
			case 'GETMALLLIST' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_MALL_LIST;
				break;
			case 'GETMALLINFO' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.GET_MALL_INFO;
				break;
			case 'DELETEMALL' :
				$api_url = API_PROTOCOL.API_DOMAIN.API_PATH.DELETE_MALL;
				break;
		}

		$xml_data = $this->curlFunc($api_url, $data);
		$parser = new StrucXMLParser();
		$parser->parse(trim($xml_data));
		$rtn_value = $parser->parseOut();
		unset($parser);

		$rtn_value = $this->convertEncodeArr($rtn_value, 'utf-8', 'euc-kr');
		return $rtn_value;
	}

	function convertEncodeArr($cvt_data, $in_charset, $out_charset) {
		$tmp_data;
		if(!empty($cvt_data) && is_array($cvt_data)) {
			
			foreach($cvt_data as $key => $val) {

				if(is_array($val)) {
					
					$tmp_data[strToLower(iconv($in_charset, $out_charset, $key))] = $this->convertEncodeArr($val, $in_charset, $out_charset);
				}
				else {
					$tmp_data[strToLower(iconv($in_charset, $out_charset, $key))] = iconv($in_charset, $out_charset, $val);
				}
			}
		}
		else {
			if($cvt_data) {
				$tmp_data = iconv($in_charset, $out_charset, $cvt_data);
			}
		}

		return $tmp_data;
	}
	
	function createRowData($ret_arr) {

		$ret_header = $ret_arr['data'][0]['child']['header'][0]['child'];
		$ret_item = $ret_arr['data'][0]['child']['return'][0]['child']['item'];

		$ret = array();

		if($ret_header['code'][0]['data'] == '000') {
			if(is_array($ret_item) && !empty($ret_item)) {
				foreach($ret_item as $row_item) {
					$tmp_item[] = $this->createArrData($row_item);	
				}
			}
			$ret = $tmp_item;
		}
		else {
			$ret['code'] = $ret_header['code'][0]['data'];
			$ret['msg'] = $ret_header['msg'][0]['data'];
		}

		return $ret;
	}

	function createRetData($ret_arr) {

		$ret_header = $ret_arr['data'][0]['child']['header'][0]['child'];
		
		$ret['code'] = $ret_header['code'][0]['data'];
		$ret['msg'] = $ret_header['msg'][0]['data'];
		
		return $ret;

		
	}

	function createArrData($arr) {
		if(is_array($arr['child']) && !empty($arr['child'])) {
			$arr = $arr['child'];

			foreach($arr as $key => $val) {
				if(is_array($val) && !empty($val)) {
					$arr[$key] = $this->createArrData($val);
				}
			}
		}
		else {
			$arr = $arr['0']['data'];
		}
		return $arr;
	}

	function getSetList($arr = Array()) {
		$ret = $this->createRowData($this->getXmlData('getsetlist', $arr));
		return $ret;
	}

	function getCode($arr = Array(), $ret_type='') {
		$ret = $this->getXmlData('getcode', $arr);

		if($ret_type == 'hash') {
			$ret_item = $ret['data'][0]['child']['return'][0]['child']['item'];
			if(is_array($ret_item) && !empty($ret_item)) {
				foreach($ret_item as $row_item) {
					$tmp_item[] = $this->createArrData($row_item);				
				}

				switch($arr['grp_cd']) {
					case 'MALL_GOODS_URL' :
						foreach($tmp_item as $tmp_row) {
							$ret_code[$tmp_row['mall_cd']] = $tmp_row['url'];
						}
						break;
					default :
						foreach($tmp_item as $tmp_row) {
							$ret_code[$tmp_row['com_cd']] = $tmp_row['com_nm'];
						}
						break;
				}
			}
		}
		else {
			$ret_code = $ret;
		}
		return $ret_code;
	}

	function getMallCategory($arr = Array()) {
		$ret = $this->getXmlData('mallcategory', $arr);
		return $ret;
	}

	function getMallLoginId($arr=Array(), $ret_type='') {
		$ret = $this->createRowData($this->getXmlData('getmallloginid', $arr));
		
		$ret_id = array();
		if($ret_type == 'hash') {
			if(is_array($ret) && !empty($ret)) {
				foreach($ret as $row_ret) {
					$ret_id[$row_ret['minfo_idx']] = $row_ret;
				}
			}
		}
		else {
			$ret_id = $ret;
		}
		return $ret_id;
	}

	function scrapOrder($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('scraporder', $arr));
		return $ret;
	}

	function linkGoods($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('linkgoods', $arr));
		return $ret;
	}

	function getLoginId($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('getloginid', $arr));
		return $ret;
	}

	function delSetInfo($arr=array()) {
		$ret = $this->createRetData($this->getXmlData('delsetinfo', $arr));
		return $ret;
	}

	function linkModifyGoods($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('linkmodifygoods', $arr));
		return $ret;
	}

	function getMallGoodsUrl($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('getmallgoodsurl', $arr));
		return $ret;
	}

	function linkGoodsStatus($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('linkgoodsstatus', $arr));
		return $ret;
	}

	function sendOrder($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('sendorder', $arr));
		return $ret;
	}

	function setDeliveryInfo($arr=array()) {
		$ret = $this->createRetData($this->getXmlData('setdeliveryinfo', $arr));
		return $ret;
	}
	function setExchangeDeliveryInfo($arr=array()) {
		$ret = $this->createRetData($this->getXmlData('setexchangedeliveryinfo', $arr));
		return $ret;
	}
	function getMallGoodsExtend($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('getmallgoodsextend', $arr));
		return $ret;
	}
	function linkGoodsExtend($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('linkgoodsextend', $arr));
		return $ret;
	}
	function checkMallLogin($arr=array()) {
		$ret = $this->createRetData($this->getXmlData('checkmalllogin', $arr));
		return $ret;
	}
	function insMall($arr=array()) {
		$ret = $this->createRetData($this->getXmlData('insmall', $arr));
		return $ret;
	}
	function getMallList($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('getmalllist', $arr));
		return $ret;
	}
	function getMallInfo($arr=array()) {
		$ret = $this->createRowData($this->getXmlData('getmallinfo', $arr));
		return $ret;
	}
	function deleteMall($arr=array()) {
		$ret = $this->createRetData($this->getXmlData('deletemall', $arr));
		return $ret;
	}

	function exec_page($total, $now_page, $param_data) {//total = 데이터개수, now_page = 현재 페이지
		if($now_page > 0) {
			$prev_page_btn = "◀";
			$next_page_btn = "▶";
			$total_page = @ceil($total/10);

			if($param_data) {
				$param .= '&'.$param_data;
			}

			if ($total_page && $now_page > $total_page) $now_page = $total_page;
			$start_page = (ceil($now_page/10)-1)*10;
			if($now_page>10){
				$navi .= '<a href="'.$_SERVER['PHP_SELF'].'?page=1'.$param.'" class=navi>[1]</a>';
				$navi .= ' <a href="'.$_SERVER['PHP_SELF'].'?page='.$start_page.$param.'" class=navi>'.$prev_page_btn.'</a>';
			}

			while($i+$start_page < $total_page && $i < 10){
				$i++;
				$move_page = $i+$start_page;
				$navi .= ($now_page == $move_page) ? ' <b>'.$move_page.'</b> ' : ' <a href="'.$_SERVER['PHP_SELF'].'?&page='.$move_page.$param.'" class=navi>['.$move_page.']</a> ';
			}

			if($total_page > $move_page){
				$next_page = $move_page+1;
				$navi .= '<a href="'.$_SERVER['PHP_SELF'].'?page='.$next_page.$param.'" class=navi>'.$next_page_btn.'</a>';
				$navi .= ' <a href="'.$_SERVER['PHP_SELF'].'?page='.$total_page.$param.'" class=navi>['.$total_page.']</a>';
			}
			return $navi;
		}
	}
}

?>
