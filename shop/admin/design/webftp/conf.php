<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Config
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname(__FILE__) . "/../../lib.php";
@include_once dirname(__FILE__) . "/webftp.class.php";


{ // 경로 정의

	$hostpath	= str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] );
	$curr_path	= dirname( str_replace( $hostpath, "", __file__ ) ) . '/';
	$img_path	= $curr_path . '../../img/';

	$bbname		= basename( $_SERVER['PHP_SELF'] );					# 현재 화일 이름
	$dirname	= basename( dirname( $_SERVER['PHP_SELF'] ) );		# 현재 경로 이름

	$tmp		= explode("/", $_COOKIE['dPath']);
	$duTarget	= $tmp[1] == 'data' ? $tmp[2] : $tmp[1];
}


{ // 클래스 정의

	if ( $_GET[ $tmp='webftpid' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $webftpid == '' ) $webftpid = 'default';

	${$webftpid} = new webftp;
	$webftp = &${$webftpid};
}
?>