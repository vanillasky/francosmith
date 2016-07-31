<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp GDcopy
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';
require_once("../../../lib/upload.lib.php");


## 기능 처리 : Start --------------------------------------------------------------------------
if ( $_POST['act'] == 'handling' ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # 현재 서버절대경로

	$upload = new upload_file;

	{ // GD 이미지

		if ( trim( $_FILES['gcopyimg']['name'] ) ){ // GD 원본파일저장

			$uploadFile_Name	= $_FILES['gcopyimg']['name'];

			if ( $webftp->chkSheet( $uploadFile_Name, $webftp->img_ext_str ) == false ){

				echo alert( "업로드파일중에 확장자가 이미지파일이 아닌파일이 첨부되어져 있습니다.", "history.go( -1 );" );
				exit;
			}

			$str_end			= strtolower( strrchr( $uploadFile_Name, '.' ) );
			//$pp					= $webftp->validName( $nowPath, $uploadFile_Name );
			$pp					= $uploadFile_Name;

			$upload->upload_file($_FILES['gcopyimg'],$nowPath.$pp,'image');
			if(!$upload->upload()){
				echo alert( "업로드파일이 올바르지 않습니다.", "history.go( -1 );" );
				exit;
			}
		}


		if ( @function_exists( "imageCreate" ) ){ // GD library 체크

			$arr				= $webftp->getSupportedImageTypes();
			$supportnum			= sizeOf( $arr );
			$supporttype		= implode( ", ", $arr );

			foreach ( $arr as $sptypes ){

				if ( $sptypes == "JPG" ) $jpgsupport = "y";
				if ( $sptypes == "PNG" ) $pngsupport = "y";
				if ( $sptypes == "GIF" ) $gifsupport = "y";
			}

			if ( $jpgsupport != "y" && $str_end == ".jpg" ){

				if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- 원본삭제
				echo alert( "원본파일의 .JPG를 서버GD환경에서 복사기능을 지원하지 않습니다.", "history.go( -1 );" );
				exit;
			}

			if ( $gifsupport != "y" && $str_end == ".gif" ){

				if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- 원본삭제
				echo alert( "원본파일의 .GIF를 서버GD환경에서 복사기능을 지원하지 않습니다.", "history.go( -1 );" );
				exit;
			}

			if ( $pngsupport != "y" && $str_end == ".png" ){

				if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- 원본삭제
				echo alert( "원본파일의 .PNG를 서버GD환경에서 복사기능을 지원하지 않습니다.", "history.go( -1 );" );
				exit;
			}

			$gdConfirm = "y";	# 승인
		}


		$img_no = 0;

		foreach ( $_POST['sizeCopyimg'] as $no => $getsize ){

			setcookie( "sizeCopyimg[$no]",	$_POST['sizeCopyimg'][$no], time() + ( 86400 * 30 ), "/" ); # 30일간 쿠키 간직

			if ( $getsize > 0 && $gdConfirm == "y" && $_POST['imageCopyon'] == "y" && $supportnum > 0 ){

				$img_no++;

				if ( $_POST['sizeCopyimgName'][ $no ] != '' ){
					$cpfileName		= $_POST['sizeCopyimgName'][ $no ] . $str_end;
				}
				else {

					$tmp			= explode( ".", $pp );
					$tmp[0]			= $tmp[0] . '_' . sprintf( "%03d", $img_no );
					$cpfileName		= implode( ".", $tmp );
				}

				//$cpfileName		= $webftp->validName( $nowPath, $cpfileName );

				$webftp->create_thumb_wfixed( $nowPath . $pp, $nowPath . $cpfileName, $getsize ); # create_thumb_wfixed("원본","복사본","가로사이즈");
			}
		}


		if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- 원본삭제
	}


	{ // 직접등록

		if ( !empty( $_FILES['directimg'] ) ){		// 화일이 있으면

			$file_array = reverse_file_array($_FILES['directimg']);

			for ( $No = 0; $No < count( $_FILES['directimg']['name'] ); $No++ ){

				if ( trim( $_FILES['directimg']['name'][$No] ) != '' ){

					$OriName = $_FILES['directimg']['name'][$No];
					//$TmpName = $webftp->validName( $nowPath, $OriName );
					$TmpName = $OriName;

					if ( $webftp->chkSheet( $OriName, $webftp->app_ext_str ) == false ){

						echo alert( "업로드파일중에 확장자가 허용되지않는 파일이 첨부되어져 있습니다.", "history.go( -1 );" );
						exit;
					}

					$upload->upload_file($file_array[$No],$nowPath.$TmpName,'image');
					if(!$upload->upload()){
						echo alert( "업로드파일이 올바르지 않습니다.", "history.go( -1 );" );
						exit;
					}
					@chmod( $nowPath . $TmpName, 0707 );
				}
			}
		}
	}


	setDu($duTarget); # 계정용량 계산
	echo '<script> alert( "파일 업로드에 성공하였습니다." ); opener.window.top.folder_frame.location.reload(); opener.window.top.global_frame.location.reload(); window.close(); </script>';
	exit();
}
## ---------------------------------------------------------------------------------------- End
?>


