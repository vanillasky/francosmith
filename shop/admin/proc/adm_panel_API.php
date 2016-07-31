<?php
header("Content-type: text/html; charset=euc-kr");
@include "../lib.php";

$readDomain = 'https://gongji16.godo.co.kr/userinterface/season4_main/';

$setRequest = array(
	'sno' => $godo['sno'],
	'service' => $godo['ecCode'],
	'freeType' => $godo['freeType'],
	'webCode' => $godo['webCode']
);

switch ( $_POST['type'] ) 
{
	case 'panelAPI' :
		$readUrl = $readDomain . 'admin_panelAPI.php';

		$setRequest['domain'] = $_SERVER['HTTP_HOST'];
	break;

	case 'noticeAPI' :
		$readUrl = $readDomain . 'admin_noticeAPI.php';

		$setRequest['limit'] = $_POST['limit'];
	break;

	case 'patchAPI' :
		$readUrl = $readDomain . 'admin_patchAPI.php';

		$setRequest['limit'] = $_POST['limit'];
	break;

	case 'eduAPI' :
		$readUrl = $readDomain . 'admin_eduAPI.php';

		$setRequest['limit'] = $_POST['limit'];
	break;

	case 'betterAPI' :
		$readUrl = $readDomain . 'admin_betterAPI.php';	

		$setRequest['limit'] = $_POST['limit'];
	break;
}

$setRequestUrl = $readUrl . '?' . @http_build_query($setRequest);

if( $readUrl ){
	$getParameter = readurl( $setRequestUrl );
}

echo $getParameter;
exit;
?>