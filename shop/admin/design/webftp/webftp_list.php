<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp List
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

@include_once dirname( __file__ ) . '/conf.php';
@include_once dirname( __file__ ) . '/../../../lib/page.class.php';


## 목록 쿼리 실행 : Start ---------------------------------------------------------------------
	$nowPath = $webftp->ftp_path . $_COOKIE['dPath']; # 현재 서버절대경로

	if ( $_GET[ $tmp='page' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $_GET[ $tmp='srch_value' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $_GET[ $tmp='totSort' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];
	if ( $_GET[ $tmp='totViewnum' ] ) ${$tmp} = $_GET[$tmp]; else if ( $_POST[$tmp] ) ${$tmp} = $_POST[$tmp];


	{ // 조건 쿼리

		$strQry = '';
		if ( $srch_value ) $strQry .= 'WHERE name = ' . $srch_value;
	}


	{ // 정렬 쿼리

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


	{ // 페이징

		if( !$totViewnum ) $totViewnum = 15;
		$row_cnt		= count( $getList );				# 자료 건수 대입

		$pg = new Page( $page, $totViewnum );
		$pg->recode[total] = $row_cnt;
		$pg->page[url] = $_SERVER['PHP_SELF'];
		$pg->flag = $nextPath;
		$pg->exec();
		$pg->page[last] = $pg->page[num] * $pg->page[now];
		if ( $pg->recode[total] < $pg->page[last] ) $pg->page[last] = $pg->recode[total];
	}
## ---------------------------------------------------------------------------------------- End
## 페이지 이동시 전송할 값 셋팅 : Start -------------------------------------------------------
	$nextPath='';
	if ( $webftpid )		$nextPath.= '&webftpid=' . $webftpid;			# 클래스ID
	if ( $srch_value )		$nextPath.= '&srch_value=' . $srch_value;		# 키워드검색
	if ( $totSort )			$nextPath.= '&totSort=' . $totSort;		 		# 정렬
	if ( $totViewnum )		$nextPath.= '&totViewnum=' . $totViewnum;		# 출력갯수
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
  <caption>&#149;&nbsp;&nbsp;총 <b><?=number_format( $row_cnt )?></b> 건 &nbsp;&nbsp; &#149 현재경로 : <b><?=$_COOKIE['dPath']?></b></caption>
  <tr>
    <th>
      <FORM METHOD=get ACTION="">
      <input type="hidden" name="webftpid" value="<?=$webftpid?>">
      <input type="hidden" name="srch_value" value="<?=$srch_value?>">
      <select name="totSort" onchange="this.form.submit();">
        <option value="name DESC"<?if ( $totSort == "name DESC" ) echo ' selected';?>>- 이름 정렬↑</option>
        <option value="name ASC"<?if ( $totSort == "name ASC" ) echo ' selected';?>>- 이름 정렬↓</option>
        <option value="size DESC"<?if ( $totSort == "size DESC" ) echo ' selected';?>>- 크기 정렬↑</option>
        <option value="size ASC"<?if ( $totSort == "size ASC" ) echo ' selected';?>>- 크기 정렬↓</option>
        <option value="date DESC"<?if ( $totSort == "date DESC" ) echo ' selected';?>>- 날짜 정렬↑</option>
        <option value="date ASC"<?if ( $totSort == "date ASC" ) echo ' selected';?>>- 날짜 정렬↓</option>
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
	<th>파일명</th>
	<th nowrap width="70">크기</th>
	<th nowrap width="60">파일받기</th>
	<th nowrap width="60">주소복사</th>
	<th nowrap width="120">마지막수정날짜</th>
	<th nowrap width="80">그림크기</th>
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

	$arr['type']		= $arr['type'];	# 타입( dir / file )
	$arr['name']		= $arr['name'];	# 파일명
	$arr['size']		= $arr['size'];	# 크기
	$arr['date']		= $arr['date'];	# 수정일
	$arr['file_path']	= $nowPath . $arr['name']; # FTP ROOT 경로
	$arr['file_url']	= $webftp->ftp_url . $_COOKIE['dPath'] . $arr['name']; # FTP HOME 경로
	$arr['checkboxTag'] = '<span class="noline"><input type="checkbox" name="sepchkbox[]" value="' . $arr['name'] . '"></span>'; # 체크박스


	{ // 파일정보

		$path_parts = @pathinfo( $arr['file_path'] );
		$path_parts['extension'] = strtolower( $path_parts['extension'] );

		if ( $arr['date'] != "" ) $arr['date'] = date( 'y-m-d H:i:s', $arr['date'] ); # 날짜

		if ( $arr['type'] == 'file' ){ // 파일크기

			if ( $arr['size'] > 1024 ) $arr['size'] = round( $arr['size'] / 1024, 2 ) . ' Kb';	# KB
			else $arr['size'] = $arr['size'] . ' Byte';	# B
		}
		else $arr['size'] = '';

		if ( $webftp->chkSheet( $arr['file_path'], $webftp->img_ext_str ) == true ){ // 그림크기

			$tmp = @getimagesize( $arr['file_path'] );
			$arr['p_size'] = $tmp[0] . ' × ' . $tmp[1];
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


	if ( $arr['type'] == 'file' ){ // 파일 받기

		$idx++;
		$arr['downTag'] = '<A HREF="' . $curr_path . 'webftp_download.php?webftpid=' . $webftpid . '&filename='. urlencode( $_COOKIE['dPath'] . $arr['name'] ) .'"><img src="' . $img_path . 'webftp/bu_file.gif" border="0" align="absmiddle"></A>';
	}


	if ( $webftp->chkSheet( $arr['file_path'], $webftp->app_ext_str ) == true ){ // 주소 복사

		$idx++;
		$arr['urlcopyTag'] = '<A HREF="javascript:;" onclick ="urlCopyact( document.WFlist.link' . $idx . ' );"><img src="' . $img_path . 'webftp/bu_addcopy.gif" border="0" align="absmiddle"></A><input type="hidden" name="link' . $idx . '" value="' . $arr['file_url'] . '">';
	}


	{ // 아이콘

		if ( $arr['type'] == 'dir' ) $arr['iconKind'] = 'dir';
		else if ( $webftp->chkSheet( $arr['file_path'], $webftp->img_ext_str ) == true ) $arr['iconKind'] = $img_extension;
		else $arr['iconKind'] = 'not';

		$arr['fileIconTag'] = '<img src="' . $img_path . 'webftp/' . $arr['iconKind'] . '.gif" align=absmiddle border="0">&nbsp;&nbsp;';
	}



	{ // 파일명 링크

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
    <td colspan="7" class="Empty">파일이 존재하지 않습니다.</td>
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
 선택 폴더/파일 삭제
-------------------------------------*/
function proc_Fin(){

	var FObj = document.WFlist;

	if (!FObj['sepchkbox[]'] || PubChkSelect( FObj['sepchkbox[]'] ) == false ){

		alert("삭제하실 폴더/파일을 선택하여 주십시요.");
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
 선택 폴더/파일 변경
-------------------------------------*/
function proc_Mod(){

	var FObj = document.WFlist;

	if ( PubChkSelect( FObj['sepchkbox[]'] ) == false ){

		alert("변경하실 폴더/파일을 선택하여 주십시요.");
		return false;
	}

	if ( PubChkSelectNum( FObj['sepchkbox[]'] ) > 1 ){

		alert("폴더/파일 변경시 하나만 선택하여 주십시요.");
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


<!-- 이미지 폴더 사이즈 체크 -->

<div style="padding-left:15">※ <font class=small><font color=EA0095>상품상세정보</font>에 들어가는 이미지들은 <font color=EA0095>data/editor</font> 폴더에 있습니다. 이곳을 자주 체크하세요.</font></div>
<div style="padding-left:15">※ <font class=small><font color=EA0095>data/editor</font> 폴더를 열어 리스트 상단의  '<font color=EA0095>크기 정렬↑</font>' 로 선택하고, 필요없는 이미지들을 확인 후 삭제하세요.</font></div>


<? $ftpsize = getDu('disks'); ?>

<table border=0 cellpadding=0 cellspacing=0>
<tr><td style="padding-left:15px">※ <font class=small color=333333>고객님이 현재 쓰고 계신 총 용량은 <font class=ver8 color=#FF5A00><b><?=byte2str(array_sum($ftpsize))?></b></font>입니다.</td></tr>
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
	<td height=25 align=center><font class=small>총 용량</td>
	<td align=center><font class=ver8 color=#FF5A00><b><?=byte2str(getDu('disk'))?></b></td>
</table>
</td>

<td valign=top bgcolor=white>
<table border=1 bordercolor=#C3C2C2 style="border-collapse:collapse"  cellpadding=3 cellspacing=0>
<tr><td height=23 width=310 style="padding-left:5px"><font class=small1 color=444444>상품상세정보 등 에디터를 통해 등록된 이미지 및 파일용량</td></tr>
<tr><td height=23 style="padding-left:5px"><font class=small1 color=444444>게시판을 통해 등록된 이미지 및 파일용량</td></tr>
<tr><td height=23 style="padding-left:5px"><font class=small1 color=444444>현재 등록된 상품이미지의 총 용량</td></tr>
<tr><td height=23 style="padding-left:5px"><font class=small1 color=444444>디자인 관련 이미지 및 기타 이미지들의 용량</td></tr>
<tr><td height=25 style="padding-left:5px"><font class=small1 color=444444>현재 이용하고 계신 이미지 및 파일의 총 용량입니다</td></tr>
</table>
</td></tr></table>


<div style="padding-left:15px">
<table cellpadding=1 cellspacing=0 border=0 class=small_tip width=97%>
<tr><td height=10></td></tr>
<tr><td><font class=small color=333333>* 유의사항</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle">각종 이미지 파일 <font class=ver8 color=EA0095>(jpg, gif, swf</font><font color=EA0095> 만 가능)</font> 을 등록/복사/삭제 관리 할 수 있습니다.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>이미지파일의 용량은 <?=ini_get('upload_max_filesize')?>B까지만</font> 등록할 수 있습니다.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>bmp 파일</font>은 화질에 비해 용량이 지나치게 크므로 <font color=EA0095>jpg나 gif로 변경하신 후 사용</font>하세요.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>상품상세정보</font>에 들어가는 이미지들은 <font color=EA0095>data/editor</font> 폴더에 있습니다. 이곳을 자주 체크하세요.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=EA0095>data/editor</font> 폴더를 열어 '<font color=EA0095>크기 정렬↑</font>' 로 정렬한 다음, 필요없는 이미지들은 확인하여 삭제하세요.</td></tr>
</table>


<table cellpadding=1 cellspacing=0 border=0 class=small_tip width=97%>
<tr><td height=10></td></tr>
<tr><td bgcolor=BBBBBB></td></tr>
<tr><td height=10></td></tr>

<tr><td><font class=small color=333333>* 기능설명</td></tr>


<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>파일올리기</font>: 저장폴더를 잘 선택한 후 파일을 올리세요.</td></tr>
<!--tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>자동등록</font>: 한개의 상품원본이미지만 등록하면 여러개의 이미지가 사이즈조정되어 자동등록되는 기능입니다.</td></tr-->
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>새폴더</font>: 새로운 폴더를 생성합니다.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>이름바꾸기</font>: 파일이름을 바꾸고자 할때 파일을 선택한 후 이름을 바꿉니다.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>삭제하기</font>: 삭제할 파일/폴더를 선택하고 삭제합니다. 폴더를 삭제하면 폴더안의 파일도 삭제되므로 유의하세요.</td></tr>
<tr><td><img src="../../img/icon_list.gif" align="absmiddle"><font color=0074BA>파일명을 클릭하면 파일의 상세정보</font>가 보여집니다.</td></tr>
</table>
</div>

</body>
</html>