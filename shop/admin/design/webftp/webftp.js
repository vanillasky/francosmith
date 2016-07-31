/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Javascript Function 설정
@수정내용/수정자/수정일:

* function 간의 간격 : 3 line
* 내부블럭( 피제어부 ) 들여쓰기 : 2 column
------------------------------------------------------------------------------*/



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 공용 - 숫자 입력 체크
-------------------------------------*/
function onlyNumber(){

	if ( ( event.keyCode == 13 ) || ( event.keyCode == 190 ) || ( event.keyCode >= 96 && event.keyCode <= 105 ) || ( event.keyCode == 110 ) || ( event.keyCode > 47 && event.keyCode < 58 ) || event.keyCode == 8 || event.keyCode == 16 || event.keyCode == 116 || event.keyCode == 18 || event.keyCode == 9 || ( event.keyCode >= 37 && event.keyCode <= 40 ) || event.keyCode == 46 );
	else event.returnValue = false;
}



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 공용 - 체크박스 체크
 ckFlag : select, reflect, deselect
 CObj : checkbox object
-------------------------------------*/
function PubAllSordes( ckFlag, CObj ){

	if ( !CObj ) return;
	var ckN = CObj.length;

	if ( ckN != null ){

		if ( ckFlag == "select" ){

			var sett = 0;
			for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){
				CObj[jumpchk].checked = true;
			}
		}
		else if ( ckFlag=="reflect" ){

			var sett = 0;
			for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){

				if ( CObj[jumpchk].checked == false ) CObj[jumpchk].checked = true;
				else	CObj[jumpchk].checked = false;
			}
		}
		else{
			var sett = 0;
			for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){
				CObj[jumpchk].checked = false;
			}
		}
	}
	else {

		if ( ckFlag == "select" ){

			CObj.checked = true;
		}
		else if ( ckFlag == "reflect" ){

			var sett = 0;
			if ( CObj.checked == false ) CObj.checked = true;
			else CObj.checked = false;
		}
		else{

			var sett = 0;
			CObj.checked = false;
		}
	}
}



/*-------------------------------------
 공용 - 체크박스 한개이상 체크여부
 CObj : checkbox object
-------------------------------------*/
function PubChkSelect( CObj ){

	if ( !CObj ) return;
	var ckN = CObj.length;

	if ( ckN != null ){

		var sett = 0;
		for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){

			if ( CObj[jumpchk].checked == false ){
				sett++;
			}
		}

		if ( sett == ckN ) return false;
		else return true;
	}
	else{

		if ( CObj.checked == true ) return true;
		else return false;
	}
}



/*-------------------------------------
 공용 - 체크박스 체크갯수
 CObj : checkbox object
-------------------------------------*/
function PubChkSelectNum( CObj ){

	if ( !CObj ) return 0;
	var ckN = CObj.length;

	if ( ckN != null ){

		var sett = 0;
		for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){

			if ( CObj[jumpchk].checked == false ){
				sett++;
			}
		}

		if ( sett == ckN ) return 0;
		else return ( ckN - sett );
	}
	else{

		if ( CObj.checked == true ) return 1;
		else return 0;
	}
}



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 공용 - 윈도우 팝업창 호출 / 리턴
 ckFlag : select, reflect, deselect
 CObj : checkbox object
-------------------------------------*/
function Pubwinopen_return( theURL, winName, Width, Height, left, top, scrollbars ){

	if ( !Width ) Width=500;
	if ( !Height ) Height=415;
	if ( !left ) left=200;
	if ( !top ) top=10;
	if ( scrollbars=='' ) scrollbars=0;
	features = "loaction=no, directories=no, Width="+Width+", Height="+Height+", left="+left+", top="+top+", scrollbars="+scrollbars;
	var win = window.open( theURL, winName, features );

	return win;
}



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 Cookie 생성
-------------------------------------*/
function setCookie( name, value, expires, path, domain, secure ){

	var curCookie = name + "=" + escape( value ) +
		( ( expires ) ? "; expires=" + expires.toGMTString() : "" ) +
		( ( path ) ? "; path=" + path : "" ) +
		( ( domain ) ? "; domain=" + domain : "" ) +
		( ( secure ) ? "; secure" : "" );

	document.cookie = curCookie;
}



/*-------------------------------------
 Cookie 제거
-------------------------------------*/
function clearCookie( name ){

    var today = new Date();
    var expire_date = new Date(today.getTime() - 60*60*24*1000);
    document.cookie = name + "= " + "; expires=" + expire_date.toGMTString();
}



