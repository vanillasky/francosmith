<?php
/*
 * IP 접속 제한 설정 (관리자 IP 접속 제한, 쇼핑몰 IP 접속 제한 설정)
 * @author artherot @ godosoft development team.
 */

include dirname(__FILE__).'/../lib.php';

// IP 접속 제한 저장
$saveResult				= $IPAccessRestriction->saveAccessIP();

if ($saveResult === 'NO_DATA') {
	msg('IP접속제한 설정을 다시 확인해 주세요.');
}
else if ($saveResult === 'NO_IP') {
	msg('IP접속제한 설정을 처리할 IP 를 등록해 주세요.');
} else if ($saveResult === true) {
	msg('IP접속제한 설정이 완료 되었습니다.',$_SERVER['HTTP_REFERER'],'parent');
}
?>