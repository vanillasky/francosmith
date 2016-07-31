<?php
/**
 * �߰輭���� ���̹�üũ�ƿ� AIP ��������
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
	msg('���̹�üũ�ƿ��� ��û�Ǿ�� �ֹ� API������ ��û �� �� �ֽ��ϴ�');exit;
}

$url = $ncAPI->relayURL; // checkout�� URL�Դϴ�

// �߰輭���� ����� ��û������ ����ϴ�
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
	msg('���̹�üũ�ƿ� �ֹ� API���� ��û�� �����߽��ϴ�.'.$result);exit;
}

$configCheckoutAPI = array(
	'cryptkey'=>$cryptKey,
	'linkStock'=>'y',
);
$config->save('checkoutapi',$configCheckoutAPI);

echo "<script>\n";
echo "alert('���̹�üũ�ƿ� �ֹ� API���� ������ �Ǿ����ϴ�');parent.location.href=parent.location.href;";
echo "</script>";
?>
