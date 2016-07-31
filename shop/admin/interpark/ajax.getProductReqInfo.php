<?php

/*
 * @type POST
 * @param inpkMidDispNo 인터파크 중분류코드
 * @return json [ { code : String, name : String, type : String, requ : boolean, desc : String } .. ]
 * @description 인터파크 중분류코드를 받아 해당 상품군의 필수입력정보 항목들을 반환
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