/*-------------------------------------
 Cookie 체크
-------------------------------------*/
function getCookie( name ){

	var dc = document.cookie;

	var prefix = name + "="

	var begin = dc.indexOf("; " + prefix);

	if ( begin == -1 ){

		begin = dc.indexOf(prefix);
		if (begin != 0) return null;
	}
	else {
		begin += 2
	}

	var end = document.cookie.indexOf(";", begin);

	if (end == -1) end = dc.length;

	return unescape(dc.substring(begin + prefix.length, end));
}



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 프레임 로드
-------------------------------------*/
function frame_load(){

	if ( !getCookie( name='dPath' ) ){
		setCookie( name='dPath', value='/', expires='', path='/' );
	}

	var page_query = '?webftpid=' + webftpid;

	if ( dPath = getCookie( name='dPath' ) ) page_query += '&dPath=' + dPath;

	if ( window.top.global_frame.location.href == 'about:blank' || window.top.global_frame.location.pathname == curr_path + 'webftp_tree.php' ){
		window.top.global_frame.location.href = curr_path + 'webftp_tree.php' + page_query;
	}

	window.top.folder_frame.location.href = curr_path + 'webftp_list.php' + page_query;
}



/*-------------------------------------
 dPath 경로 정의
-------------------------------------*/
function dPathCookie( dPath ){

	setCookie( name='dPath', value=dPath, expires='', path='/' );

	window.top.frame_load();
	window.top.folder_frame.location.reload();
}



/*-------------------------------------
 현재 경로 상위 디렉토리 리턴
-------------------------------------*/
function get_nowhighDir(){

	var dirpath = getCookie( name='dPath' )

	return get_highDir( dirpath );
}



/*-------------------------------------
 상위 디렉토리 리턴
-------------------------------------*/
function get_highDir( dirpath ){

	var highdir = '';

	if ( dirpath != '/' ){

		tmp1 = dirpath.split( "/" );
		tmp2 = Array();

		for ( i = 0; i < ( tmp1.length - 2 ); i++ ){
			tmp2[i] = tmp1[i];
		}

		highdir = tmp2.join( "/" );

		if ( highdir != '/' ) highdir += '/';
	}
	else {
		highdir = dirpath;
	}


	return highdir;
}



/*-------------------------------------
 검색 프레임 호출
-------------------------------------*/
function frame_search(){

	if ( !getCookie( name='dPath' ) ){
		setCookie( name='dPath', value='/', expires='', path='/' );
	}

	var page_query = '?webftpid=' + webftpid;

	if ( dPath = getCookie( name='dPath' ) ) page_query += '&dPath=' + dPath;

	window.top.global_frame.location.href = curr_path + 'webftp_search.php' + page_query;
}



/*-------------------------------------
 폴더 프레임 호출
-------------------------------------*/
function frame_tree(){

	if ( !getCookie( name='dPath' ) ){
		setCookie( name='dPath', value='/', expires='', path='/' );
	}

	var page_query = '?webftpid=' + webftpid;

	if ( dPath = getCookie( name='dPath' ) ) page_query += '&dPath=' + dPath;

	window.top.global_frame.location.href = curr_path + 'webftp_tree.php' + page_query;
}



/*-------------------------------------
 정보 프레임 호출
-------------------------------------*/
function frame_info( file_root ){

	if ( file_root == '' ){
		alert( "정확한 파일정보가 아닙니다." );
	}
	else {
		window.top.global_frame.location.href = curr_path + 'webftp_info.php?webftpid=' + webftpid + '&file_root=' + file_root;
	}
}



/*-------------------------------------
 지정경로로 목록 프레임 호출
-------------------------------------*/
function frame_list_dpath( dPath ){

	if ( window.top.folder_frame ){

		setCookie( name='dPath', value=dPath, expires='', path='/' );

		var page_query = '?webftpid=' + webftpid + '&dPath=' + dPath;

		window.top.folder_frame.location.href = curr_path + 'webftp_list.php' + page_query;
	}
}



/*-------------------------------------
 url 주소 복사
-------------------------------------*/
function urlCopyact( urlObj ){

	if (window.clipboardData) {
		alert("웹주소(URL)를 카피하였습니다. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다~");
		window.clipboardData.setData("Text", urlObj.value);
	} else {
		temp = prompt("웹주소(URL)를 클립보드로 복사(Ctrl+C) 하시고. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다~", urlObj.value);
	}
}



