/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: Javascript Function ����
@��������/������/������:

* function ���� ���� : 3 line
* ���κ�( ������� ) �鿩���� : 2 column
------------------------------------------------------------------------------*/



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 ���� - ���� �Է� üũ
-------------------------------------*/
function onlyNumber(){

	if ( ( event.keyCode == 13 ) || ( event.keyCode == 190 ) || ( event.keyCode >= 96 && event.keyCode <= 105 ) || ( event.keyCode == 110 ) || ( event.keyCode > 47 && event.keyCode < 58 ) || event.keyCode == 8 || event.keyCode == 16 || event.keyCode == 116 || event.keyCode == 18 || event.keyCode == 9 || ( event.keyCode >= 37 && event.keyCode <= 40 ) || event.keyCode == 46 );
	else event.returnValue = false;
}



/*-------------------------------------------------------------------------------------------*/



/*-------------------------------------
 ���� - üũ�ڽ� üũ
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
 ���� - üũ�ڽ� �Ѱ��̻� üũ����
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
 ���� - üũ�ڽ� üũ����
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
 ���� - ������ �˾�â ȣ�� / ����
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
 Cookie ����
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
 Cookie ����
-------------------------------------*/
function clearCookie( name ){

    var today = new Date();
    var expire_date = new Date(today.getTime() - 60*60*24*1000);
    document.cookie = name + "= " + "; expires=" + expire_date.toGMTString();
}



/*-------------------------------------
 Cookie üũ
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
 ������ �ε�
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
 dPath ��� ����
-------------------------------------*/
function dPathCookie( dPath ){

	setCookie( name='dPath', value=dPath, expires='', path='/' );

	window.top.frame_load();
	window.top.folder_frame.location.reload();
}



/*-------------------------------------
 ���� ��� ���� ���丮 ����
-------------------------------------*/
function get_nowhighDir(){

	var dirpath = getCookie( name='dPath' )

	return get_highDir( dirpath );
}



/*-------------------------------------
 ���� ���丮 ����
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
 �˻� ������ ȣ��
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
 ���� ������ ȣ��
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
 ���� ������ ȣ��
-------------------------------------*/
function frame_info( file_root ){

	if ( file_root == '' ){
		alert( "��Ȯ�� ���������� �ƴմϴ�." );
	}
	else {
		window.top.global_frame.location.href = curr_path + 'webftp_info.php?webftpid=' + webftpid + '&file_root=' + file_root;
	}
}



/*-------------------------------------
 ������η� ��� ������ ȣ��
-------------------------------------*/
function frame_list_dpath( dPath ){

	if ( window.top.folder_frame ){

		setCookie( name='dPath', value=dPath, expires='', path='/' );

		var page_query = '?webftpid=' + webftpid + '&dPath=' + dPath;

		window.top.folder_frame.location.href = curr_path + 'webftp_list.php' + page_query;
	}
}



/*-------------------------------------
 url �ּ� ����
-------------------------------------*/
function urlCopyact( urlObj ){

	if (window.clipboardData) {
		alert("���ּ�(URL)�� ī���Ͽ����ϴ�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�~");
		window.clipboardData.setData("Text", urlObj.value);
	} else {
		temp = prompt("���ּ�(URL)�� Ŭ������� ����(Ctrl+C) �Ͻð�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�~", urlObj.value);
	}
}



/*-------------------------------------
 ��������
-------------------------------------*/
function mkdir(){

 	var win = Pubwinopen_return( curr_path + 'webftp_mkdir.php?webftpid=' + webftpid, 'win_mkdir', 500, 500, 10, 10, 0 );
	win.focus();
}



/*-------------------------------------
 ���� �ø���
-------------------------------------*/
function file_upload(){

 	var win = Pubwinopen_return( curr_path + 'webftp_upload.php?webftpid=' + webftpid, 'win_upload', 700, 500, 10, 10, 1 );
	win.focus();
}



/*-------------------------------------
 ���� ����������
-------------------------------------*/
function file_gdcopy(){

 	var win = Pubwinopen_return( curr_path + 'webftp_gdcopy.php?webftpid=' + webftpid, 'win_gdcopy', 700, 500, 10, 10, 1 );
	win.focus();
}



/*-------------------------------------
 ����/���� ����
-------------------------------------*/
function file_delete(){

	if ( window.top.folder_frame.proc_Fin == null ){
		alert( "������ �� ���� ������ �Դϴ�." );
	}
	else {
		window.top.folder_frame.proc_Fin();
	}
}



/*-------------------------------------
 ����/���� ����
-------------------------------------*/
function file_modity(){

	if ( window.top.folder_frame.proc_Mod == null ){
		alert( "������ �� ���� ������ �Դϴ�." );
	}
	else {
		window.top.folder_frame.proc_Mod();
	}
}



/*-------------------------------------
 ���丮 TREE Cookie ó��
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
 ���丮 Display
-------------------------------------*/
function tree_display( FObj, init ){

	var ie = ( document.getElementById && !document.all ? 0 : 1 );


	if ( init == 1 ) { // ����� �� ����

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