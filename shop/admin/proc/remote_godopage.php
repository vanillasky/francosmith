<?php
include '../lib.php';
include_once(SHOPROOT.'/lib/httpSock.class.php');
$url = 'http://www.godo.co.kr/gate/remoteGodoPage.php';
$requestVar = array(
	'code' => null, // 원격페이지코드
	'solutionType' => null, // 솔루션타입
	'rootDir' => null, // 경로
	'detail' => null, // 솔루션정보
	'etc'=>array(), // 기타정보
);
$requestVar = array_merge($requestVar, $_GET);
$requestVar['solutionType'] = $godo['ecCode'];
$requestVar['rootDir'] = $cfg['rootDir'];
$requestVar['detail'] = $godo;
$httpSock = new httpSock($url,'POST',$requestVar);
$httpSock->send();
echo $httpSock->resContent;
?>