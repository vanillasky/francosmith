<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Delete
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';

if($_COOKIE[dPath] == '/'){
	echo('관리 가능한 디렉토리가 아닙니다.');
	exit;
}
?>


<html>
<head>
<title>Webftp Delete</title>
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
    <td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_delete.gif" align="absmiddle"></td>
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
                <td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">폴더 / 파일 삭제</td>
                <td class="title_SubRight1"></td>
              </tr>
            </table>
          </td>
        </tr>
		<tr>
          <td>

		    <table border="0" cellspacing="0" cellpadding="5" class="table_PopSelect1">
			  <td align="center">
				<table width="96%" border="0" cellspacing="0" cellpadding="2">
				  <?
foreach ( $_POST['sepchkbox'] as $v ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # 현재 서버절대경로


	{ // 삭제실행

		$hand_result = '';

		if ( $_POST['act'] == 'handling' ){

			if ( $webftp->delDirFile( $nowPath . $v ) ){
				$hand_result = '삭제성공';
			}
			else {
				$hand_result = '삭제실패';
			}
		}
	}


	echo '<tr><td>' . $_COOKIE['dPath'] . $v . '&nbsp;&nbsp;<b><font color="D68200">' . $hand_result . '</font></b></td></tr>';


	if ( $hand_result != '삭제성공' ){
		echo '<input type="hidden" name="sepchkbox[]" value="' . $v . '">';
	}
}

if ( count( $_POST['sepchkbox'] ) && $_POST['act'] == 'handling' ) setDu($duTarget); # 계정용량 계산
?>

				</table>
			  </td>
			</tr>
		  </table>

          </td>
        </tr>
		<tr>
          <td height="10"></td>
        </tr>
        <tr>
          <td style="padding-left:10px">- 폴더 / 파일 삭제가 실패시 퍼미션 및 삭제될 폴더가<br><font color="ffffff">- </font>비워져 있는지 확인하십시오.</td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
		<tr>
          <td height="20"></td>
        </tr>
		<tr>
          <td align="center" class="noline"><input type="image" src="<?=$img_path?>webftp/pop_bu_delete.gif" border="0" align="absmiddle" value="[삭제]"></td>
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

	if ( FObj['sepchkbox[]'] == null ){

		alert( "삭제할 폴더 / 파일이 없습니다." );
		return false;
	}

	if ( !confirm( "선택한 폴더 / 파일을 삭제하시겠습니까?" ) ){
		return false;
	}

	return true;
}



/*-------------------------------------
 본창 RELOAD
-------------------------------------*/
var act_value = '<?=$_POST['act']?>';

if ( act_value == 'handling' ){
	opener.window.top.folder_frame.location.reload();
	opener.window.top.global_frame.location.reload();
}
//-->
</SCRIPT>


</body>
</html>