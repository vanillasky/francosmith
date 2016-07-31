<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Modify
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';

if($_COOKIE[dPath] == '/'){
	echo('관리 가능한 디렉토리가 아닙니다.');
	exit;
}

## 기능 처리 : Start --------------------------------------------------------------------------
if ( $_GET[ $tmp='sepchkbox' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];

if ( $_POST['act'] == 'handling' ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # 현재 서버절대경로

	if ( $webftp->chkSheet( $_POST['new_name'], $webftp->app_ext_str ) == false ){
		echo '<script> alert( "허용되지 않는 확장자는 사용할 수 없습니다." ); document.location.href="?webftpid=' . $webftpid . '&sepchkbox[0]=' . $sepchkbox[0] . '"; </script>';
		exit;
	}

	if ( !file_exists( $nowPath . $_POST['new_name'] ) ){

		if ( rename( $nowPath . $sepchkbox[0], $nowPath . $_POST['new_name']) ){

			echo '<script> alert( "이름 변경에 성공하였습니다." ); opener.window.top.folder_frame.location.reload(); opener.window.top.global_frame.location.reload(); window.close(); </script>';
			exit();
		}
		else {

			echo '<script> alert( "이름 변경에 실패하였습니다." ); document.location.href="?webftpid=' . $webftpid . '&sepchkbox[0]=' . $sepchkbox[0] . '"; </script>';
			exit();
		}
	}
	else {

		echo '<script> alert( "이미 동명( ' . $_POST['new_name'] . ' )이 존재합니다." ); document.location.href="?webftpid=' . $webftpid . '&sepchkbox[0]=' . $sepchkbox[0] . '"; </script>';
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
  <!-- 타이틀 : Start -->
  <tr>
    <td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_modify.gif" align="absmiddle"></td>
  </tr>
  <!-- 타이틀 : End -->

  <!-- 설정폼 : Start -->
  <tr>
    <td valign="top" align="center" style="padding:14px">

      <table class="table_Basic1" border="0" cellpadding="0" cellspacing="0">
	  <form method="post" name="fm" action="?webftpid=<?=$webftpid?>" onsubmit="return fm_chk( this );">
      <input type="hidden" name="act" value="handling">
        <tr>
          <td>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">폴더명 / 파일명 변경</td>
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
					<td class="table_Left1" align="right">이름</td>
					<td class="table_Right1"><?=$sepchkbox[0]?></td><input type="hidden" name="sepchkbox[]" value="<?=$sepchkbox[0]?>">
				  </tr>
				  <tr>
					<td class="table_Left1" align="right">새 이름</td>
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
          <td align="center" class="noline"><input type="image" src="<?=$img_path?>webftp/pop_bu_modify.gif" border="0" align="absmiddle" value="[변경]"></td>
        </tr>
		<tr>
          <td style="padding:15 0 0 7"><div>※ <font class=small color=444444>파일명 변경시 새이름은 <font color=EA0095>파일의 확장자명까지 모두 입력</font>하세요.</font></div>
          <div style="padding:3 0 0 16"><font class=small color=444444>ex) <font color=EA0095><b>logo.gif</b></font> 를 새이름으로 변경한다면, <font color=EA0095><b>logo1.gif</b></font> 이렇게 <font color=EA0095><b>gif 까지 모두 입력</b></font></font></div>
          <div style="padding:3 0 0 0">※ <font class=small color=444444>확장자명를 빼고 입력하면 파일이 삭제됩니다. 꼭 확장자명까지 입력해서 변경하세요.</font></div>
          <div style="padding:3 0 0 0">※ <font class=small color=444444>확장자명이란 파일포맷인 gif 나 jpg를 말합니다.</font></div>
          </td>
		</tr>
		</form>
      </table>

	</td>
  </tr>
  <!-- 설정폼 : End -->

  <!-- 클로즈 : Start -->
  <tr>
    <td class="table_PopCloseOut1"><div class="table_PopCloseOut2"><a href="javascript:parent.close();"><img src="<?=$img_path?>webftp/pop_closebu.gif" alt="Closw Window" border="0" align="absmiddle"></a></div></td>
  </tr>
  <!-- 클로즈 : End -->
</table>


<SCRIPT LANGUAGE="JavaScript">
<!--
/*-------------------------------------
 실행 체크
-------------------------------------*/
function fm_chk( FObj ){

	if ( FObj['sepchkbox[]'].value == '' ){

		alert( "변경할 폴더 / 파일이 없습니다." );
		return false;
	}

	if ( FObj['new_name'].value == '' ){

		alert( "새 이름을 입력하셔야 합니다." );
		return false;
	}

	patten = eval(/^[a-zA-Z0-9]{1}[a-zA-Z0-9\._-]*$/);
	if (!patten.test(FObj['new_name'].value)){
		alert( "새 이름에 다음 문자를 사용할 수 없습니다.\n \\ / : * ? ' \" < > |" );
		return false;
	}

	return true;
}
//-->
</SCRIPT>


</body>
</html>