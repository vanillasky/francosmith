<?php
/**
 * 중계서버에 네이버체크아웃 AIP 연동설정
 * @author sunny, oneorzero
 */
include('../lib.php');
include_once(SHOPROOT.'/lib/httpSock.class.php');
$ncAPI = Core::loader('naverCheckoutAPI');
$config = Core::loader('config');
$godo = $config->load('godo');

$strPath = '../../conf/naverCheckout.cfg.php';
if(file_exists($strPath)) require $strPath;

if(!($checkoutCfg['imageId'] && $checkoutCfg['useYn']=='y')) {
	msg('네이버체크아웃이 신청되어야 주문 API연동을 신청 할 수 있습니다');exit;
}

$url = $ncAPI->relayURL; // checkout의 URL입니다

// 중계서버에 등록할 신청정보를 만듭니다
$apiUrl = $sitelink->_get_full_url('http',$sitelink->_regular_domain,21,$sitelink->_prefix_dir.'/_godoConn/link.checkout.php');
$cryptKey = substr(md5(microtime().rand(1,1000)),0,10);

$requestPost = array(
	'mode'=>'register',
	'shopNo'=>$godo['sno'],
	'naverId'=>$checkoutCfg['naverId'],
	'shopButtonKey'=>$checkoutCfg['imageId'],
	'apiUrl'=>$apiUrl,
	'cryptKey'=>$cryptKey,
);

$httpSock = new httpSock($url,'POST',$requestPost);
$httpSock->send();
$result = $httpSock->resContent;
if($result!='DONE') {
	msg('네이버체크아웃 주문 API연동 신청에 실패했습니다.'.$result);exit;
}

$configCheckoutAPI = array(
	'cryptkey'=>$cryptKey,
	'linkStock'=>'y',
);
$config->save('checkoutapi',$configCheckoutAPI);

echo "<script>\n";
echo "alert('네이버체크아웃 주문 API연동 설정이 되었습니다');parent.location.href=parent.location.href;";
echo "</script>";
?>
