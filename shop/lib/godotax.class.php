<?php

class godotax {

	var $server = 'www.godobill.com';
	var $encodeKey = 'dusqhdtkdtmd';

	function godotax() {
		include_once(SHOPROOT.'/lib/httpSock.class.php');
	}

	function check_connection($site_id,$api_key) {
		$requestPost = array(
			'id'=>$site_id,
			'api_key'=>$api_key,
		);

		$url = 'http://'.$this->server.'/gate/check.php';
		$httpSock = new httpSock($url,'GET',$requestPost);
		$httpSock->send();

		$result = $httpSock->resContent;

		if(substr($result,0,2)=='OK') {
			return true;
		}
		else {
			return false;
		}
	}

	function getLinkDetail($taxid) {
		$config = Core::loader('config');
		$config_godotax = $config->load('godotax');
		return 'http://'.$this->server.'/gate/page.php?id='.$config_godotax['site_id'].'&api_key='.$config_godotax['api_key'].'&mode=detail&taxid='.$taxid;
	}

	function getLinkList() {
		$config = Core::loader('config');
		$config_godotax = $config->load('godotax');
		return 'http://'.$this->server.'/gate/page.php?id='.$config_godotax['site_id'].'&api_key='.$config_godotax['api_key'].'&mode=list';
	}

	function sendHttp($arData) {

		$config = Core::loader('config');
		$config_godotax = $config->load('godotax');

		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->encodeKey);
		$requestPost=array(
			'request' => base64_encode($xxtea->encrypt(serialize($arData))),
			'id'=>$config_godotax['site_id'],
			'api_key'=>$config_godotax['api_key'],
		);

		$url = 'http://'.$this->server.'/gate/add_taxinvoice.php';
		$httpSock = new httpSock($url,'POST',$requestPost);
		$httpSock->send();

		$result = $httpSock->resContent;

		return $result;
	}

	function sendGodoetax($taxsno) {
		$taxsno = (int)$taxsno;

		$arData = array();
		$db = Core::loader('db');
		$taxResult = $db->_select("select * from gd_tax where sno='{$taxsno}'");

		if(!$taxResult[0]['sno']) {
			return false;
		}
		$taxResult = $taxResult[0];
		$query = $db->_query_print('select * from gd_order where ordno=[s]',$taxResult['ordno']);

		$orderResult = $db->_select($query);
		$orderResult = $orderResult[0];


		$arData['DMDER_BUSNID_TP_CD']='01'; // 사업자등록번호 구분코드(01 사업자등록번호,02 주민등럭번호,03 외국인)
		$arData['DMDER_BUSNID']=$taxResult['busino']; // 사업자등록번호
		$arData['DMDER_SUB_BD_NO']=''; // 종사업자등록번호
		$arData['SUP_AMT_SM']=$taxResult['supply']; // 공급가액총액
		$arData['TX_SM']=$taxResult['surtax']; // 세액합계
		$arData['TOT_AMT']=$taxResult['price']; // 총금액
		$arData['ETAXBIL_KND_CD']='01'; // 전자세금계산서종류코드(01 일반, 02 영세)
		$arData['RCPT_RQEST_TP_CD']='01'; // 영수청구구분코드(01 영수, 02 청구)
		$arData['WRITE_DT']=str_replace('-','',$taxResult['issuedate']); // 작성날짜
		$arData['ETAXBIL_NOTE']=''; // 비고

		$arData['DMDER_MAIN_TX_OFFCR_NM']=$orderResult['nameOrder'];
		$arData['DMDER_MAIN_TX_OFFCR_EMAIL_ADDR']=$orderResult['email'];
		$arData['DMDER_MAIN_TX_OFFCR_MTEL_NO']=$orderResult['mobileOrder'];
		$arData['DMDER_BUSNSECT_NM']=$taxResult['service'];
		$arData['DMDER_DETAIL_NM']=$taxResult['item'];
		$arData['DMDER_CHIEF_NM']=$taxResult['name'];
		$arData['DMDER_TRADE_NM']=$taxResult['company'];
		$arData['DMDER_ADDR']=$taxResult['address'];

		$query = $db->_query_print('select count(*) as cnt from gd_order_item where ordno=[s]',$taxResult['ordno']);
		$tmp = $db->_select($query);
		$itemCount = $tmp[0]['cnt'];

		$query = $db->_query_print('select * from gd_order_item where ordno=[s] limit 1',$taxResult['ordno']);
		$itemResult = $db->_select($query);

		$arItem = array(
			'THNG_PURCHS_DT'=>str_replace('-','',$taxResult['issuedate']),
			'THNG_SUP_AMT'=>$taxResult['supply'],
			'THNG_TX'=>$taxResult['surtax'],
		);

		$suffix = ($itemCount>1) ? '외 '.($itemCount-1).'건' : '';
		$arItem['THNG_NM']=strcut_by_char($itemResult[0]['goodsnm'],100,$suffix);

		$arData['item'][]=$arItem;

		$result = $this->sendHttp($arData);

		if(substr($result,0,4)=='DONE') {
			$query = $db->_query_print('update gd_tax set step="4" , doc_number=[s] where sno=[s]',substr($result,4),$taxsno);
			$db->query($query);
			return true;
		}
		else if(substr($result,0,5)=='ERROR') {
			msg(substr($result,5));exit;
		}
		else {
			return false;
		}
	}



}


?>
