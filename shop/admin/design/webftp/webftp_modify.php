<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp Modify
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';

if($_COOKIE[dPath] == '/'){
	echo('���� ������ ���丮�� �ƴմϴ�.');
	exit;
}

## ��� ó�� : Start --------------------------------------------------------------------------
if ( $_GET[ $tmp='sepchkbox' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];

if ( $_POST['act'] == 'handling' ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # ���� ����������

	if ( $webftp->chkSheet( $_POST['new_name'], $webftp->app_ext_str ) == false ){
		echo '<script> alert( "������ �ʴ� Ȯ���ڴ� ����� �� �����ϴ�." ); document.location.href="?webftpid=' . $webftpid . '&sepchkbox[0]=' . $sepchkbox[0] . '"; </script>';
		exit;
	}

	if ( !file_exists( $nowPath . $_POST['new_name'] ) ){

		if ( rename( $nowPath . $sepchkbox[0], $nowPath . $_POST['new_name']) ){

			echo '<script> alert( "�̸� ���濡 �����Ͽ����ϴ�." ); opener.window.top.folder_frame.location.reload(); opener.window.top.global_frame.location.reload(); window.close(); </script>';
			exit();
		}
		else {

			echo '<script> alert( "�̸� ���濡 �����Ͽ����ϴ�." ); document.location.href="?webftpid=' . $webftpid . '&sepchkbox[0]=' . $sepchkbox[0] . '"; </script>';
			exit();
		}
	}
	else {

		echo '<script> alert( "�̹� ����( ' . $_POST['new_name'] . ' )�� �����մϴ�." ); document.location.href="?webftpid=' . $webftpid . '&sepchkbox[0]=' . $sepchkbox[0] . '"; </script>';
		exit();
	}
}
## ---------------------------------------------------------------------------------------- End
?>


<html>
<head>
<title>Webftp Modify</title>
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
    <td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_modify.gif" align="absmiddle"></td>
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
                <td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">������ / ���ϸ� ����</td>
                <td class="title_SubRight1"></td>
              </tr>
            </table>
          </td>
        </tr>
		<tr>
          <td>

		    <table border="0" cellspacing="0" cellpadding="5" class="table_PopSelect1">
			<tr>
			  <td>
				<table width="100%" border="0" cellspacing="0" cellpadding="4">
				  <tr>
					<td class="table_Left1" align="right">�̸�</td>
					<td class="table_Right1"><?=$sepchkbox[0]?></td><input type="hidden" name="sepchkbox[]" value="<?=$sepchkbox[0]?>">
				  </tr>
				  <tr>
					<td class="table_Left1" align="right">�� �̸�</td>
					<td class="table_Right1"><input type="text" name="new_name" size="50%" class="Line"></td>
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
          <td align="center" class="noline"><input type="image" src="<?=$img_path?>webftp/pop_bu_modify.gif" border="0" align="absmiddle" value="[����]"></td>
        </tr>
		<tr>
          <td style="padding:15 0 0 7"><div>�� <font class=small color=444444>���ϸ� ����� ���̸��� <font color=EA0095>������ Ȯ���ڸ���� ��� �Է�</font>�ϼ���.</font></div>
          <div style="padding:3 0 0 16"><font class=small color=444444>ex) <font color=EA0095><b>logo.gif</b></font> �� ���̸����� �����Ѵٸ�, <font color=EA0095><b>logo1.gif</b></font> �̷��� <font color=EA0095><b>gif ���� ��� �Է�</b></font></font></div>
          <div style="padding:3 0 0 0">�� <font class=small color=444444>Ȯ���ڸ� ���� �Է��ϸ� ������ �����˴ϴ�. �� Ȯ���ڸ���� �Է��ؼ� �����ϼ���.</font></div>
          <div style="padding:3 0 0 0">�� <font class=small color=444444>Ȯ���ڸ��̶� ���������� gif �� jpg�� ���մϴ�.</font></div>
          </td>
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

	if ( FObj['sepchkbox[]'].value == '' ){

		alert( "������ ���� / ������ �����ϴ�." );
		return false;
	}

	if ( FObj['new_name'].value == '' ){

		alert( "�� �̸��� �Է��ϼž� �մϴ�." );
		return false;
	}

	patten = eval(/^[a-zA-Z0-9]{1}[a-zA-Z0-9\._-]*$/);
	if (!patten.test(FObj['new_name'].value)){
		alert( "�� �̸��� ���� ���ڸ� ����� �� �����ϴ�.\n \\ / : * ? ' \" < > |" );
		return false;
	}

	return true;
}
//-->
</SCRIPT>


</body>
</html>