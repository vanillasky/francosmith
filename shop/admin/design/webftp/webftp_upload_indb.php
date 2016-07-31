<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Upload
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';
require_once("../../../lib/upload.lib.php");

if ( $_POST['act'] == 'handling' && !empty($_FILES['directimg']) ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # 현재 서버절대경로
	$TmpName = $_FILES['directimg']['name'];

	if ( $webftp->chkSheet( $TmpName, $webftp->app_ext_str ) == false ){
		echo '<script type="text/javascript">alert("업로드파일중에 확장자가 허용되지않는 파일이 첨부되어져 있습니다"); parent.update_sended();</script>';
		exit;
	}

	$exist = file_exists($nowPath . $TmpName);
	$cookie_name = 'directimg_rewrite_'.$_GET['count'];

	if ($exist === true && isset($_COOKIE[$cookie_name]) === false)
	{
		echo '<script type="text/javascript">parent.update_rewrite();</script>';
		exit;
	}
	else if ($exist === true && $_COOKIE[$cookie_name] != 'Y');
	else {
		$upload= new upload_file($_FILES['directimg'],$nowPath.$TmpName);
		if(!$upload->upload()){
			echo alert( "업로드파일이 올바르지 않습니다.", "history.go( -1 );" );
			exit;
		}
		setDu($duTarget); # 계정용량 계산
	}

	echo '<script type="text/javascript">parent.update_sended();</script>';
	exit();
}
?>