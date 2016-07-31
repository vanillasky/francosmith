<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp Mkdir
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';

if($_COOKIE[dPath] == '/'){
	echo('���� ������ ���丮�� �ƴմϴ�.');
	exit;
}

{ // ���丮�� ����

	$handing_able = true;

	foreach( $webftp->dir_display as $tmp ){

		$pattern = str_replace( array( "/", "*" ), array( "\/", "[^\/]+" ), $tmp['path'] );

		if ( preg_match( "/^{$pattern}$/i", $webftp->ftp_path . $_COOKIE['dPath'] ) && $tmp['able'] != '' ){
			$handing_able = false;
			break;
		}
	}
}


## ��� ó�� : Start --------------------------------------------------------------------------
if ( $_POST['act'] == 'handling' ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # ���� ����������

	if ( !file_exists( $nowPath . $_POST['dir_name'] ) ){

		if ( mkdir( $nowPath . $_POST['dir_name']) ){

			@chMod( $nowPath . $_POST['dir_name'], 0757 );
			setDu($duTarget); # �����뷮 ���

			echo '<script> alert( "���� ������ �����Ͽ����ϴ�." ); opener.window.top.folder_frame.location.reload(); opener.window.top.global_frame.location.reload(); window.close(); </script>';
			exit();
		}
		else {

			echo '<script> alert( "���� ������ �����Ͽ����ϴ�." ); document.location.href="?webftpid=' . $webftpid . '"; </script>';
			exit();
		}
	}
	else {

		echo '<script> alert( "�̹� ����( ' . $_POST['dir_name'] . ' )�� �����մϴ�." ); document.location.href="?webftpid=' . $webftpid . '"; </script>';
		exit();
	}
}
## ---------------------------------------------------------------------------------------- End
?>


<html>
<head>
<title>Webftp Mkdir</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="styleSheet" href="<?=$curr_path?>../../style.css">
<link rel="styleSheet" href="<?=$curr_path?>webftp.css">
<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>
</head>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
  <!-- Ÿ��Ʋ : Start -->
  <tr>
    <td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_folder.gif" align="absmiddle"></td>
  </tr>
  <!-- Ÿ��Ʋ : End -->

  <!-- ������ : Start -->
  <tr>
    <td valign="top" align="center" style="padding:14px">

      <table class="table_Basic1" border="0" cellpadding="0" cellspacing="0">
	  <form method="post" name="fm" action="?webftpid=<?=$webftpid?>" onsubmit="return fm_chk( this );">
      <input type="hidden" name="act" value="handling">
        <tr>
          <td>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">��������</td>
                <td class="title_SubRight1"></td>
              </tr>
            </table>
          </td>
        </tr>
		<tr>
          <td>

<? if ( $handing_able === false ){ ?>
		    <b><?=$_COOKIE['dPath']?></b> ������ �������� ������ ���ѵǾ� �ֽ��ϴ�.
<? } ?>

		    <table border="0" cellspacing="0" cellpadding="5" class="table_PopSelect1">
			<tr>
			  <td>
				<table width="100%" border="0" cellspacing="0" cellpadding="4">
				  <tr>
					<td class="table_Left1" align="right">������</td>
					<td class="table_Right1"><input type="text" name="dir_name" size="50%" class="Line"></td>
				  </tr>
				</table>
			  </td>
			</tr>
		  </table>

          </td>
        </tr>
		<tr>
          <td height="20"></td>
        </tr>
		<tr>
          <td align="center" class="noline"><input type="image" src="<?=$img_path?>webftp/pop_bu_folder.gif" border="0" align="absmiddle" value="[����]"></td>
        </tr>
		</form>
      </table>

	</td>
  </tr>
  <!-- ������ : End -->


  <!-- Ŭ���� : Start -->
  <tr>
    <td class="table_PopCloseOut1"><div class="table_PopCloseOut2"><a href="javascript:parent.close();"><img src="<?=$img_path?>webftp/pop_closebu.gif" alt="Closw Window" border="0" align="absmiddle"></a></div></td>
  </tr>
  <!-- Ŭ���� : End -->
</table>


<SCRIPT LANGUAGE="JavaScript">
<!--
/*-------------------------------------
 ���� üũ
-------------------------------------*/
function fm_chk( FObj ){

<? if ( $handing_able === false ){ ?>
	alert( "<?=$_COOKIE['dPath']?> ������ �������� ������ ���ѵǾ� �ֽ��ϴ�." );
	return false;
<? } ?>

	if ( FObj['dir_name'].value == '' ){

		alert( "�������� �Է��ϼž� �մϴ�." );
		return false;
	}

	patten = eval(/^[a-zA-Z0-9]{1}[a-zA-Z0-9\._-]*$/);
	if (!patten.test(FObj['dir_name'].value)){
		alert( "������ ���� ���ڸ� ����� �� �����ϴ�.\n \\ / : * ? ' \" < > |" );
		return false;
	}

	return true;
}
//-->
</SCRIPT>


</body>
</html>