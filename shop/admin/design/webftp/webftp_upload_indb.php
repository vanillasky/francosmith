<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp Upload
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';
require_once("../../../lib/upload.lib.php");

if ( $_POST['act'] == 'handling' && !empty($_FILES['directimg']) ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # ���� ����������
	$TmpName = $_FILES['directimg']['name'];

	if ( $webftp->chkSheet( $TmpName, $webftp->app_ext_str ) == false ){
		echo '<script type="text/javascript">alert("���ε������߿� Ȯ���ڰ� �������ʴ� ������ ÷�εǾ��� �ֽ��ϴ�"); parent.update_sended();</script>';
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
			echo alert( "���ε������� �ùٸ��� �ʽ��ϴ�.", "history.go( -1 );" );
			exit;
		}
		setDu($duTarget); # �����뷮 ���
	}

	echo '<script type="text/javascript">parent.update_sended();</script>';
	exit();
}
?>