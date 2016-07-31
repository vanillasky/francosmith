<?php
/**
 * Created on 2012-07-23
 *
 * Filename	: _error.php
 * Comment 	: 에러시 출력할 메세지
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?
include dirname(__FILE__) . "/../../setGoods/class/_common.php";

$confErrorMsgKor = array();
// for database (MySQL.class.php)
$confErrorMsgKor['801'] = '데이터 베이스에 접속할수없습니다.';
$confErrorMsgEng['801'] = 'Database connection error';

$confErrorMsgEng['802'] = '데이터베이스 서버 접속 해제 에러';
$confErrorMsgKor['802'] = 'Off error connecting to the database server';

$confErrorMsgEng['803'] = '데이터 베이스 질의 에러';
$confErrorMsgKor['803'] = 'Database query error';

// for array 
$confErrorMsgEng['101'] = '존재하지 않는 키 입니다.';
$confErrorMsgKor['101'] = '[Key] does not exist.';


/* select system language */
if ( Language == 'Korean' ) {
	// change php to (xml or file db).. 
	$confErrorMsg =& $confErrorMsgKor;
}else if ( Language == 'English' ) {
	$confErrorMsg =& $confErrorMsgEng;
}
?>