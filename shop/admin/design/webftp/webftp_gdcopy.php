<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp GDcopy
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';
require_once("../../../lib/upload.lib.php");


## ��� ó�� : Start --------------------------------------------------------------------------
if ( $_POST['act'] == 'handling' ){

	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # ���� ����������

	$upload = new upload_file;

	{ // GD �̹���

		if ( trim( $_FILES['gcopyimg']['name'] ) ){ // GD ������������

			$uploadFile_Name	= $_FILES['gcopyimg']['name'];

			if ( $webftp->chkSheet( $uploadFile_Name, $webftp->img_ext_str ) == false ){

				echo alert( "���ε������߿� Ȯ���ڰ� �̹��������� �ƴ������� ÷�εǾ��� �ֽ��ϴ�.", "history.go( -1 );" );
				exit;
			}

			$str_end			= strtolower( strrchr( $uploadFile_Name, '.' ) );
			//$pp					= $webftp->validName( $nowPath, $uploadFile_Name );
			$pp					= $uploadFile_Name;

			$upload->upload_file($_FILES['gcopyimg'],$nowPath.$pp,'image');
			if(!$upload->upload()){
				echo alert( "���ε������� �ùٸ��� �ʽ��ϴ�.", "history.go( -1 );" );
				exit;
			}
		}


		if ( @function_exists( "imageCreate" ) ){ // GD library üũ

			$arr				= $webftp->getSupportedImageTypes();
			$supportnum			= sizeOf( $arr );
			$supporttype		= implode( ", ", $arr );

			foreach ( $arr as $sptypes ){

				if ( $sptypes == "JPG" ) $jpgsupport = "y";
				if ( $sptypes == "PNG" ) $pngsupport = "y";
				if ( $sptypes == "GIF" ) $gifsupport = "y";
			}

			if ( $jpgsupport != "y" && $str_end == ".jpg" ){

				if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- ��������
				echo alert( "���������� .JPG�� ����GDȯ�濡�� �������� �������� �ʽ��ϴ�.", "history.go( -1 );" );
				exit;
			}

			if ( $gifsupport != "y" && $str_end == ".gif" ){

				if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- ��������
				echo alert( "���������� .GIF�� ����GDȯ�濡�� �������� �������� �ʽ��ϴ�.", "history.go( -1 );" );
				exit;
			}

			if ( $pngsupport != "y" && $str_end == ".png" ){

				if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- ��������
				echo alert( "���������� .PNG�� ����GDȯ�濡�� �������� �������� �ʽ��ϴ�.", "history.go( -1 );" );
				exit;
			}

			$gdConfirm = "y";	# ����
		}


		$img_no = 0;

		foreach ( $_POST['sizeCopyimg'] as $no => $getsize ){

			setcookie( "sizeCopyimg[$no]",	$_POST['sizeCopyimg'][$no], time() + ( 86400 * 30 ), "/" ); # 30�ϰ� ��Ű ����

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

				$webftp->create_thumb_wfixed( $nowPath . $pp, $nowPath . $cpfileName, $getsize ); # create_thumb_wfixed("����","���纻","���λ�����");
			}
		}


		if ( $_POST['gcopyimg_save'] == 'N' ) @unlink( $nowPath . $pp ); //-- ��������
	}


	{ // �������

		if ( !empty( $_FILES['directimg'] ) ){		// ȭ���� ������

			$file_array = reverse_file_array($_FILES['directimg']);

			for ( $No = 0; $No < count( $_FILES['directimg']['name'] ); $No++ ){

				if ( trim( $_FILES['directimg']['name'][$No] ) != '' ){

					$OriName = $_FILES['directimg']['name'][$No];
					//$TmpName = $webftp->validName( $nowPath, $OriName );
					$TmpName = $OriName;

					if ( $webftp->chkSheet( $OriName, $webftp->app_ext_str ) == false ){

						echo alert( "���ε������߿� Ȯ���ڰ� �������ʴ� ������ ÷�εǾ��� �ֽ��ϴ�.", "history.go( -1 );" );
						exit;
					}

					$upload->upload_file($file_array[$No],$nowPath.$TmpName,'image');
					if(!$upload->upload()){
						echo alert( "���ε������� �ùٸ��� �ʽ��ϴ�.", "history.go( -1 );" );
						exit;
					}
					@chmod( $nowPath . $TmpName, 0707 );
				}
			}
		}
	}


	setDu($duTarget); # �����뷮 ���
	echo '<script> alert( "���� ���ε忡 �����Ͽ����ϴ�." ); opener.window.top.folder_frame.location.reload(); opener.window.top.global_frame.location.reload(); window.close(); </script>';
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
  <!-- Ÿ��Ʋ : Start -->
  <tr>
    <td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_copyup.gif" align="absmiddle"></td>
  </tr>
  <!-- Ÿ��Ʋ : End -->

  <!-- ������ : Start -->
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
                <td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">���� ����������</td>
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
					<td class="table_Left1" align="right">��������</td>
					<td class="table_Right1"><?
