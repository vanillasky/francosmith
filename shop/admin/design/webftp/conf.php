<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp Config
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname(__FILE__) . "/../../lib.php";
@include_once dirname(__FILE__) . "/webftp.class.php";


{ // ��� ����

	$hostpath	= str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] );
	$curr_path	= dirname( str_replace( $hostpath, "", __file__ ) ) . '/';
	$img_path	= $curr_path . '../../img/';

	$bbname		= basename( $_SERVER['PHP_SELF'] );					# ���� ȭ�� �̸�
	$dirname	= basename( dirname( $_SERVER['PHP_SELF'] ) );		# ���� ��� �̸�

	$tmp		= explode("/", $_COOKIE['dPath']);
	$duTarget	= $tmp[1] == 'data' ? $tmp[2] : $tmp[1];
}


{ // Ŭ���� ����

	if ( $_GET[ $tmp='webftpid' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $webftpid == '' ) $webftpid = 'default';

	${$webftpid} = new webftp;
	$webftp = &${$webftpid};
}
?>