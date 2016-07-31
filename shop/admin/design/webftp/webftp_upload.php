<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Upload
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
<title>Webftp Upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="styleSheet" href="<?=$curr_path?>../../style.css">
<link rel="styleSheet" href="<?=$curr_path?>webftp.css">
<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>
</head>
<iframe name="ifrmHidden" src="../../../blank.txt" style="display:none"></iframe>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
<!-- 타이틀 : Start -->
<tr>
	<td class="table_PopTitle"><img src="<?=$img_path?>webftp/pop_titlebar_up.gif" align="absmiddle"></td>
</tr>
<!-- 타이틀 : End -->

<!-- 설정폼 : Start -->
<tr>
	<td valign="top" align="center" style="padding:14px">
	<? include "../../proc/warning_disk_msg.php"; # not_delete  ?>

	<table class="table_Basic1" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<form>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="title_Sub1"><img src="<?=$img_path?>webftp/top_titledot1.gif" align="absmiddle" border="0">파일 올리기</td>
			<td class="title_SubRight1">
			행 추가 <select name="fileUp_plus">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="10">10</option>
			<option value="15">15</option>
			<option value="20">20</option>
			</select> <input type="button" value="생성" onclick="space_add( this.form.fileUp_plus.value, 'fileUpBox,directimg' );">
			</td>
		</tr>
		<tr>
			<td style="padding-left:25px;" colspan="2">(확장자 <?=str_replace( ";", " ", $webftp->app_ext_str );?>)</td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" id="fileUpBox">
		<tr>
			<td colspan="2" class="table_TLine1"></td>
		</tr>
		<tr>
			<td colspan="2" height="10"></td>
		</tr>
		<tr>
			<td class="table_Left1">이미지 1</td>
			<td class="table_Right1">
				<form method="post" enctype="multipart/form-data">
				<input type="hidden" name="act" value="handling">
				<input TYPE="file" name="directimg" size="70%" class="Line">
				</form>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="2"></td>
		</tr>
		<tr>
			<td class="table_BLine1" colspan="2"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center" class="noline"><img src="<?=$img_path?>webftp/pop_bu_register.gif" border="0" align="absmiddle" alt="[등록]" onclick="update_start();" style="cursor:pointer;"></td>
	</tr>
	</table>

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
	</div>
	</td>
</tr>
<!-- 설정폼 : End -->


<!-- 클로즈 : Start -->
<tr>
	<td class="table_PopCloseOut1"><div class="table_PopCloseOut2"><a href="javascript:parent.close();"><img src="<?=$img_path?>webftp/pop_closebu.gif" alt="Closw Window" border="0" align="absmiddle"></a></div></td>
</tr>
<!-- 클로즈 : End -->
</table>


<script type="text/javascript">
<!--
/*-------------------------------------
 실행 체크
-------------------------------------*/
var forms;
var count = 1;
var limit = 0;
var today = new Date();
var expire_date = new Date(today.getTime() - 60*60*24*1000);
function update_start()
{
	forms = document.getElementsByTagName('form');
	count = 1;
	limit = forms.length;
	var directimg = 0;
	for ( var i = 1; i < limit; i++ ){
		if ( forms[i]['directimg'].value != '' ){
			directimg++;
		}
	}
	if ( directimg == 0 ){
		alert( "이미지를 선택하셔야 합니다." );
		return;
	}
	update_send();
}
function update_send()
{
	while (forms[count] != null && forms[count]['directimg'].value == '') {
		forms[count]['directimg'].style.backgroundColor = '#dddddd';
		forms[count]['directimg'].disabled = true;
		count++;
	}
	if (forms[count] != null && forms[count]['directimg'].value != '') {
		forms[count].action="webftp_upload_indb.php?webftpid=<?=$webftpid?>&count="+count;
		forms[count].target="ifrmHidden";
		forms[count].submit();
	}
	else if (count >= limit) {
		update_end();
	}
}
function update_sended()
{
	setCookie( name='directimg_rewrite_'+count, value='', expires=expire_date, path='/' );
	forms[count]['directimg'].style.backgroundColor = '#dddddd';
	forms[count]['directimg'].disabled = true;
	count++;
	update_send();
}
function update_rewrite()
{
	var directimg_rewrite_all = getCookie('directimg_rewrite_all');
	if ( directimg_rewrite_all == null ){
		var rewrite = confirm("파일이 이미 존재합니다. 덮어씌우시겠습니까?");
		if (count+1 < limit){
			if (rewrite){
				if (confirm("동일한 파일이 존재합니다. 모두 덮어씌우시겠습니까?")){
					setCookie( name='directimg_rewrite_all', value='Y', expires='', path='/' );
				}
				else {
					setCookie( name='directimg_rewrite_all', value='', expires=expire_date, path='/' );
				}
			}
			else {
				if (confirm("동일한 파일이 존재합니다. 모두 건너띄우시겠습니까?")){
					setCookie( name='directimg_rewrite_all', value='N', expires='', path='/' );
				}
				else {
					setCookie( name='directimg_rewrite_all', value='', expires=expire_date, path='/' );
				}
			}
		}
	}
	else {
		var rewrite = (directimg_rewrite_all == 'Y' ? true : false);
	}

	if ( rewrite ) {
		setCookie( name='directimg_rewrite_'+count, value='Y', expires='', path='/' );
		update_send();
	}
	else {
		update_sended();
	}
}
function update_end()
{
	setCookie( name='directimg_rewrite_all', value='', expires=expire_date, path='/' );
	alert( "완료되었습니다." );
	opener.window.top.folder_frame.location.reload();
	opener.window.top.global_frame.location.reload();
	window.close();
}
//-->
</script>



<script type="text/javascript">
<!--
/*-------------------------------------
	이미지 UP Load 행 추가
-------------------------------------*/
function space_add( plus, e_nm ){

	var tmp			= e_nm.split( ',' );

	var TableObj	= eval( 'document.getElementById("' + tmp[0] + '")' );
	var f_ele		= eval( 'document.getElementsByName("' + tmp[1] + '")' );

	if ( !plus ){

		alert( "추가할 행 수를 기입하세요" );
		return;
	}


	for ( i = 0; i < plus; i++ ){

		{ // 번호

			if ( !f_ele ) var no = 1;
			else {

				var no = f_ele.length;
				if ( no == undefined ) no = 1;
				no += 1;
			}

			var realTag = '이미지 ' + no;
		}


		{ // 경계선 출력

			newTr = TableObj.insertRow( TableObj.rows.length - 2 );
			newTd = newTr.insertCell( 0 );
			newTd.className='table_Line1';
			newTd = newTr.insertCell( 0 );
			newTd.className='table_Line1';
		}


		{ // 본문 라인 출력

			newTr = TableObj.insertRow( TableObj.rows.length - 2 );
			newTd = newTr.insertCell( 0 );
			newTd.className='table_Left1';
			newTd.innerHTML = realTag;

			newTd = newTr.insertCell( 1 );
			newTd.className='table_Right1';
			newTd.innerHTML = '\
				<form method="post" enctype="multipart/form-data">\
				<input type="hidden" name="act" value="handling">\
				<input TYPE="file" name="directimg" size="70%" class="Line">\
				</form>\
				';
		}

		var f_ele = eval( 'document.getElementsByName("' + tmp[1] + '")' );
	}
}
//-->
</script>


<script type="text/javascript" src="../../proc/warning_disk_js.php"><!-- not_delete --></script>
</body>
</html>