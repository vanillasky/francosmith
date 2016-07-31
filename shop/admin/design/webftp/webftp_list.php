<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Webftp List
@��������/������/������:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';
@include_once dirname( __file__ ) . '/../../../lib/page.class.php';


## ��� ���� ���� : Start ---------------------------------------------------------------------
	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # ���� ����������

	if ( $_GET[ $tmp='page' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $_GET[ $tmp='srch_value' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $_GET[ $tmp='totSort' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $_GET[ $tmp='totViewnum' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];


	{ // ���� ����

		$strQry = '';
		if ( $srch_value ) $strQry .= 'WHERE name = ' . $srch_value;
	}


	{ // ���� ����

		if ( !$totSort ) $totSort = "name ASC";
		$totSort = stripSlashes( $totSort );
		$ExOrderby = explode( ' ', $totSort );

		switch ( $ExOrderby[0] ){

			default:
				$strQry .= " ORDER BY $totSort";
				break;
		}
	}


	$getList = $webftp->get_dirQuery( $nowPath, $strQry );


	{ // ����¡

		if( !$totViewnum ) $totViewnum = 15;
		$row_cnt		= count( $getList );				# �ڷ� �Ǽ� ����

		$pg = new Page( $page, $totViewnum );
		$pg->recode[total] = $row_cnt;
		$pg->page[url] = $_SERVER['PHP_SELF'];
		$pg->flag = $nextPath;
		$pg->exec();
		$pg->page[last] = $pg->page[num] * $pg->page[now];
		if ( $pg->recode[total] < $pg->page[last] ) $pg->page[last] = $pg->recode[total];
	}
## ---------------------------------------------------------------------------------------- End
## ������ �̵��� ������ �� ���� : Start -------------------------------------------------------
	$nextPath='';
	if ( $webftpid )		$nextPath.= '&webftpid=' . $webftpid;			# Ŭ����ID
	if ( $srch_value )		$nextPath.= '&srch_value=' . $srch_value;		# Ű����˻�
	if ( $totSort )			$nextPath.= '&totSort=' . $totSort;		 		# ����
	if ( $totViewnum )		$nextPath.= '&totViewnum=' . $totViewnum;		# ��°���
## ---------------------------------------------------------------------------------------- End
?>


<html>
<head>
<title>Webftp List</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="styleSheet" href="<?=$curr_path?>../../style.css">
<link rel="styleSheet" href="<?=$curr_path?>webftp.css">
<script src="../../cssRound.js"></script>
<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>
</head>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="resizeFrame();">

<table width="100%" border="0" cellpadding="0" cellspacing="0" id="info">
  <caption>&#149;&nbsp;&nbsp;�� <b><?=number_format( $row_cnt )?></b> �� &nbsp;&nbsp; &#149 ������ : <b><?=$_COOKIE['dPath']?></b></caption>
  <tr>
    <th>
      <FORM METHOD=get ACTION="">
      <input type="hidden" name="webftpid" value="<?=$webftpid?>">
      <input type="hidden" name="srch_value" value="<?=$srch_value?>">
      <select name="totSort" onchange="this.form.submit();">
        <option value="name DESC"<?if ( $totSort == "name DESC" ) echo ' selected';?>>- �̸� ���ġ�</option>
        <option value="name ASC"<?if ( $totSort == "name ASC" ) echo ' selected';?>>- �̸� ���ġ�</option>
        <option value="size DESC"<?if ( $totSort == "size DESC" ) echo ' selected';?>>- ũ�� ���ġ�</option>
        <option value="size ASC"<?if ( $totSort == "size ASC" ) echo ' selected';?>>- ũ�� ���ġ�</option>
        <option value="date DESC"<?if ( $totSort == "date DESC" ) echo ' selected';?>>- ��¥ ���ġ�</option>
        <option value="date ASC"<?if ( $totSort == "date ASC" ) echo ' selected';?>>- ��¥ ���ġ�</option>
      </select>
      <select name="totViewnum" onchange="this.form.submit();">
        <option value="10"<?if ( $totViewnum == 10 ) echo ' selected';?>>10</option>
        <option value="15"<?if ( $totViewnum == 15 ) echo ' selected';?>>15</option>
        <option value="20"<?if ( $totViewnum == 20 ) echo ' selected';?>>20</option>
        <option value="30"<?if ( $totViewnum == 30 ) echo ' selected';?>>30</option>
        <option value="50"<?if ( $totViewnum == 50 ) echo ' selected';?>>50</option>
        <option value="100"<?if ( $totViewnum == 100 ) echo ' selected';?>>100</option>
        <option value="150"<?if ( $totViewnum == 150 ) echo ' selected';?>>150</option>
        <option value="200"<?if ( $totViewnum == 200 ) echo ' selected';?>>200</option>
        <option value="250"<?if ( $totViewnum == 250 ) echo ' selected';?>>250</option>
      </select>
      </FORM>
    </th>
  </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" id="list">
<FORM METHOD=POST ACTION="" name="WFlist">
  <tr>
	<td colspan="7" class="Round"></td>
  </tr>
  <tr>
	<th nowrap width="50"><span class="noline"><input type="checkbox" onclick="javascript:PubAllSordes( ( this.checked ? 'select' : 'deselect' ), WFlist['sepchkbox[]'] );"></span></th>
	<th>���ϸ�</th>
	<th nowrap width="70">ũ��</th>
	<th nowrap width="60">���Ϲޱ�</th>
	<th nowrap width="60">�ּҺ���</th>
	<th nowrap width="120">������������¥</th>
	<th nowrap width="80">�׸�ũ��</th>
  </TR>
  <tr>
    <td colspan="7" class="Round"></td>
  </tr>
  <tr>
    <td nowrap class="Parent" colspan="7"><img src="<?=$img_path?>webftp/up.gif" align=absmiddle border="0">&nbsp;<a href="javascript:;" onclick="dPathCookie( get_nowhighDir() );">..</a></td>
  </tr>
  <tr>
    <td colspan="7" class="Lines"></td>
  </tr>

<?
for ( $i = $pg->recode[start]; $i < $pg->page[last]; $i++ ){

	$arr = $getList[$i];

	$arr['type']		= $arr['type'];	# Ÿ��( dir / file )
	$arr['name']		= $arr['name'];	# ���ϸ�
	$arr['size']		= $arr['size'];	# ũ��
	$arr['date']		= $arr['date'];	# ������
	$arr['file_path']	= $nowPath . $arr['name']; # FTP ROOT ���
	$arr['file_url']	= $webftp->ftp_url . $_COOKIE['dPath'] . $arr['name']; # FTP HOME ���
	$arr['checkboxTag'] = '<span class="noline"><input type="checkbox" name="sepchkbox[]" value="' . $arr['name'] . '"></span>'; # üũ�ڽ�


	{ // ��������

		$path_parts = @pathinfo( $arr['file_path'] );
		$path_parts['extension'] = strtolower( $path_parts['extension'] );

		if ( $arr['date'] != "" ) $arr['date'] = date( 'y-m-d H:i:s', $arr['date'] ); # ��¥

		if ( $arr['type'] == 'file' ){ // ����ũ��

			if ( $arr['size'] > 1024 ) $arr['size'] = round( $arr['size'] / 1024, 2 ) . ' Kb';	# KB
			else $arr['size'] = $arr['size'] . ' Byte';	# B
		}
		else $arr['size'] = '';

		if ( $webftp->chkSheet( $arr['file_path'], $webftp->img_ext_str ) == true ){ // �׸�ũ��

			$tmp = @getimagesize( $arr['file_path'] );
			$arr['p_size'] = $tmp[0] . ' �� ' . $tmp[1];
			$img_extension = 'not';
			if ($tmp[2] == 1){
				$img_extension = 'gif';
			} else if ($tmp[2] == 2){
				$img_extension = 'jpg';
			} else if ($tmp[2] == 3){
				$img_extension = 'png';
			} else if ($tmp[2] == 4){
				$img_extension = 'swf';
			} else if ($tmp[2] == 5){
				$img_extension = 'psd';
			} else if ($tmp[2] == 6){
				$img_extension = 'bmp';
			}
		}
	}


	if ( $arr['type'] == 'file' ){ // ���� �ޱ�

		$idx++;
		$arr['downTag'] = '<A HREF="' . $curr_path . 'webftp_download.php?webftpid=' . $webftpid . '&filename='. urlencode( $_COOKIE['dPath'] . $arr['name'] ) .'"><img src="' . $img_path . 'webftp/bu_file.gif" border="0" align="absmiddle"></A>';
	}


	if ( $webftp->chkSheet( $arr['file_path'], $webftp->app_ext_str ) == true ){ // �ּ� ����

		$idx++;
		$arr['urlcopyTag'] = '<A HREF="javascript:;" onclick ="urlCopyact( document.WFlist.link' . $idx . ' );"><img src="' . $img_path . 'webftp/bu_addcopy.gif" border="0" align="absmiddle"></A><input type="hidden" name="link' . $idx . '" value="' . $arr['file_url'] . '">';
	}


	{ // ������

		if ( $arr['type'] == 'dir' ) $arr['iconKind'] = 'dir';
		else if ( $webftp->chkSheet( $arr['file_path'], $webftp->img_ext_str ) == true ) $arr['iconKind'] = $img_extension;
		else $arr['iconKind'] = 'not';

		$arr['fileIconTag'] = '<img src="' . $img_path . 'webftp/' . $arr['iconKind'] . '.gif" align=absmiddle border="0">&nbsp;&nbsp;';
	}



	{ // ���ϸ� ��ũ

		$arr['fileLinkTag'] = $arr['name'];

		if ( $arr['type'] == 'dir' ) $arr['fileLinkTag'] = '<a href="javascript:;" onclick ="dPathCookie( \'' . $_COOKIE['dPath'] . $arr['name'] . '/\' );">' . $arr['fileLinkTag'];
		else $arr['fileLinkTag'] = '<a href="javascript:;" onclick ="frame_info( \'' . $_COOKIE['dPath'] . $arr['name'] . '\' );">' . $arr['fileLinkTag'];
	}
?>

  <tr>
    <td nowrap><?=$arr['checkboxTag'];?></td>
    <td style="text-align:left"><?=$arr['fileIconTag'];?><?=$arr['fileLinkTag'];?></a></td>
    <td nowrap style="text-align:right"><?=$arr['size'];?>&nbsp;&nbsp;&nbsp;</td>
    <td nowrap><?=$arr['downTag'];?></td>
    <td nowrap><?=$arr['urlcopyTag'];?></td>
    <td nowrap>20<?=$arr['date'];?></td>
    <td nowrap><?=$arr['p_size'];?></td>
  </tr>

<?
}


if ( !$row_cnt ){
?>

  <tr>
    <td colspan="7" class="Empty">������ �������� �ʽ��ϴ�.</td>
  </tr>

<?
}
?>

  <tr>
    <td colspan="7" class="Lines"></td>
  </tr>
</FORM>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>


<SCRIPT LANGUAGE="JavaScript">
<!--
/*-------------------------------------
 ���� ����/���� ����
-------------------------------------*/
function proc_Fin(){

	var FObj = document.WFlist;

	if (!FObj['sepchkbox[]'] || PubChkSelect( FObj['sepchkbox[]'] ) == false ){

		alert("�����Ͻ� ����/������ �����Ͽ� �ֽʽÿ�.");
		return false;
	}

 	var win = Pubwinopen_return( '', 'win_del', 500, 500, 10, 10, 1 );

	FObj.action = curr_path + 'webftp_delete.php?webftpid=' + webftpid;
	FObj.target = 'win_del';

	win.focus();
	FObj.submit();

	FObj.action = '';
	FObj.target = '';
}



/*-------------------------------------
 ���� ����/���� ����
-------------------------------------*/
function proc_Mod(){

	var FObj = document.WFlist;

	if ( PubChkSelect( FObj['sepchkbox[]'] ) == false ){

		alert("�����Ͻ� ����/������ �����Ͽ� �ֽʽÿ�.");
		return false;
	}

	if ( PubChkSelectNum( FObj['sepchkbox[]'] ) > 1 ){

		alert("����/���� ����� �ϳ��� �����Ͽ� �ֽʽÿ�.");
		return false;
	}

 	var win = Pubwinopen_return( '', 'win_mod', 500, 500, 10, 10, 0 );

	FObj.action = curr_path + 'webftp_modify.php?webftpid=' + webftpid;
	FObj.target = 'win_mod';

	win.focus();
	FObj.submit();

	FObj.action = '';
	FObj.target = '';
}
//-->
</SCRIPT>



<SCRIPT LANGUAGE="JavaScript">
<!--
function resizeFrame(){

	var oBody = document.body;
	var oFrame = parent.document.getElementById("folder_frame");
	var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;

}
//-->
</SCRIPT>


<!-- �̹��� ���� ������ üũ -->

<div style="padding-left:15">�� <font class=small><font color=EA0095>��ǰ������</font>�� ���� �̹������� <font color=EA0095>data/editor</font> ������ �ֽ��ϴ�. �̰��� ���� üũ�ϼ���.</font></div>
<div style="padding-left:15">�� <font class=small><font color=EA0095>data/editor</font> ������ ���� ����Ʈ �����  '<font color=EA0095>ũ�� ���ġ�</font>' �� �����ϰ�, �ʿ���� �̹������� Ȯ�� �� �����ϼ���.</font></div>


<? $ftpsize = getDu('disks'); ?>

<table border=0 cellpadding=0 cellspacing=0>
<tr><td style="padding-left:15px">�� <font class=small color=333333>������ ���� ���� ��� �� �뷮�� <font class=ver8 color=#FF5A00><b><?=byte2str(array_sum($ftpsize))?></b></font>�Դϴ�.</td></tr>
<tr><td height=4></td></tr>
</table>

<? if ($godo[webCode]!="webhost_outside"){ ?>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td style="padding-left:15px">
<iframe src="./3DBar_img.php" width="300" height="30" frameborder=0 marginwidth=0 marginheight=0 scrolling=no></iframe>
</td></tr></table>
<? } ?>

<div style="padding-top:5px"></div>

<table border=0 cellpadding=0 cellspacing=0>
<tr><td style="padding-left:15px">

<table border=1 bordercolor=#C3C2C2 style="border-collapse:collapse" cellpadding=3 cellspacing=0>
<tr>
	<td width=100 height=23 align=center bgcolor=white><font class=ver8><font class=ver8><b><?=$k='editor'?></b></td>
	<td width=70 align=center bgcolor=white><font class=ver8><?=byte2str($ftpsize[$k])?></td>
</tr>
<tr>
	<td width=100 height=23 align=center bgcolor=white><font class=ver8><font class=ver8><b><?=$k='board'?></b></td>
	<td width=70 align=center bgcolor=white><font class=ver8><?=byte2str($ftpsize[$k])?></td>
</tr>
<tr>
	<td width=100 height=23 align=center bgcolor=white><font class=ver8><font class=ver8><b><?=$k='goods'?></b></td>
	<td width=70 align=center bgcolor=white><font class=ver8><?=byte2str($ftpsize[$k])?></td>
</tr>
<tr>
	<td width=100 height=23 align=center bgcolor=white><font class=ver8><font class=ver8><b><?=$k='skin'?></b></td>
	<td width=70 align=center bgcolor=white><font class=ver8><?=byte2str($ftpsize[$k])?></td>
</tr>
<tr bgcolor=#F1F1F1>
	<td height=25 align=center><font class=small>�� �뷮</td>
	<td align=center><font class=ver8 color=#FF5A00><b><?=byte2str(getDu('disk'))?></b></td>
</table>
</td>

<td valign=top bgcolor=white>
<table border=1 bordercolor=#C3C2C2 style="border-collapse:collapse"  cellpadding=3 cellspacing=0>
<tr><td height=23 width=310 style="padding-left:5px"><font class=small1 color=444444>��ǰ������ �� �����͸� ���� ��ϵ� �̹��� �� ���Ͽ뷮</td></tr>
<tr><td height=23 style="padding-left:5px"><font class=small1 color=444444>�Խ����� ���� ��ϵ� �̹��� �� ���Ͽ뷮</td></tr>
<tr><td height=23 style="padding-left:5px"><font class=small1 color=444444>���� ��ϵ� ��ǰ�̹����� �� �뷮</td></tr>
<tr><td height=23 style="padding-left:5px"><font class=small1 color=444444>������ ���� �̹��� �� ��Ÿ �̹������� �뷮</td></tr>
<tr><td height=25 style="padding-left:5px"><font class=small1 color=444444>���� �̿��ϰ� ��� �̹��� �� ������ �� �뷮�Դϴ�</td></tr>
</table>
</td></tr></table>


<div style="padding-left:15px">
<table cellpadding=1 cellspacing=0 border=0 class=small_tip width=97%>
<tr><td height=10></td></tr>
<tr><td><font class=small color=333333>* ���ǻ���</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle">���� �̹��� ���� <font class=ver8 color=EA0095>(jpg, gif, swf</font><font color=EA0095> �� ����)</font> �� ���/����/���� ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>�̹��������� �뷮�� <?=ini_get('upload_max_filesize')?>B������</font> ����� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>bmp ����</font>�� ȭ���� ���� �뷮�� ����ġ�� ũ�Ƿ� <font color=EA0095>jpg�� gif�� �����Ͻ� �� ���</font>�ϼ���.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>��ǰ������</font>�� ���� �̹������� <font color=EA0095>data/editor</font> ������ �ֽ��ϴ�. �̰��� ���� üũ�ϼ���.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>data/editor</font> ������ ���� '<font color=EA0095>ũ�� ���ġ�</font>' �� ������ ����, �ʿ���� �̹������� Ȯ���Ͽ� �����ϼ���.</td></tr>
</table>


<table cellpadding=1 cellspacing=0 border=0 class=small_tip width=97%>
<tr><td height=10></td></tr>
<tr><td bgcolor=BBBBBB></td></tr>
<tr><td height=10></td></tr>

<tr><td><font class=small color=333333>* ��ɼ���</td></tr>


<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>���Ͽø���</font>: ���������� �� ������ �� ������ �ø�����.</td></tr>
<!--tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>�ڵ����</font>: �Ѱ��� ��ǰ�����̹����� ����ϸ� �������� �̹����� �����������Ǿ� �ڵ���ϵǴ� ����Դϴ�.</td></tr-->
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>������</font>: ���ο� ������ �����մϴ�.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>�̸��ٲٱ�</font>: �����̸��� �ٲٰ��� �Ҷ� ������ ������ �� �̸��� �ٲߴϴ�.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>�����ϱ�</font>: ������ ����/������ �����ϰ� �����մϴ�. ������ �����ϸ� �������� ���ϵ� �����ǹǷ� �����ϼ���.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>���ϸ��� Ŭ���ϸ� ������ ������</font>�� �������ϴ�.</td></tr>
</table>
</div>

</body>
</html>