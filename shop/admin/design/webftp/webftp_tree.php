<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Tree
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';



function dir_tag_print( $dirTree, $colstate, $divid ){

	global $img_path, $webftp;

	$num_row = count( $dirTree );
	$idx = -1;

	ksort ($dirTree);
	reset ($dirTree);


	foreach ( $dirTree as $key => $arr ){

		$idx++;

		$divid_obj = $divid . $idx;							# 아이디명

		$arr[path] = str_replace( $webftp->ftp_path, "", $arr[path] ) . '/';	# URL 경로

		if ( $num_row != ( $idx + 1 ) ) $tdstyle = " style=\"background:url('" . $img_path . "webftp/tab_treed_active.gif') repeat-y;\"";
		else $tdstyle = "";

		if ( count( $arr[low_dir] ) ){

			$ico		= $img_path . ( $_COOKIE[ $arr[path] ] == 'Y' ? 'webftp/tab_opened.gif' : 'webftp/tab_closed.gif' );
			$aTagJs		= ' href="javascript:;" onclick="tree_cookie( \'' . $arr[path] . '\', document.webftp_tree[\'' . $divid_obj . '\'] );"';
			echo '<input type="hidden" name="' . $divid_obj . '" value="' . ( $_COOKIE[ $arr[path] ] == 'Y' ? 'Y' : 'N' ) . '"><input type="hidden" name="' . $arr[path] . '" value="' . $divid_obj . '">';
		}
		else {

			$ico		= $img_path . 'webftp/tab_none.gif';
			$aTagJs		= '';
		}


		echo '
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>';

		for ( $i = 0; $i < strlen( $colstate ); $i++ ){
			if ( substr( $colstate, $i, 1 ) == 'Y' ) echo '<td width="14" style="background:url(\'' . $img_path . 'webftp/tab_treed_active.gif\') repeat-y;"></td>';
			else echo '<td width="14"></td>';
		}

		echo '
		<td width="23"' . $tdstyle . '><a' . $aTagJs . ' onfocus="this.blur();"><div id="' . $divid_obj . '_img"><img src="' . $ico . '" alt="" border="0"></div></a></td>
		<td><a href="javascript:;" onclick="frame_list_dpath(\'' . $arr[path] . '\');" onfocus="this.blur();"><font style="font:8pt tahoma;color:ffffff">' . $key . '</font></a></td>
		</tr>
		</table>' . "\n\n";

		echo '<div id="' . $divid_obj . '" style="display:block;border:solid 0 #000000;">';

		if ( count( $arr[low_dir] ) ){

			if ( $num_row != ( $idx + 1 ) ) $colstateSub = $colstate . 'Y';
			else $colstateSub = $colstate . 'N';

			dir_tag_print( $arr[low_dir], $colstateSub, $divid_obj . '.' . $idx );
		}

		echo '</div>' . "\n\n";
	}
}
?>


<html>
<head>
<title>Webftp Tree</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="styleSheet" href="<?=$curr_path?>../../style.css">
<link rel="styleSheet" href="<?=$curr_path?>webftp.css">
<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>
</head>
<body bgcolor="#7D746E" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- 전체보기 : Start -->
<div class="allview"><a href="javascript:;" onclick="frame_list_dpath('/');"><font color="ffffff">전체보기</font></a></div>
<!-- 전체보기 : End -->

<!-- 사용자 디렉토리 : Start -->
<form name="webftp_tree">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="10" bgcolor="7D746E" background="<?=$img_path;?>webftp/left_folder_bg.gif"></td>
  </tr>
  <tr>
    <td bgcolor="7D746E" style="padding:0 6 12 11">

<?
{ // dir_tag_print 함수 호출

	$dirTree = $webftp->get_dirStructure( $webftp->ftp_path . "/" );
	dir_tag_print( $dirTree, $colstate = '', $divid = 'tree' );
}
?>

    </td>
  </tr>
</table>
</form>
<!-- 사용자 디렉토리 : End -->


<script language="javascript"> tree_display( document.webftp_tree, 1 ); </script>


</body>
</html>