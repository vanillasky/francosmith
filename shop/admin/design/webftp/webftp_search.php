<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp Search
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';



function dir_tag_option( $dirTree, $depth ){

	global $img_path, $webftp;

	$num_row = count( $dirTree );
	$idx = -1;

	ksort ($dirTree);
	reset ($dirTree);

	foreach ( $dirTree as $key => $arr ){

		$idx++;

		$arr[path] = str_replace( $webftp->ftp_path, "", $arr[path] ) . '/';	# URL ���

		echo '<option value="' . $arr[path] . '">' . str_repeat( '&nbsp;&nbsp;', $depth ) . $key . '</option>';

		if ( count( $arr[low_dir] ) ) dir_tag_option( $arr[low_dir], $depth + 1 );
	}
}
?>


<html>
<head>
<title>Webftp Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="styleSheet" href="<?=$curr_path?>../../style.css">
<link rel="styleSheet" href="<?=$curr_path?>webftp.css">
<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>
</head>
<body bgcolor="#7D746E" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- ��ü���� : Start -->
<div class="allview"><a href="javascript:;" onclick="frame_list_dpath('/');"><font color="ffffff">���� �� ���� ã��</font></a></div>
<!-- ��ü���� : End -->

<!-- �˻��� : Start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="7D746E" align="center">

      <table width="88%" cellpadding="0" cellspacing="0" border="0" style="margin:10 0 14 0;">
      <form name="webftp_search" onsubmit="return fm_chk( this );">
        <tr>
          <td style="font-size:8pt;letter-spacing:-1px;color:#D8CDC5">�˻��� ���� �Ǵ� ���� : </td>
        </tr>
        <tr>
          <td><input type="text" name="srch_value" style="width:100%;ime-mode:active;height:18px;background:#C5BAB3;font:9pt tahoma;border-top:1px #6B6059 solid;border-left:1px #6B6059 solid;border-right:1px #C5BAB3 solid;border-bottom:1px #C5BAB3 solid;color:#333333;"></td>
        </tr>
        <tr>
          <td style="font-size:8pt;letter-spacing:-1px;color:#D8CDC5;padding-top:6;">ã�� ��ġ : </td>
        </tr>
        <tr>
          <td><select name="srch_dir" style="font:8pt ����;width:100%;background:#C5BAB3;color:#333333">
          <option value="">- ���� ����</option>
          <option value="/">- ��Ʈ</option>

<?
{ // dir_tag_print �Լ� ȣ��

	$dirTree = $webftp->get_dirStructure( $webftp->ftp_path . "/" );
	dir_tag_option( $dirTree, $depth = 1 );
}
?>

          </select></td>
        </tr>
        <tr>
          <td align="center" class="noline" style="padding-top:12;"><input type="image" src="<?=$img_path;?>webftp/btn_search.gif"></td>
        </tr>
      </form>
      </table>

    </td>
  </tr>
</table>
<!-- �˻��� : End -->


<script language="javascript">
function fm_chk( FObj ){

	if ( FObj.srch_value.value == '' ){

		alert( "�˻��� ���� �Ǵ� ���� �����ϼž� �մϴ�." );
		FObj.srch_value.focus();
		return false;
	}

	if ( FObj.srch_dir.value != '' ){

		var dPath = FObj.srch_dir.value;
		setCookie( name='dPath', value=dPath, expires='', path='/' );
	}

	window.top.folder_frame.location.href = curr_path + 'webftp_list.php?webftpid=' + webftpid + '&srch_value=' + FObj.srch_value.value;

	return false;
}
</script>


</body>
</html>