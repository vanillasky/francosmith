<?
/*********************************************************
* 파일명     :  setInfoPop.php
* 프로그램명 :  세트정보 팝업
* 작성자     :  이훈
* 생성일     :  2012.05.08
**********************************************************/
/*********************************************************
* 수정일     :  
* 수정내용   :  
**********************************************************/
$location = "셀리 > 세트정보 팝업";
include "../_header.popup.php";
include "../../lib/sAPI.class.php";
include "../../lib/lib.enc.php";

$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
$res_cust_cd = $db->_select($cust_cd_query);
$cust_cd = $res_cust_cd[0]['value'];

$cust_seq_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_seq');
$res_cust_seq = $db->_select($cust_seq_query);
$cust_seq = $res_cust_seq[0]['value'];

$domain_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
$res_domain = $db->_select($domain_query);
$selly_domain = $res_domain[0]['value'];

unset($cust_cd_query, $res_cust_cd, $cust_seq_query, $res_cust_seq, $domain_query, $res_domain);

$set_data = explode('|', $_GET['set_data']);
$sAPI = new sAPI();
$seq = $sAPI->xcryptare($cust_seq, $cust_cd, true);

?>

<iframe src="http://<?=$selly_domain?>/linkgoods/STSetInfoShop.gm?mall_cd=<?=$set_data[0]?>&mall_login_id=<?=$set_data[1]?>&seq=<?=base64_encode($seq)?>&set_cd=<?=$set_data[2]?>&mode=<?=$_GET['mode']?>" width="900" height="500" frameborder="0" scrolling="auto"></iframe>