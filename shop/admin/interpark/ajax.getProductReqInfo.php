<?php

/*
 * @type POST
 * @param inpkMidDispNo ������ũ �ߺз��ڵ�
 * @return json [ { code : String, name : String, type : String, requ : boolean, desc : String } .. ]
 * @description ������ũ �ߺз��ڵ带 �޾� �ش� ��ǰ���� �ʼ��Է����� �׸���� ��ȯ
 */

require dirname(__FILE__).'/../lib.php';
require dirname(__FILE__).'/../../lib/httpSock.class.php';

header('Content-Type: text/plain; charset=euc-kr');

if(isset($_POST['inpkMidDispNo']) && $_POST['inpkMidDispNo'])
{
	$inpkMidDispNo = $_POST['inpkMidDispNo'];

	$http = new httpSock('http://godointerpark.godo.co.kr/sock_getProductReqInfo.php', 'POST', array('inpkMidDispNo' => $inpkMidDispNo));
	$http->send();

	exit($http->resContent);
}

?>