//--------------------------------- GD������
# üũ������ m.by zeldign 2004.08.25

if ( function_exists( "imageCreate" ) ){

	$arr=$webftp->getSupportedImageTypes();
	$supporttype=implode( ", ", $arr );
	$getversion=phpversion();

	echo '<input TYPE="hidden" name="imageCopyon" value="y">'."GD�� �����Ͽ� �̹����������� Ȱ��ȭ �Ǿ����ϴ�.<br>[�������� ��] <b>".$supporttype;

}else {
	echo "GD�� �������� �ʾ� �������� ����Ҽ� �����ϴ�.";
}
?></td>
				  </tr>
				  <tr>
					<td class="table_Left1" align="right">�����̹���</td>
					<td class="table_Right1"><input type="file" name="gcopyimg" <?if ( sizeof( $arr ) == 0 ) echo " disabled";?> size="50" class="Line"><br>���� �� �����̹��� ���� ���� : <span  class="noline"><input type="radio" name="gcopyimg_save" value="N" checked> ���� <input type="radio" name="gcopyimg_save" value="Y"> ����</span></td>
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
                <td width="80" nowrap class="table_Left2">���� ũ��</td>
                <td width="130" nowrap class="table_Left2">�̹��� ��</td>
                <td width="100%" nowrap class="table_Left2">�������</td>
              </tr>
			  <tr>
                <td class="table_Line1" colspan="4"></td>
              </tr>

<?
	for ( $ii = 0; $ii < 10; $ii++ ){
?>
              <tr>
                <td class="table_Left1" nowrap>�̹��� <?=( $ii + 1 )?></td>
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
          <td style="padding-left:10px">�� �̹����������� �̿��Ͻ÷��� <b>�����̹���</b>�� <b>����ũ��</b>�� �ʼ��� �Է��ϼž� �մϴ�.<br>
          �� �����̹��� : <B>GD ��������</B>�� ���� ���� ũ�⺸�� ū �̹����� �����մϴ�.<br>
          �� ���� ũ�� : ���ο� ����ϰ� ��ҵǾ� ����˴ϴ�.<br>
          �� �̹��� �� : ����� ī�Ǹ��� �Է��մϴ�. Ȯ����( .gif, .jpg )�� �����ϰ� �Է��ϼ���. ( ���� ��� �ڵ� �ο� )</td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
		<tr>
          <td height="20"></td>
        </tr>
		<tr>
          <td align="center" class="noline"><input type="image" src="<?=$img_path?>webftp/pop_bu_register.gif" border="0" align="absmiddle" value="[���]"></td>
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

	var directimg = 0;

	for ( var i = 0; i < FObj['directimg[]'].length; i++ ){

		if ( FObj['directimg[]'][i].value != '' ){
			directimg++;
		}
	}


	if ( FObj.gcopyimg.value == '' && directimg == 0 ){

		alert( "�����̹��� �Ǵ� ��������� �����ϼž� �մϴ�." );
		return false;
	}

	return true;
}



/*-------------------------------------
 Ȱ��ȭ
-------------------------------------*/
function docBox( dobj, box, idx ){ //-- obj

	if ( dobj == false ) document.all[box][idx].disabled=false;
	else document.all[box][idx].disabled=true;
}



/*-------------------------------------
 �������ε� ��
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