/*-------------------------------------
 폴더생성
-------------------------------------*/
function mkdir(){

 	var win = Pubwinopen_return( curr_path + 'webftp_mkdir.php?webftpid=' + webftpid, 'win_mkdir', 500, 500, 10, 10, 0 );
	win.focus();
}



/*-------------------------------------
 파일 올리기
-------------------------------------*/
function file_upload(){

 	var win = Pubwinopen_return( curr_path + 'webftp_upload.php?webftpid=' + webftpid, 'win_upload', 700, 500, 10, 10, 1 );
	win.focus();
}



/*-------------------------------------
 파일 원본복사등록
-------------------------------------*/
function file_gdcopy(){

 	var win = Pubwinopen_return( curr_path + 'webftp_gdcopy.php?webftpid=' + webftpid, 'win_gdcopy', 700, 500, 10, 10, 1 );
	win.focus();
}



/*-------------------------------------
 폴더/파일 삭제
-------------------------------------*/
function file_delete(){

	if ( window.top.folder_frame.proc_Fin == null ){
		alert( "실행할 수 없는 페이지 입니다." );
	}
	else {
		window.top.folder_frame.proc_Fin();
	}
}



/*-------------------------------------
 폴더/파일 변경
-------------------------------------*/
function file_modity(){

	if ( window.top.folder_frame.proc_Mod == null ){
		alert( "실행할 수 없는 페이지 입니다." );
	}
	else {
		window.top.folder_frame.proc_Mod();
	}
}



/*-------------------------------------
 디렉토리 TREE Cookie 처리
-------------------------------------*/
function tree_cookie( path, formObj ) {

	var FObj = formObj.form;

	if ( formObj.value == 'Y' ){

		formObj.value = 'N';
		setCookie( name=path, value='N', expires='', path='/' );
	}
	else {

		formObj.value = 'Y';
		setCookie( name=path, value='Y', expires='', path='/' );
	}


	tree_display( FObj );
}



/*-------------------------------------
 디렉토리 Display
-------------------------------------*/
function tree_display( FObj, init ){

	var ie = ( document.getElementById && !document.all ? 0 : 1 );


	if ( init == 1 ) { // 현경로 뷰 셋팅

		var dirpath = getCookie( name='dPath' );

		var notEmpty = 1;

		var len = FObj.elements.length;



		while ( notEmpty ){

			for ( i = 0; i < len; i++ ){

				var fname = FObj.elements[i].name;

				if ( fname == dirpath ){

					var tname = FObj.elements[ dirpath ].value;

					FObj.elements[ tname ].value = 'Y';

					break;
				}
			}


			dirpath = get_highDir( dirpath );

			if ( dirpath == '' || dirpath == '/' ){
				notEmpty = 0;
			}
		}

	}


	{ // Display

		var len = FObj.elements.length;

		for ( i = 0; i < len; i++ ){

			var fname = FObj.elements[i].name;

			if ( fname.substr( 0, 4 )!= 'tree' ) continue;

			if ( FObj.elements[i].value == 'Y' ){

				if ( ie ){
					if ( document.all[ fname ][1].style.display == 'block' ) continue;
					document.all[ fname ][1].style.display = 'block';
					document.all[ ( fname + '_img' ) ].innerHTML = '<img src="' + curr_path + '../../img/webftp/tab_opened.gif">';
				}
				else {
					if ( document.getElementById( fname ).style.display == 'block' ) continue;
					document.getElementById( fname ).style.display = 'block';
					document.getElementById( ( fname + '_img' ) ).innerHTML = '<img src="' + curr_path + '../../img/webftp/tab_opened.gif">';
				}
			}
			else {

				if ( ie ){
					if ( document.all[ fname ][1].style.display == 'none' ) continue;
					document.all[ fname ][1].style.display = 'none';
					document.all[ ( fname + '_img' ) ].innerHTML = '<img src="' + curr_path + '../../img/webftp/tab_closed.gif">';
				}
				else {
					if ( document.getElementById( fname ).style.display == 'none' ) continue;
					document.getElementById( fname ).style.display = 'none';
					document.getElementById( ( fname + '_img' ) ).innerHTML = '<img src="' + curr_path + '../../img/webftp/tab_closed.gif">';
				}
			}
		}
	}
}



/*-------------------------------------------------------------------------------------------*/