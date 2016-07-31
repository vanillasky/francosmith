<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: WebFTP
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

include "../_header.popup.php";

?>

<div style="margin-bottom:10px;">

<div class="title title_top">WebFTP 이미지관리<span>내 쇼핑몰의 모든 이미지를 관리합니다</span></div>

<?
{ // WebFTP 메인
	$webftpid = 'default';
	include "../design/webftp/main.php";
}
?>

<script>table_design_load();</script>