<html>
<head>
<title>Webftp GDcopy</title>
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
    <td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_copyup.gif" align="absmiddle"></td>
  </tr>
  <!-- 타이틀 : End -->

  <!-- 설정폼 : Start -->
  <tr>
    <td valign="top" align="center" style="padding:14px">
      <? include "../../proc/warning_disk_msg.php"; # not_delete  ?>

      <table class="table_Basic1" border="0" cellpadding="0" cellspacing="0">
	  <form method="post" name="fm" action="?webftpid=<?=$webftpid?>" onsubmit="return fm_chk( this );" enctype="multipart/form-data">
      <input type="hidden" name="act" value="handling">
        <tr>
          <td>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">파일 원본복사등록</td>
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
					<td class="table_Left1" align="right">지원사항</td>
					<td class="table_Right1"><?
//--------------------------------- GD복사기능
# 체크섬수정 m.by zeldign 2004.08.25

if ( function_exists( "imageCreate" ) ){

	$arr=$webftp->getSupportedImageTypes();
	$supporttype=implode( ", ", $arr );
	$getversion=phpversion();

	echo '<input TYPE="hidden" name="imageCopyon" value="y">'."GD를 지원하여 이미지복사기능이 활성화 되었습니다.<br>[지원사항 →] <b>".$supporttype;

}else {
	echo "GD를 지원하지 않아 복사기능을 사용할수 없습니다.";
}
?></td>
				  </tr>
				  <tr>
					<td class="table_Left1" align="right">원본이미지</td>
					<td class="table_Right1"><input type="file" name="gcopyimg" <?if ( sizeof( $arr ) == 0 ) echo " disabled";?> size="50" class="Line"><br>복사 후 원본이미지 저장 여부 : <span  class="noline"><input type="radio" name="gcopyimg_save" value="N" checked> 삭제 <input type="radio" name="gcopyimg_save" value="Y"> 저장</span></td>
				  </tr>
				</table>
			  </td>
			</tr>
		  </table>
		  <br>

		  <table width="100%" border="0" cellpadding="0" cellspacing="0">
			  <tr>
                <td colspan="4" class="table_TLine1"></td>
              </tr>
			  <tr>
                <td colspan="4" height="10"></td>
              </tr>
			  <tr align="center">
                <td width="70" nowrap></td>
                <td width="80" nowrap class="table_Left2">가로 크기</td>
                <td width="130" nowrap class="table_Left2">이미지 명</td>
                <td width="100%" nowrap class="table_Left2">직접등록</td>
              </tr>
			  <tr>
                <td class="table_Line1" colspan="4"></td>
              </tr>

<?
	for ( $ii = 0; $ii < 10; $ii++ ){
?>
              <tr>
                <td class="table_Left1" nowrap>이미지 <?=( $ii + 1 )?></td>
                <td class="table_Right1" nowrap align="center"><input TYPE="text" NAME="sizeCopyimg[]" value="<?=$_COOKIE['sizeCopyimg'][$ii]?>" maxlength="3" size="5" OnKeydown="onlyNumber();" onblur="javascript:docBox( ( this.value ? true : false ), 'directimg[]', '<?=$ii?>' );" <?if ( sizeof( $arr ) == 0 ) echo " disabled";?> class="Line"> px</td>
                <td class="table_Right1" nowrap align="center"><input TYPE="text" NAME="sizeCopyimgName[]" value="" size="20" <?if ( sizeof( $arr ) == 0 ) echo " disabled";?> class="Line" ></td>
                <td class="table_Right1" nowrap align="center"><input type="file" name="directimg[]" size="36" disabled  class="Line"></td>
              </tr>
              <tr>
                <td class="table_Line1" colspan="4"></td>
              </tr>
<?	}?>
			  <tr>
                <td colspan="4" height="8"></td>
              </tr>
              <tr>
                <td colspan="4" class="table_BLine1"></td>
              </tr>
            </table>




          </td>
        </tr>
		<tr>
          <td height="10"></td>
        </tr>
        <tr>
          <td style="padding-left:10px">※ 이미지복사기능을 이용하시려면 <b>원본이미지</b>와 <b>가로크기</b>를 필수로 입력하셔야 합니다.<br>
          ※ 원본이미지 : <B>GD 지원사항</B>에 따라 가로 크기보다 큰 이미지를 선택합니다.<br>
          ※ 가로 크기 : 세로와 비례하게 축소되어 복사됩니다.<br>
          ※ 이미지 명 : 사용자 카피명을 입력합니다. 확장자( .gif, .jpg )는 제외하고 입력하세요. ( 공백 경우 자동 부여 )</td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
		<tr>
          <td height="20"></td>
        </tr>
		<tr>
          <td align="center" class="noline"><input type="image" src="<?=$img_path?>webftp/pop_bu_register.gif" border="0" align="absmiddle" value="[등록]"></td>
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

	var directimg = 0;

	for ( var i = 0; i < FObj['directimg[]'].length; i++ ){

		if ( FObj['directimg[]'][i].value != '' ){
			directimg++;
		}
	}


	if ( FObj.gcopyimg.value == '' && directimg == 0 ){

		alert( "원본이미지 또는 직접등록을 선택하셔야 합니다." );
		return false;
	}

	return true;
}



/*-------------------------------------
 활성화
-------------------------------------*/
function docBox( dobj, box, idx ){ //-- obj

	if ( dobj == false ) document.all[box][idx].disabled=false;
	else document.all[box][idx].disabled=true;
}



/*-------------------------------------
 페이지로딩 후
-------------------------------------*/
for ( var i = 0; i < fm['sizeCopyimg[]'].length; i++ ){

	var obj = fm['sizeCopyimg[]'][i];
	docBox( ( obj.value ? true : false ), 'directimg[]', i );
}
//-->
</SCRIPT>


<SCRIPT LANGUAGE="JavaScript" SRC="../../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
</body>
</html>