<?php
//           꼬   ㅇㅣ  ㅇㅓ  ㅂ  ㅅㅔ  요.   
//           ㄱ   ㄹㄱ        ㅗ

define('MCASH_CANCEL_DEFINE', true);

// 휴대폰 결제 취소 서버환경 설정
// 디폴트로 테스트 서버환경으로 설정되어 있으며
// 실운영 환경으로 전환할때는 실서버환경으로 변경하셔야 합니다.

//공통 상수 정의
define('RTN_ERR', -1);
define('RTN_OK', 0);

//휴대폰 취소 모빌리언스 서버환경 설정 
// TEST IP  : 121.254.135.131
// REAL IP : 121.254.135.130
if (MOBILIANS_SERVICE_TYPE == '00') {
	define('SERVER_NAME', '121.254.135.131');
}
else {
	define('SERVER_NAME', '121.254.135.130');
}
define('SERVER_PORT', 7500);

//로그 환경 설정 (샘플경로이므로 가맹점 서버 경로로 맞춰주시고 other  그룹 쓰기권한 필수)
define('LOG_DIR',    dirname(__FILE__).'/../../../../log/mobilians/mcash_'.date('Ymd'));       //예제 로그를 남길 디렉토리 절대경로
define('LOG_RUN',    'YES');                                                 // 로그기록 사용은 YES , 미기록은 NO 

//전역변수
$gMrchid = '';
$gSvcid = '';
$gTradeid = '';
$gPrdtprice = '';
$gMobilid = '';

$gResultcd = '';
$gResultmsg = '';
$gszErrMsg = '';

?>
