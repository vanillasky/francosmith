<?php
include '../lib.php';
include_once(SHOPROOT.'/lib/httpSock.class.php');
$url = 'http://www.godo.co.kr/gate/remoteGodoPage.php';
$requestVar = array(
	'code' => null, // �����������ڵ�
	'solutionType' => null, // �ַ��Ÿ��
	'rootDir' => null, // ���
	'detail' => null, // �ַ������
	'etc'=>array(), // ��Ÿ����
);
$requestVar = array_merge($requestVar, $_GET);
$requestVar['solutionType'] = $godo['ecCode'];
$requestVar['rootDir'] = $cfg['rootDir'];
$requestVar['detail'] = $godo;
$httpSock = new httpSock($url,'POST',$requestVar);
$httpSock->send();
echo $httpSock->resContent;
?>