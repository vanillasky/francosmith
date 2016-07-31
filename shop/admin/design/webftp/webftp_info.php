<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp Info
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';


if ( file_exists( $webftp->ftp_path . $_GET['file_root'] ) ){ // ���� ����

	$filename	= str_replace( dirname( $_GET['file_root'] ) . '/', "", $_GET['file_root'] );	# ���ϸ�


	{ // ���ϰ��

		$file_path		= $webftp->ftp_path . $_GET['file_root'];
		$file_url		= $webftp->ftp_url . $_GET['file_root'];
	}


	{ // ��������

		$path_parts = @pathinfo( $file_path );
		$path_parts['extension'] = strtolower( $path_parts['extension'] );

		$f_time = date( 'y-m-d H:i:s', @filemtime( $file_path ) ); # ��¥

		{ // ����ũ��

			$f_size = @filesize( $file_path );

			if ( $f_size > 1024 ) $f_size = round( $f_size / 1024, 2 ) . ' Kb';	# KB
			else $f_size = $f_size . ' Byte';	# B
		}


		{ // �׸�ũ��

			$p_size = $p_view = '';

			if ( $webftp->chkSheet( $file_path, $webftp->img_ext_str ) == true ){

				$tmp = @getimagesize( $file_path );

				$p_size = $tmp[0] . ' �� ' . $tmp[1];

				$p_view = $webftp->ConfirmImage( $file_url, $WSize="150", $HSize="150", $BorderSize=0, $IDName="", $vspace="0", $hspace="0" );
			}
		}


		{ // ����

			$f_kind = $webftp->ext_name[ $f_type ];
			if ( $webftp->chkSheet( $file_path, $webftp->app_ext_str ) == true ) $f_kind = $webftp->ext_name[ $path_parts['extension'] ];
		}
	}


	{ // �ּ� ����

		$urlcopyTagA = $urlcopyTagForm = '';

		if ( $webftp->chkSheet( $file_path, $webftp->app_ext_str ) == true ){

			$urlcopyTagA = '<A HREF="javascript:;" onclick ="urlCopyact( document.fm_url.link );"><img src="' . $img_path . 'webftp/bu_addcopy2.gif" border="0" align="absmiddle"></A><br>';
			$urlcopyTagForm = '<form name="fm_url"><input type="hidden" name="link" value="' . $file_url . '"></form>';
		}
	}
}
else {
	$notexistfile = 'Y';
}
?>


<html>
<head>
<title>Webftp Info</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="styleSheet" href="<?=$curr_path?>../../style.css">
<script src="<?=$curr_path?>../../common.js"></script>
<link rel="styleSheet" href="<?=$curr_path?>webftp.css">
<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>
</head>
<body bgcolor="#7D746E" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- ��ü���� : Start -->
<div class="allview"><a href="javascript:;" onclick="frame_list_dpath('/');"><font color="ffffff">���� �� ����</font></a></div>
<!-- ��ü���� : End -->

<? if ( $notexistfile != 'Y' ){ ?>

<!-- ���� �� ���� : Start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="7D746E">

<? if ( $p_view ){?>
  <tr>
    <td align="center" height="160"><?=$p_view?></td>
  </tr>
<? } ?>

  <tr>
    <td style="color:D8CDC5;line-height:16px;font:8pt tahoma;padding-left:10px;"><b><?=$filename?></b><br>
    Type : <?=$f_kind;?><br>
    Image Size : <?=$p_size;?><br>
    Size : <?=$f_size;?><br>
    Modified : <?=$f_time;?><br>
    Url : <?=$urlcopyTagA?><!-- <?=str_replace( "/", "/<nobr>", $file_url );?><br> -->
    <?=$urlcopyTagForm?>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
</table>
<!-- ���� �� ���� : End -->

<? } else { ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="7D746E">
  <tr>
    <td align="center" height="100" style="color:D8CDC5;font-weight:bold;">���ε�� �̹����� �����ϴ�.</td>
  </tr>
</table>

<? } ?>

</body>
</html>